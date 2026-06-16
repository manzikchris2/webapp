<?php

class Partner extends Login{
    public function __construct(Database $db){
        parent::__construct($db);
    }
    public function sign_in(string $email, string $pass){
         $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                return $this->check_partner($email, $pass);
    }
      private function check_partner(string $email, string $pass)
    {
        try {
            $stmt = $this->conn->prepare("SELECT p.Image ,u.Email,u.Tel,u.pass,u.user_id FROM Users as u 
                                          join Partners as p on p.ID = u.user_id 
                                        WHERE u.Email=:email and attribute = 'p'");
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch(pdo::FETCH_ASSOC);
            if (!isset($row['Email'])) {
                return json_encode(["error" => "user not found"]);
            } else {
                if (password_verify($pass, $row['pass'])) {
                    $stmt = $this->conn->prepare("SELECT * from User_Tokens WHERE user_id = :id");
                    $param = ['id' => $row['user_id']];
                    $stmt->execute($param);
                    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$row2) {
                        return json_encode(['mess' => $row2, 'id' => $row['user_id']]);
                    }

                    $this->create_session();
                    $_SESSION['PartnersID'] = $row['user_id'] ?? null;
                    $_SESSION['email'] = $row['Email'];
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
                    return json_encode(["sucess" => true, "page" => "partner_home", 'img' => $row['Image']]);
                } else {
                    return json_encode(["error" => "WRONG PASSWORD"]);
                }
            }
        } catch (exception $th) {
            return json_encode(["file" => $th->getFile(), "mess" => $th->getMessage()]);
        }
    }
     public function profile_partner()
    {
        //if(!$_SESSION['loggedin']){}
        $stmt = $this->conn->prepare('SELECT p.Name as nome ,p.Bname as cognome,u.Email,u.Tel FROM `Partners` as p JOIN Partners_users as u ON p.ID=u.PartnersID WHERE p.ID=:user');
        $stmt->execute([':user' => $_SESSION['PartnersID']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $html = '<form id="profile_form" class="container">
                               <label for="customer_fname">Nome</label>
                               <input id="customer_fname" type="text" class="profile_inputs" value="' . $row['nome'] . '"/>
                               <label for="customer_lname">Business name</label>
                               <input id="customer_lname" type="text" class="profile_inputs" value="' . $row['cognome'] . '" />
                               <label for="customer_email">Email</label>
                                <input id="customer_email" type="email" class="profile_inputs" value="' . $row['Email'] . '"/>
                                <label for="customer_tel">Tel</label>
                                <input id="customer_tel" type="tel" class="profile_inputs" value="' . $row['nome'] . '"/>
                                <label for="customer_pass">pass</label>
                                <input id="customer_pass" type="text" class="profile_inputs"/>
                                <button type="submit" id="profile_submit" onclick="update_profile()">update</button>
                            </form>
                            <p id="profile_err"class="err" style="display:none"></p>';
        return json_encode(['success' => true, 'content' => $html]);
    }
}