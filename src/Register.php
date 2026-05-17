<?php
class Register
{
    private PDO $conn;
    private int $id;
    public function __construct(Database $db)
    {
        $this->conn = $db->get_connection();
    }
    public function register(string $demand, ?string $name, ?string $surname, ?string $address, string $email, ?string $tel, string $pass): string
    {
        switch ($demand) {
            case 'customer':
                $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $surname = filter_var($surname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                $tel = filter_var($tel, FILTER_SANITIZE_NUMBER_INT);
                return $this->register_user($name, $surname, $address, $email, $tel, $pass);
                break;
            case 'partner':
                $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $b_name = filter_var($surname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                $tel = filter_var($tel, FILTER_SANITIZE_NUMBER_INT);
                $adress = filter_var($address, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                return $this->register_partner($name, $b_name, $adress, $email, $tel, $pass);
                break;
            default:
                throw new Exception("something went wrong");
        }
    }
    public function check_mail(string $email, string $attr): bool
    {
        $stmt = $this->conn->prepare("SELECT `email` FROM `Users` WHERE `email`= :email and 'attribute'=:attr");
        $stmt->execute(["email" => $email,'attr'=>$attr]);
        $num = $stmt->rowCount();
        if ($num > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function update_user(array $data)
    {
        $this->conn->beginTransaction();
        try {
            $query = "UPDATE Customers as c join Users as u on c.ID=u.customerID SET ";
            $set = [];
            $param = [];
            if (isset($data['name']) && !empty($data['name'])) {
                $set[] = "c.Fname = :name";
                $param['name'] = $data['name'];
            }
            if (isset($data['surname']) && !empty($data['surname'])) {
                $set[] = "c.Lname = :surname";
                $param['surname'] = $data['surname'];
            }
            if (isset($data['email']) && !empty($data['email'])) {
                $set[] = "u.Email = :email";
                $param['email'] = $data['email'];
            }
            if (isset($data['tel']) && !empty($data['tel'])) {
                $set[] = "u.Tel = :tel";
                $param['tel'] = $data['tel'];
            }
            if (empty($set)) {
                return json_encode(['success' => false, 'message' => "info required"]);
            }
            $query .= implode(", ", $set);
            $query .= " WHERE c.ID = :user";
            $param['user'] = $_SESSION['customer_id'];
            $stmt = $this->conn->prepare($query);
            $stmt->execute($param);
            $this->conn->commit();
            return json_encode(['success' => true]);
        } catch (Exception $th) {
            $this->conn->rollBack();
            return json_encode(["sucess" => false, "message" => $th->getMessage(), 'query' => $query, 'param' => $param]);
        }
    }
    public function update_partner(array $data)
    {
        $this->conn->beginTransaction();
        try {
            $query = "UPDATE Partners as c join Partners_users as u on c.ID=u.PartnersID SET ";
            $set = [];
            $param = [];
            if (isset($data['name']) && !empty($data['name'])) {
                $set[] = "c.Name = :name";
                $param['name'] = $data['name'];
            }
            if (isset($data['surname']) && !empty($data['surname'])) {
                $set[] = "c.Bname = :surname";
                $param['surname'] = $data['surname'];
            }
            if (isset($data['email']) && !empty($data['email'])) {
                $set[] = "u.Email = :email";
                $param['email'] = $data['email'];
            }
            if (isset($data['tel']) && !empty($data['tel'])) {
                $set[] = "u.Tel = :tel";
                $param['tel'] = $data['tel'];
            }
            if (empty($set)) {
                return json_encode(['success' => false, 'message' => "info required"]);
            }
            $query .= implode(", ", $set);
            $query .= " WHERE c.ID = :user";
            $param['user'] = $_SESSION['PartnersID'];
            $stmt = $this->conn->prepare($query);
            $stmt->execute($param);
            $this->conn->commit();
            return json_encode(['success' => true, $param]);
        } catch (Exception $th) {
            $this->conn->rollBack();
            return json_encode(["sucess" => false, "message" => $th->getMessage(), 'query' => $query, 'param' => $param]);
        }
    }
    public function register_deliver(array $person)
    {
        if (!isset($person['name']) || !isset($person['s-name']) || !isset($person['email']) || !isset($person['tel-number']) || !isset($person['password'])) {
            return json_encode(['success' => false, 'message' => 'please fill all the form']);
        } else {
            $name = filter_var($person['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $s_name = filter_var($person['s-name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_var($person['email'], FILTER_SANITIZE_EMAIL);
            $tel = filter_var($person['tel-number'], FILTER_SANITIZE_NUMBER_INT);
            $adress = filter_var($person['address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $pass = password_hash($person['password'], PASSWORD_DEFAULT);
            $check= $this->check_mail($email,'d');
            if($check){
                return json_encode(['sucess'=>false,'message'=>'email already in use']);
            }
            try{
                $this->conn->beginTransaction();
                 $stmt = $this->conn->prepare('INSERT INTO Delivery(Name,Surname,address) VALUES(:name,:sur,:address)');
            $stmt->execute(['name' => $name, 'sur' => $s_name, 'address' => $adress]);
            $id = $this->conn->lastInsertId();
            $stmt2 = $this->conn->prepare('INSERT INTO Users(user_id,Email,Tel,pass,attribute) values(:id,:email,:tel,:pass,"d")');
            $stmt2->execute(['id' => $id, 'email' => $email, 'tel' => $tel, 'pass' => $pass]);
            $token = substr(uniqid('T-'), 0, 8);
            $session = substr(uniqid('s-'), 0, 8);
            $stmt3 = $this->conn->prepare("INSERT into User_Tokens(token_id,user_id,session_id,time) values(:token,:user,:session_id,:time)");
            $stmt3->execute(['token' => $token, 'user' => $id, 'session_id' => $session, 'time' => time()]);
            $this->conn->commit();
            return json_encode(['success' => true]);
            }catch(Exception $th){
                $this->conn->rollBack();
                return json_encode(['success'=>false,'code'=>$th->getMessage(),'line'=>$th->getLine().' '.$th->getFile()]);
            }
           
        }
    }
    private function register_user(?string $name, ?string $surname, ?string $address, string $email, ?int $tel, string $pass)
    {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO Customers(fname,lname,adress) VALUES(:name,:lastname,:address)");
            $params = ['name' => $name, 'lastname' => $surname, 'address' => $address];
            $res = $stmt->execute($params);
            if ($res) {
                $this->id = $this->conn->lastinsertid();
                $stmt1 = $this->conn->prepare("INSERT INTO Users(Email,pass,Tel,customerID) VALUES(:email,:pass,:tel,:c_id)");
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $params1 = ['email' => $email, 'pass' => $hash, 'tel' => $tel, 'c_id' => $this->id];
                $stmt1->execute($params1);
                $token = substr(uniqid('T-'), 0, 8);
                $session = substr(uniqid('s-'), 0, 8);
                $stmt2 = $this->conn->prepare("INSERT into User_Tokens(token_id,user_id,session_id,time) values(:token,:user,:session_id,:time)");
                $stmt2->execute(['token' => $token, 'user' => $this->id, 'session_id' => $session, 'time' => time()]);
                $this->conn->commit();
                return json_encode(["success" => true, "message" => "registration successful"]);
            }
        } catch (Exception $th) {
            $this->conn->rollBack();
            return json_encode(["sucess" => false, "message" => $th->getMessage()]);
        }
    }
    private function register_partner(?string $name, string $b_name, ?string $address, string $email, ?int $tel, string $pass)
    {
        $this->conn->beginTransaction();
        $check = $this->check_mail($email,'p');
        if ($check) {
            return json_encode(["message" => "email already in use"]);
        } else {
            try {
                $stmt = $this->conn->prepare("INSERT INTO `Partners`(`ID`, `Name`, `Bname`, `Address`) VALUES (:id, :name, :bname, :adress)");
                $id = substr(uniqid("p_"), 0, 10);
                $res = $stmt->execute(['id' => $id, 'name' => $name, 'bname' => $b_name, 'adress' => $address]);
                if ($res) {
                    $stmt2 = $this->conn->prepare("INSERT INTO `Partners_users`(`pass`,`email`,`Tel`,`PartnersID`) VALUES (:pass,:email,:tel,:id)");
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $stmt2->execute(['pass' => $hash, 'email' => $email, 'tel' => $tel, 'id' => $id]);
                    $token = substr(uniqid('T-'), 0, 8);
                    $session = substr(uniqid('s-'), 0, 8);
                    $stmt2 = $this->conn->prepare("INSERT into User_Tokens(token_id,user_id,session_id,time) values(:token,:user,:session_id,:time)");
                    $stmt2->execute(['token' => $token, 'user' => $id, 'session_id' => $session, 'time' => time()]);
                    $this->conn->commit();
                    return json_encode(["message" => "suceesfully"]);
                }
            } catch (Exception $th) {
                $this->conn->rollBack();
                return json_encode(["error" => $th->getMessage(), "line" => $th->getLine(), "check" => $check]);
            }
        }
    }
    public function address(string $address)
    {
        $address = filter_var($address, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare('INSERT INTO Address(user_id,address) VALUES(:id,:address)');
            $stmt->execute([':id' => $_SESSION['customer_id'], ':address' => $address]);
            $this->conn->commit();
            return json_encode(['success' => true]);
        } catch (exception $th) {
            $this->conn->rollBack();
            return json_encode([
                'sucess' => false,
                'reason' => $th->getMessage(),
                'line' => $th->getFile() . " " . $th->getline(),
                'message' => 'something fucked up'
            ]);
        }
    }

    public function change_pass(string $pass, string $email)
    {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare('UPDATE Users set pass=:pass where Email = :email');
            $stmt->execute(['pass' => $hashed, 'email' => $email]);
            $this->conn->commit();
            return json_encode(['success' => true]);
        } catch (Exception $th) {
            $this->conn->rollBack();
            return json_encode([

                'success' => false,
                'reason' => $th->getMessage(),
                'line' => $th->getFile() . " " . $th->getline(),
                'message' => 'something fucked up'
            ]);
        }
    }
}
