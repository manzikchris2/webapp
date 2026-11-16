<?php
set_exception_handler("ErrorHandler::handle_exception");
class Partner extends Login{
    private string $id;
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
            $stmt = $this->conn->prepare("SELECT p.Image ,u.Email,u.Tel,u.pass,u.user_id,u.status FROM Users as u 
                                          join Partners as p on p.ID = u.user_id 
                                        WHERE u.Email=:email and attribute = 'p'");
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch(pdo::FETCH_ASSOC);
            if (!isset($row['Email'])) {
                return json_encode(["error" => "user not found"]);
            } else {
                $this->id=$row['user_id'];
                if (password_verify($pass, $row['pass'])) {
                    if($row['status']==='BLOCKED'){
                        return $this->check_attempts($this->id);
                    }else{
                        $this->restart_attempts($this->id);
                    }
                   
                    $this->create_session();
                    $_SESSION['PartnersID'] = $row['user_id'] ?? null;
                    $_SESSION['email'] = $row['Email'];
                    $_SESSION['loggedin'] = true;
                    $_SESSION['login-time'] = time();
                    $_SESSION['on'] = "partner";

                  
                    return json_encode(["sucess" => true, "page" => "partner_home", 'img' => $row['Image']]);
                } else {
                    return $this->check_attempts($this->id);
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