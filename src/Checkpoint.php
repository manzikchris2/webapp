<?php
require "RateLimit.php";
require "Database.php";
set_exception_handler("ErrorHandler::handle_exception");
class Checkpoint
{
    public static function check($type = null)

    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
              return ['success' => false, 'head' => '/welcome', header('Location:/welcome')];
          }
       
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
            return json_encode(['success' => false, 'head' => '/welcome',header('Location:/welcome')]);
        }
        if (!isset($_SESSION['customer_id']) && !isset($_SESSION['PartnersID']) && !isset($_SESSION['deliver_id'])) {
            return json_encode(['success' => false, 'head' => '/welcome']);
        }
        if ($type === 'customer' && !isset($_SESSION['customer_id'])) {
            return json_encode(['success' => false, 'head' => '/welcome']);
        }
        if ($type === 'partner' && !isset($_SESSION['PartnersID'])) {
            return json_encode(['success' => false, 'head' => '/welcome']);
        }
        if ($type === 'deliver' && !isset($_SESSION['deliver_id'])) {
            return json_encode(['success' => false, 'head' => '/welcome']);
        }
        if(!isset($_SESSION['login-time'])){
             return json_encode(['success' => false, 'head' => '/welcome']);
        }
        $db=new Database();
        $conn = $db->get_connection();
        $rate = new RateLimit($conn);
        $rate->checkLimit();
        $time_dif = time() - $_SESSION['login-time'];
        if($time_dif > 900){
            return json_encode(['success' => false, 'head' => '/welcome', readfile(__DIR__ . '/../page/home.html')]);
        }else{
            $_SESSION['login-time'] = time();
        }
        return true;
    }
    public static function rider()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
              return ['success' => false, 'head' => '/deliver'];
          }
        
        if(isset($_SESSION['order']) && isset($_SESSION['order']['active']) && $_SESSION['order']['active'] === true){
            return json_encode([
                'success' => false,
                'message' => 'You already have an order in process'
            ]);
        }
        
    
        else {
            return true;
        }
    }
   
    public static function logout(string $attr)
    {
        try {
            // Delete all cookies
            foreach ($_COOKIE as $name => $value) {
                setcookie($name, '', time() - 3600, '/');
                setcookie($name, '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
            }

            // Clear session
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_unset();
                session_destroy();
            }

            // Clear the cookie array
            $_COOKIE = [];

            $pages = ['c' => "welcome", 'p' => 'partner', 'd' => 'deliver'];

            return json_encode([
                "success" => true,
                "redirect" => $pages[$attr],
                "message" => "Logged out successfully"
            ]);
        } catch (Exception $e) {
            return json_encode([
                "success" => false
            ]);
        }
    }
}
