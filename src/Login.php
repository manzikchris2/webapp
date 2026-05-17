<?php
require __DIR__.'/Email.php';
class Login
{
    private PDO $conn;
    public function __construct(Database $db)
    {
        $this->conn = $db->get_connection();
    }

    public function sign_in(?string $email, string $pass, string $demand): string
    {
        switch ($demand) {
            case 'customer':
                if (strpos($email, '@') !== false) {
                    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                    return $this->check_user($email, $pass);
                } else if (is_numeric($email)) {
                    $email = filter_var($email, FILTER_SANITIZE_NUMBER_INT);
                    return $this->check_user($email, $pass);
                } else {
                    return json_encode(["success" => false, "message" => "invalid email or tel"]);
                }
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
    }
    private function create_session(array $data)
    {
        //session_regenerate_id(true);
        session_id($data['session_id']);
        session_start();
    }
    private function check_user(string $email, string $pass)
    {
        try {
            $this->conn->beginTransaction();
            if (is_numeric($email)) {
                $stmt = $this->conn->prepare("SELECT email, tel, pass,customerID from Users  WHERE tel = :tel");
                $stmt->execute(['tel' => $email]);
            } else {
                $stmt = $this->conn->prepare("SELECT email, tel, pass, user_id from Users  WHERE email = :email and attribute = 'c' " );
                $stmt->execute(['email' => $email]);
            }
            $row = $stmt->fetch(pdo::FETCH_ASSOC);
            if ($row === false) {
                throw new Exception("user not found");
            } else {
                if (password_verify($pass, $row['pass'])) {
                    $stmt = $this->conn->prepare("SELECT * FROM User_Tokens WHERE user_id=:user");
                    $param = ['user' => $row['user_id']];
                    $stmt->execute($param);
                    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$row2) {
                        return json_encode('empty');
                    }
                    $this->create_session($row2);
                    $_SESSION['customer_id'] = $row['user_id'];
                    $_SESSION['user_email'] = $row['email'] ?? null;
                    $_SESSION['loggedin'] = false;
                    $_SESSION['login-time'] = time();
                    $_SESSION['on'] = "custmer";
                    $stmt3 = $this->conn->prepare('SELECT Adress from Customers where ID = :id');
                    $stmt3->execute(['id' => $row['user_id']]);
                    $adress = $stmt3->fetch(PDO::FETCH_ASSOC);
                    setcookie('c_session_id', $row2['session_id'], [
                        'expires' => time() + 86400,
                        'domain' => 'localhost',
                        'secure' => false,
                        'httponly' => false,
                        'samesite' => 'Lax'
                    ]);
                    setcookie('c_address', $adress['Adress'], [
                        'expires' => time() + 86400,
                        'domain' => 'localhost',
                        'secure' => false,
                        'httponly' => false,
                        'samesite' => 'Lax'
                    ]);


                    $this->conn->commit();

                    $page = '<form class="otp_check">
                            <h2 class="otp_head">Enter OTP</h2>
                            <p class="otp_p" id="otp_mess">an otp was sent to your email</p>
                            <div id="otp_inp_div">
                            <input id="otp_inp1" class="otp_inp" maxlength="1" autodocus/>
                            <input id="otp_inp2" class="otp_inp" maxlength="1" />
                            <input id="otp_inp3" class="otp_inp" maxlength="1" />
                            <input id="otp_inp4" class="otp_inp" maxlength="1" />
                            <input id="otp_inp5" class="otp_inp" maxlength="1" />
                            <input id="otp_inp6" class="otp_inp" maxlength="1" />
                            </div>
                            <p class="otp_p">this otp will expile in aminute</p>
                            <span id="otp_verify" onclick="otp_management(0)"> send otp</span>
                            <p id="otp_message"></p>
                            <p class="otp_p">didn\'t recive otp code ? click below</p>
                            <span id="otp_resend" onclick="otp_management(1)"> resend otp </span>
                            </form>';
                    $email = new Email($_SESSION['user_email']);
                    $otp_sent = $email->sendotp($this->conn,'c');
                    if($otp_sent === true){
                        return json_encode(["success" => true, "page" => $page,'ses'=>$_SESSION]);
                    }
                    else{
                        return json_encode(['success'=>false, 'ses'=>$_SESSION]);
                    }

                    
                } else {
                    $this->conn->rollBack();
                    return json_encode(["success" => false, "message" => "wrong user or password"]);
                }
            }
        } catch (Exception $th) {
            $this->conn->rollBack();
            return json_encode(["sucess" => false, "message" => $th->getMessage(), "line" => $th->getLine(), "file" => $th->getFile()]);
        }
    }
    public function otp_management(array $data,string $attr){
        
        if(isset($data['resend'])){
            $email = new Email($_SESSION['user_email']);
           if($email->sendotp($this->conn,$attr)){
            return json_encode(['success'=>false,'message'=>'otp sent']);
           }else{
             return json_encode(['success'=>false,'message'=>'something went wrong',$_SESSION]);
           }
        }
        if(!isset($data['otp']) || empty($data['otp'])){
            return json_encode(['success'=>false,'message'=>'how did you manage']);
        }
        
        $stmt = $this->conn->prepare('SELECT otp, ot_time FROM Users WHERE Email = :email and attribute = :attr');
        $stmt->execute([':email' => $_SESSION['user_email'],':attr'=>$attr]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_time = time();
        $deficite = $current_time - $row['ot_time'];
        if($deficite > 60){
            return json_encode(['success'=>false,'message'=>' this otp expired']);
        }
        else if($data['otp'] ==! $row['otp']){
            return json_encode(['success'=>false,'message'=>' otp invalid ']);
        }
        else{
            $pages = ['c'=>'/home','p'=>'/patrner/home','d'=>'/deliver/murugo'];
            $_SESSION['loggedin']=true;
            return json_encode(['success'=>true,'page'=>$pages[$attr]]);
        }

    }
    public function profile_customer()
    {
        $stmt = $this->conn->prepare('SELECT c.Fname as nome ,c.Lname as cognome,u.Email,u.Tel FROM `Customers` as c JOIN Users as u ON c.ID=u.customerID WHERE c.ID=:user');
        $stmt->execute([':user' => $_SESSION['customer_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $html = '<form id="profile_form" class="container">
                               <label for="customer_fname">Nome</label>
                               <input id="customer_fname" type="text" class="profile_inputs" value="'.htmlspecialchars($row['nome']).'"/>
                               <label for="customer_lname">Cognome</label>
                               <input id="customer_lname" type="text" class="profile_inputs" value="'.htmlspecialchars($row['cognome']).'" />
                               <label for="customer_email">Email</label>
                                <input id="customer_email" type="email" class="profile_inputs" value="'.htmlspecialchars($row['Email']).'"/>
                                <label for="customer_tel">Tel</label>
                                <input id="customer_tel" type="tel" class="profile_inputs" value="'.htmlspecialchars($row['Tel']).'"/>
                                <label for="customer_pass">pass</label>
                                <input id="customer_pass" type="text" class="profile_inputs"/>
                                <button type="submit" id="profile_submit" onclick="update_profile()">update</button>
                            </form>
                            <p id="profile_err"class="err" style="display:none"></p>';
        return json_encode(['success' => true, 'content' => $html]);
    }
    public function profile_partner()
    {
        //if(!$_SESSION['loggedin']){}
        $stmt = $this->conn->prepare('SELECT p.Name as nome ,p.Bname as cognome,u.Email,u.Tel FROM `Partners` as p JOIN Partners_users as u ON p.ID=u.PartnersID WHERE p.ID=:user');
        $stmt->execute([':user' => $_SESSION['PartnersID']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $html = '<form id="profile_form" class="container">
                               <label for="customer_fname">Nome</label>
                               <input id="customer_fname" type="text" class="profile_inputs" value="'.$row['nome'].'"/>
                               <label for="customer_lname">Business name</label>
                               <input id="customer_lname" type="text" class="profile_inputs" value="'.$$row['cognome'].'" />
                               <label for="customer_email">Email</label>
                                <input id="customer_email" type="email" class="profile_inputs" value="'.$row['Email'].'"/>
                                <label for="customer_tel">Tel</label>
                                <input id="customer_tel" type="tel" class="profile_inputs" value="'.$row['nome'].'"/>
                                <label for="customer_pass">pass</label>
                                <input id="customer_pass" type="text" class="profile_inputs"/>
                                <button type="submit" id="profile_submit" onclick="update_profile()">update</button>
                            </form>
                            <p id="profile_err"class="err" style="display:none"></p>';
        return json_encode(['success' => true, 'content' => $html]);
    }
    public function check_deliver(array $data)
    {
        $stmt = $this->conn->prepare("SELECT pass,user_id  FROM Users WHERE email=:email and attribute = 'd'");
        $stmt->execute(['email' => $data['d-user']]);
        $row = $stmt->fetch(pdo::FETCH_ASSOC);
        if ($row == 'null') {
            return json_encode(['successs'=>false,'message'=>"user not found"]);
        } else {
            $check = password_verify($data['d-pass'],$row['pass']);
            if ($check) {
                $stmt2 = $this->conn->prepare('SELECT * From User_Tokens where user_id = :user');
                $stmt2->execute(['user'=>$row['user_id']]);
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                 $this->create_session($row2);
                 $_SESSION['deliver_id'] = $row['user_id'];
                    $_SESSION['user_email'] = $data['d-user'] ;
                    $_SESSION['loggedin'] = false;
                    $_SESSION['login-time'] = time();
                    $_SESSION['on'] = "deliver";
                    $this->orders_in_process($row['user_id']);
                $mail = new Email($data['d-user']);
                $mail->sendotp($this->conn,'d');
                $html ='<form class="otp_check">
                            <h2 class="otp_head">Enter OTP</h2>
                            <p class="otp_p" id="otp_mess">an otp was sent to your email</p>
                            <div id="otp_inp_div">
                            <input id="otp_inp1" class="otp_inp" maxlength="1" autodocus/>
                            <input id="otp_inp2" class="otp_inp" maxlength="1" />
                            <input id="otp_inp3" class="otp_inp" maxlength="1" />
                            <input id="otp_inp4" class="otp_inp" maxlength="1" />
                            <input id="otp_inp5" class="otp_inp" maxlength="1" />
                            <input id="otp_inp6" class="otp_inp" maxlength="1" />
                            </div>
                            <p class="otp_p">this otp will expile in aminute</p>
                            <span id="otp_verify" onclick="otp_management(0)"> send otp</span>
                            <p id="otp_message"></p>
                            <p class="otp_p">didn\'t recive otp code ? click below</p>
                            <span id="otp_resend" onclick="otp_management(1)"> resend otp </span>
                            </form>'; 
                return json_encode(['success'=>true,'content'=>$html]);
                
            } else {
                return json_encode(['success'=>false,'message'=>'wrong email or password']);
            }
        }
    }
    private function orders_in_process(string $deli): void{
        $stmt = $this->conn->prepare('SELECT ID from Orders Where deliveryID= :deli and stutus = "DELI"');
        $stmt ->execute([':deli'=>$deli]);
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            if(isset($row['ID'])){
                $_SESSION['order'] = ['order'=>$row['ID'],'active' => true];
            }
        }
    }
    private function check_partner(string $email, string $pass)
    {
        try {
            $stmt = $this->conn->prepare("SELECT  email, Tel, pass,PartnersID from Partners_users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch(pdo::FETCH_ASSOC);
            if (!isset($row['email'])) {
                return json_encode(["error" => "user not found"]);
            } else {
                if (password_verify($pass, $row['pass'])) {
                    $stmt = $this->conn->prepare("SELECT * from User_Tokens WHERE user_id = :id");
                    $param = ['id' => $row['PartnersID']];
                    $stmt->execute($param);
                    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$row2) {
                        return json_encode(['mess' => $row2, 'id' => $row['PartnersID']]);
                    }

                    $this->create_session($row2);
                    $_SESSION['PartnersID'] = $row['PartnersID'] ?? null;
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['loggedin'] = true;
                    $_SESSION['login-time'] = time();
                    $_SESSION['on'] = "partner";

                    setcookie('p_session_id', $row2['session_id'], [
                        'expires' => time() + 86400,
                        'domain' => 'localhost',
                        'secure' => false,
                        'httponly' => false,
                        'samesite' => 'Lax'
                    ]);
                    return json_encode(["sucess" => true, "page" => "partner_home"]);
                } else {
                    return json_encode(["error" => "WRONG PASSWORD"]);
                }
            }
        } catch (exception $th) {
            return json_encode(["file" => $th->getFile(), "mess" => $th->getMessage()]);
        }
    }
}
