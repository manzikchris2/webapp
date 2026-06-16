<?php

class Customer extends Login
{
    public function __consruct(Database $db)
    {
        parent::__construct($db);
    }
    public function sign_in(?string $email, string $pass): string
    {
        if (strpos($email, '@') !== false) {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            return $this->check_user($email, $pass);
        } else if (is_numeric($email)) {
            $email = filter_var($email, FILTER_SANITIZE_NUMBER_INT);
            return $this->check_user($email, $pass);
        } else {
            return json_encode(["success" => false, "message" => "invalid email or tel"]);
        }
    }

    private function check_user(string $email, string $pass)
    {
        try {
            $this->conn->beginTransaction();
            if (is_numeric($email)) {
                $stmt = $this->conn->prepare("SELECT email, tel, pass,customerID from Users  WHERE tel = :tel");
                $stmt->execute(['tel' => $email]);
            } else {
                $stmt = $this->conn->prepare("SELECT email, tel, pass, user_id from Users  WHERE email = :email and attribute = 'c' ");
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
                    $this->create_session();
                    $_SESSION['customer_id'] = $row['user_id'];
                    $_SESSION['user_email'] = $row['email'] ?? null;
                    $_SESSION['loggedin'] = false;
                    $_SESSION['login-time'] = time();
                    $stmt3 = $this->conn->prepare('SELECT Adress from Customers where ID = :id');
                    $stmt3->execute(['id' => $row['user_id']]);
                    $adress = $stmt3->fetch(PDO::FETCH_ASSOC);
                    
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
                            <input id="otp_inp1" class="otp_inp" maxlength="1" autofocus suggestion="off"/>
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
                    $otp_sent = $email->sendotp($this->conn, 'c');
                    if ($otp_sent === true) {
                        return json_encode(["success" => true, "page" => $page, 'ses' => $_SESSION]);
                    } else {
                        return json_encode(['success' => false, 'ses' => $_SESSION]);
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
    public function profile_customer()
    {
        $stmt = $this->conn->prepare('SELECT c.Fname as nome ,c.Lname as cognome,u.Email,u.Tel FROM `Customers` as c JOIN Users as u ON c.ID=u.user_id WHERE c.ID=:user and u.attribute="c"');
        $stmt->execute([':user' => $_SESSION['customer_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $html = '<form id="profile_form" class="container">
                               <label for="customer_fname">Nome</label>
                               <input id="customer_fname" type="text" class="profile_inputs" value="' . htmlspecialchars($row['nome']) . '"/>
                               <label for="customer_lname">Cognome</label>
                               <input id="customer_lname" type="text" class="profile_inputs" value="' . htmlspecialchars($row['cognome']) . '" />
                               <label for="customer_email">Email</label>
                                <input id="customer_email" type="email" class="profile_inputs" value="' . htmlspecialchars($row['Email']) . '"/>
                                <label for="customer_tel">Tel</label>
                                <input id="customer_tel" type="tel" class="profile_inputs" value="' . htmlspecialchars($row['Tel']) . '"/>
                                
                                
                                <button type="submit" id="profile_submit" onclick="update_profile()">update</button>
                            </form>
                            <span id="change_pass_span">change pass</span>
                                <div id="change_pass">
                                      <label for="customer-pass">pass</label>
                                      <input id="customer-pass" type="text" class="profile_inputs"/>
                                      <label for="customer-pass_conf">confirm  pass</label>
                                       <input id="customer-pass_conf" type="text" class="profile_inputs"/>
                                       <p id="customer-pass_err"></p>
                                       <span onclick="change_pass()">change</span>
                                </div>
                            <p id="profile_err"class="err" style="display:none"></p>';
        return json_encode(['success' => true, 'content' => $html]);
    }
}
