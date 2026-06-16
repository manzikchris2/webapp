<?php
require __DIR__ . '/Email.php';
class Login
{
    protected PDO $conn;
    public function __construct(Database $db)
    {
        $this->conn = $db->get_connection();
    }

   /* public function sign_in(?string $email, string $pass, string $demand): string
    {
        switch ($demand) {
           /* case 'customer':
                
                break;
            case 'deliver':
                return false;
                break;
            case 'partner':
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                return $this->check_partner($email, $pass);
            default:
                return json_encode(["message" => "something went wrong with demands"]);
        }
    }*/
    protected function create_session()
    {
        $ses_id =  '';
        
        if(isset($_COOKIE['ses_id']) && !empty($_COOKIE['ses_id'])){
            $ses_id =  $_COOKIE['ses_id'];
        }else{
            $ses_id =  uniqid('s-');
            setcookie('c_session_id',$ses_id , [
                        'expires' => time() + 86400,
                        'domain' => 'localhost',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
        }
        session_id($ses_id);
        session_start();
    }
   
    public function otp_management(array $data, string $attr)
    {

        if (isset($data['resend'])) {
            $email = new Email($_SESSION['user_email']);
            if ($email->sendotp($this->conn, $attr)) {
                return json_encode(['success' => false, 'message' => 'otp sent']);
            } else {
                return json_encode(['success' => false, 'message' => 'something went wrong', $_SESSION]);
            }
        }
        if (!isset($data['otp']) || empty($data['otp'])) {
            return json_encode(['success' => false, 'message' => 'how did you manage']);
        }

        $stmt = $this->conn->prepare('SELECT otp, ot_time FROM Users WHERE Email = :email and attribute = :attr');
        $stmt->execute([':email' => $_SESSION['user_email'], ':attr' => $attr]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_time = time();
        $deficite = $current_time - $row['ot_time'];
        if ($deficite > 60) {
            return json_encode(['success' => false, 'message' => ' this otp expired']);
        } else if ($data['otp'] == ! $row['otp']) {
            return json_encode(['success' => false, 'message' => ' otp invalid ']);
        } else {
            $pages = ['c' => '/home', 'p' => '/patrner/home', 'd' => '/deliver/murugo'];
            $_SESSION['loggedin'] = true;
            return json_encode(['success' => true, 'page' => $pages[$attr]]);
        }
    }
    
    
    public function change_pass(string $pass, string $attr)
    {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('UPDATE Users set pass = :pass where user_id=:user and attribute = :attr');
        $stmt->execute(['pass' => $hash, 'user' => $_SESSION['customer_id'], 'attr' => $attr]);
        return json_encode(['success' => true, $_SESSION]);
    }
   
   
    
  
}


