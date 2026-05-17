<?php
Class Categories {
    private PDO $conn;
    

    public function __construct(database $db){
        $this-> conn = $db->get_connection();
    }
     public function get_all_categories(){
        $stmt = $this->conn->query("SELECT * FROM Categories");
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['ID']] = $row['CategoryName'];
        }
        return $data;
    }
    public function get_all_partners(){
        $stmt = $this->conn->prepare("SELECT ID, Bname FROM Partners");
        $stmt->execute();
        $data = [];
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['ID']] = $row['Bname'];
        }
        return $data;
    }
}