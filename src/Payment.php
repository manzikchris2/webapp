<?php
class Payment{
    private PDO $conn;
    public function __construct(database $db){
        $this->conn = $db->get_connection();
    }
    public function payment(array $data, string $action){
        switch($action){
            case 'pay':
                return $this->insert_payment($data);
                break;
            case 'retrive':
                return $this->retrive($data['customer'],$data['order']);
                break;
            default:
                return json_encode(['success'=>false,'page'=>'notfound']);
        }
    }
    private function retrive(string $customer,string $order_id){
        try{
            $data=[];
            $stmt=$this->conn->prepare('SELECT * FROM payments WHERE customerID=:customer');
            $stmt->execute(['customer'=>$customer]);
            while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                $data[$row['ID']]=['number'=>$row['c_number'],
                                   'cvs'=>$row['cvs'],
                                   'brand'=>$row['brand']];
            }
            $stmt2=$this->conn->prepare('SELECT SUM(p.price * oo.quantity) as total FROM Orders as o 
                                        join Orderdetails as oo on o.ID=oo.orderID 
                                        join Products as p ON p.ID=oo.productID 
                                        WHERE o.ID = :order_id');
            $stmt2->execute(["order_id"=> $order_id]);
            $row2=$stmt2->fetch(PDO::FETCH_ASSOC);
            
            return json_encode(['success'=>true,'cards'=>$data,'total'=>$row2['total']]);
            
        }
        catch(exception $th){
            return json_encode(['success'=>false,'page'=>$th->getFile(),
                                  'line'=>$th->getLine(),
                                  'code'=>$th->getMessage()]);
        }
    }
    
   private function insert_payment(array $card){
    try{
        $this->conn->beginTransaction();
        $pay_id = '';
        if(!isset($card['payement_id'])){
            $stmt = $this->conn->prepare('INSERT INTO payments(c_number,cvs,customerID,brand) VALUES(:card_num,:cvs,:customerID,:brand)');
            $stmt->execute(['card_num'=>$card['number'],'cvs'=>$card['cvs'],'customerID'=>$card['customerID'],'brand'=>$card['brand']]);
            $pay_id = $this->conn->lastInsertId();
        }
        else{
            $pay_id = $card['payement_id'];
        }
        $stmt1 = $this->conn->prepare("UPDATE Orders set paymentID = :payid,stutus = 'ACTIVE' where ID=:order ");
        $stmt1->execute(['payid'=>$pay_id,'order'=>$_COOKIE['order_id']]);
        $this->conn->commit();
        setcookie('order_id', '', time() - 3600, '/');
        return json_encode(['success'=>true]);

    }
    catch(exception $th){
            $this->conn->rollBack();
            return json_encode(['success'=>false,'page'=>$th->getFile(),
                                  'line'=>$th->getLine(),
                                  'code'=>$th->getMessage(),
                                  'card'=>$card]);
        }
    
   }
 
}