<?php
class RateLimit {
    private PDO $pdo;
    private int $maxRequests;
    private int $timeWindow;
    
    public function __construct(PDO $pdo, $maxRequests = 60, $timeWindow = 60) {
        $this->pdo = $pdo;
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        
        
    }
    
    
    public function checkLimit($identifier = null) {
        if (!$identifier) {
            $identifier = $_SERVER['REMOTE_ADDR'] . ':' . ($_SERVER['HTTP_USER_AGENT'] ?? '');
            $identifier = md5($identifier);
        }
        
        $now = date('Y-m-d H:i:s');
        $windowStart = date('Y-m-d H:i:s', strtotime("-{$this->timeWindow} seconds"));
        
        try {
            // Clean old records
            $this->cleanOldRecords($windowStart);
            
            // Get current count
            $stmt = $this->pdo->prepare("
                SELECT request_count, first_request 
                FROM rate_limits 
                WHERE identifier = ? AND first_request > ?
            ");
            $stmt->execute([$identifier, $windowStart]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$record) {
                // First request in window
                $stmt = $this->pdo->prepare("
                    INSERT INTO rate_limits (identifier, request_count, first_request, last_request)
                    VALUES (?, 1, ?, ?)
                ");
                $stmt->execute([$identifier, $now, $now]);
                
                return [
                    'allowed' => true,
                    'remaining' => $this->maxRequests - 1,
                    'reset_in' => $this->timeWindow
                ];
            }
            
            if ($record['request_count'] >= $this->maxRequests) {
                // Rate limit exceeded
                $resetTime = strtotime($record['first_request']) + $this->timeWindow;
                $waitTime = $resetTime - time();
                
                return [
                    'allowed' => false,
                    'message' => "Rate limit exceeded. Try again in {$waitTime} seconds.",
                    'retry_after' => $waitTime,
                    'remaining' => 0
                ];
            }
            
            // Increment counter
            $stmt = $this->pdo->prepare("
                UPDATE rate_limits 
                SET request_count = request_count + 1, last_request = ?
                WHERE identifier = ?
            ");
            $stmt->execute([$now, $identifier]);
            
            return [
                'allowed' => true,
                'remaining' => $this->maxRequests - ($record['request_count'] + 1),
                'reset_in' => $this->timeWindow
            ];
            
        } catch (PDOException $e) {
            error_log("Rate limiter error: " . $e->getMessage());
            return ['allowed' => true]; // Fail open
        }
    }
    
    private function cleanOldRecords($windowStart) {
        $stmt = $this->pdo->prepare("DELETE FROM rate_limits WHERE first_request < ?");
        $stmt->execute([$windowStart]);
    }
}