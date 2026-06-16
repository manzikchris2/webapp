<?php
Class Delivery extends Login{
    public function __constuct(Database $db){
        parent::__construct($db);
    }
     public function check_deliver(array $data)
    {
        //$user = filter_sanitize();
        $stmt = $this->conn->prepare("SELECT pass,user_id  FROM Users WHERE email=:email and attribute = 'd'");
        $stmt->execute(['email' => $data['d-user']]);
        $row = $stmt->fetch(pdo::FETCH_ASSOC);
        if ($row == 'null') {
            return json_encode(['successs' => false, 'message' => "user not found"]);
        } else {
            $check = password_verify($data['d-pass'], $row['pass']);
            if ($check) {
                $stmt2 = $this->conn->prepare('SELECT * From User_Tokens where user_id = :user');
                $stmt2->execute(['user' => $row['user_id']]);
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                $this->create_session();
                $_SESSION['deliver_id'] = $row['user_id'];
                $_SESSION['user_email'] = $data['d-user'];
                $_SESSION['loggedin'] = false;
                $_SESSION['login-time'] = time();
                $_SESSION['on'] = "deliver";
                $this->orders_in_process($row['user_id']);
                $mail = new Email($data['d-user']);
                $mail->sendotp($this->conn, 'd');
                $html = '<form class="otp_check">
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
                return json_encode(['success' => true, 'content' => $html]);
            } else {
                return json_encode(['success' => false, 'message' => 'wrong email or password']);
            }
        }
    }
    private function orders_in_process(string $deli): void
    {
        $stmt = $this->conn->prepare('SELECT ID from Orders Where deliveryID= :deli and stutus = "DELI"');
        $stmt->execute([':deli' => $deli]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($row['ID'])) {
                $_SESSION['order'] = ['order' => $row['ID'], 'active' => true];
            }
        }
    }
    public function profile_deliver()
    {
        $stmt = $this->conn->prepare('SELECT c.Name as nome ,c.Surname as cognome,u.Email,u.Tel FROM `Delivery` as c JOIN Users as u ON c.ID=u.user_id WHERE c.ID=:user and u.attribute="d"');
        $stmt->execute([':user' => $_SESSION['deliver_id']]);
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
        return json_encode(['success' => true, 'content' => $html, $row]);
    }

}