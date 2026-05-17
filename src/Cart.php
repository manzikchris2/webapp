<?php
class Cart{
    private $conn;
    private array $cart;
    public function __init(database $db){
        $this->conn = $db->get_connection();
    }
    private function getcart(int $customer_id){
        try{ 
        $stmt = $this -> conn -> prepare("SELECT from Orderdetails as oo join orders as o on oo.order_id = o.ID where status = 0 and where o.customerID = :customer_id ");
        $stmt-> execute(["customer_id" => $customer_id]);
        
        }
        catch(Exception $th) {
            return json_encode(['error' => $th->getMessage()]);
        }

    }
}