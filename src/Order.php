<?php

class Order{
    private PDO $conn;
    private  $order_id;
    public function __construct(Database $db){
         $this->conn = $db->get_connection();
    }
    public function order_management(array $product,string $demand){
        switch($demand){
            case 'assign':
                return $this->assign_order($product['product'],$product["partner"]);
                break;
            case 'active':
                return $this->active_orders();
                break;
            case 'retrive':
                return $this->get_orders($product['id']);
                break;
            case 'partner':
                return $this->get_order_partner($product['partner'],$product['customer']);
                break;
            case 'delete':
                return $this->delete_order($product['partner'],$product['customer']);
                break;
            case 'accept':
                return $this->accept_order($product['order'],$product['stat']);
                break;
            default:
                return json_encode(["success"=>false]);
                break;
        }
    }
    private function check($customer,$prod){
        $stmt = $this ->conn->prepare(" SELECT ID FROM Orders as o
                                       WHERE o.PartnersID = :product
                                       AND o.stutus = 'PENDING' 
                                       AND o.customerID =:customer ");
        $stmt->execute(["product"=>$prod,"customer"=>$customer]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            return $row;   
    }
    private function check_od(string $order,string $prod){
        $stmt = $this ->conn->prepare(" SELECT * FROM Orderdetails as o
                                       WHERE o.productID = :product
                                       and o.orderID = :order
                                       ");
        $stmt->execute(["product"=>$prod,'order'=>$order]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            return $row;   
    }

    private function get_orders(string $p_id){
        $cart2 = [];
        $cart = [];
        $stmt = $this->conn->prepare("SELECT p.image,p.Name,p.Price,O.quantity ,os.PartnersID,pa.Bname 
                                       FROM  Orderdetails as O
                                       join Orders as os on O.orderID = os.ID
                                       join Products as p on O.productID = p.ID
                                       join Partners as pa on os.PartnersID = pa.ID
                                       where os.stutus = 'PENDING'
                                       AND os.customerID = :customer ");
        $stmt->execute(["customer"=>$p_id]);
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(isset($cart2[$row['Bname']])){
                $cart2[$row['Bname']]['items_im'][] = $row['image'];
                $cart2[$row['Bname']]['item_c']++;
                $cart2[$row['Bname']]['item_total'] += $row['Price']*$row['quantity'];
            }
            else{
                $cart2[$row['Bname']] = ['name'=>$row['Bname'],
                                         'items_im'=>[$row['image']],
                                         'item_c'=> 1,
                                         'item_total'=> $row['Price'] * $row['quantity'],
                                         'item_id'=>$row['PartnersID']];
            }
            
         }
         $html = '';
         $div = '';
         foreach($cart2 as $key){

              $div .= '<div class="cart">';
              $div .= '<span class="bin-btn" onclick="remove_cart(\''.htmlspecialchars($key['item_id']).'\',\''.htmlspecialchars(urlencode($key['name'])).'\')">
                         <i class="fas fa-trash-alt"></i>
                         </span>`;';
                $div .= '<h2>'.htmlspecialchars($key['name']).'</h2>';
                $div .= '<div class="cart-img">';
                foreach($key['items_im'] as $img){
                     $div .= '<img src="/page/image/'.htmlspecialchars($img).'" alt="none">';
                }
                $div .= '</div>';
                $div .= '<div class="cart-p">';
                $div .= '<p>'.htmlspecialchars($key['item_total']).'€</p>';
                $div .= '<p>'.htmlspecialchars($key['item_c']).' articles</p>';
                $div .= '</div>';
                $div .= '<span class="go_cart_btn" onclick=go_to_cart("'.htmlspecialchars($key['item_id']).'","'.htmlspecialchars($key['name']).'",false)>Go Go Cart</span>';
                $div .= '</div>';
         }
       
        return json_encode(["success"=>true,"better"=> $div]);
       
    }
    private function get_order_partner(string $partner_id,string $catid){
        try{
            $data = [];
            $total = 0;
            $order_id = $this->check($catid,$partner_id);
            $stmt = $this->conn->prepare("SELECT oo.ID,o.quantity ,(p.Price * o.quantity) as price,p.image,p.Name FROM Orderdetails as o
                                      join Orders as oo on o.OrderID = oo.ID
                                      join Products as p on o.productID = p.ID
                                      where oo.PartnersID = :patid
                                      and oo.customerID = :catid
                                      and oo.stutus = 'PENDING'");
           $stmt->execute(["patid"=>$partner_id,"catid"=> $catid]);
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $total += $row['price'] ;
            $data[]=$row;
            
         }
         $html = '';
         $html = '<div id="main_cart" ><table id="cart-table">
                          <tbody>';
        foreach($data as $key => $prod){
            $div = '<tr>
                     <td><img src="/page/image/'.htmlspecialchars($prod['image']).'" alt="none"></td>
                     <td>'.htmlspecialchars($prod['Name']).'</td>
                     <td>
                          <button  class="cart_incriment" onclick=price_maunupuration("'.htmlspecialchars(urlencode($prod['Name'])).'",'.htmlspecialchars($order_id['ID']).',-1)>-</button>
                          <input id="'.htmlspecialchars(urlencode($prod['Name'])).'_inp" class="cart_quantity"value="'.htmlspecialchars($prod['quantity']).'">
                          <button id="${prod.Name}" class="cart_incriment" onclick=price_maunupuration("'.htmlspecialchars(urlencode($prod['Name'])).'","'.htmlspecialchars($order_id['ID']).'",1)>+</button></td>
                     <td>'.htmlspecialchars($prod['price']).' €</td>
                    </tr>';
            $html .= $div;
           
        }
         $html .= '</tbody><tfoot>
                      <tr>
                      <td colspan="2" >TOTAL</td>
                      <td colspan="2" >'.htmlspecialchars($total).' €</td>
                    </tr></tfoot>
                    </table>
                    <button class="pay-btn" onclick=payment('.htmlspecialchars($order_id['ID']).')>Pay</button> </div>';
        
         
            
        return json_encode(["success"=>true,"content"=>$html,"total"=>$total,'order'=>$order_id['ID']]);
        }
        catch(Exception $th){
            return json_encode(["success"=>false,
                                 "mess"=>$th->getMessage(),
                                "line"=>$th->getLine(),
                                "file"=>$th->getFile()]);
        }

    }
    private function assign_order(string $product_id, string $bname){
        try{
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("SELECT ID FROM Partners WHERE Bname = :bname");
            $stmt->execute(["bname"=>$bname]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
               $check = $this->check($_SESSION['customer_id'],$row['ID']);  
            
           
            if(!$check){
                 $stmt2 =$this->conn->prepare("INSERT INTO Orders (customerID,PartnersID) VALUES (:customer,:partner_id)");
                 $stmt2->execute(["customer"=>$_SESSION['customer_id'],"partner_id"=>$row['ID']]);
                 $order_id = $this->conn->lastInsertId();
                 $stmt3 = $this->conn->prepare("INSERT INTO Orderdetails(productID,orderID) VALUES(:prod,:order)");
                 $stmt3->execute(["prod"=>$product_id,"order"=>$order_id]);
                 $this->conn->commit();
                 return json_encode(["succeful"=> true, "order"=>true,'ses'=>$_SESSION,'t'=>$bname]);
             }
             else{
                $o_det = $this->check_od($check['ID'],$product_id);
                if(is_array($o_det)){
                    $stmt2=$this->conn->prepare('UPDATE Orderdetails set quantity = quantity+1 
                                                 WHERE orderID = :order 
                                                 AND productID = :product');
                }
                else{ 
                    $stmt2 = $this->conn->prepare("INSERT INTO `Orderdetails`
                                               ( `orderID`, `productID`)
                                                VALUES (:order,:product)  
                                               ");
                    }
                $stmt2->execute(['order'=>$check['ID'],"product"=>$product_id]);
                 $this->conn->commit();
                return json_encode(["sucess"=>true , "update"=> true,'check'=>$o_det,'order'=>$check['ID'],"product"=>$product_id]);
             }
           
        }
        catch(Exception $th){
            $this->conn->rollBack();
            return json_encode(["success"=> false,
                                 "message"=>$th->getMessage(),
                                 "line"=>$th->getLine(),
                                 "file"=>$th->getFile(),
                                 "prod"=>$product_id
                                 ]);
        }

    }

    private function accept_order(string $order,string $status){
        try{
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare('UPDATE  Orders set stutus = :stat where ID=:order');
            $stmt->execute(['stat'=>$status,'order'=>$order]);
            $this->conn->commit();
            return json_encode(['success'=>true]);

        }
        catch(Exception $th){
            $this->conn->rollBack();
            return json_encode(["success"=> false,
                                 "message"=>$th->getMessage(),
                                 "line"=>$th->getLine(),
                                 "file"=>$th->getFile(),
                                ]);
        }
        
    }

    private function active_orders(){
        try{
            $content  = []; 
             $stmt=$this->conn->prepare('SELECT o.ID,oo.quantity,p.Name,p.image,o.stutus FROM `Orders` as o 
                                     JOIN Orderdetails as oo on o.ID = oo.orderID 
                                     JOIN Products as p on p.ID = oo.productID 
                                     WHERE (o.stutus = "ACTIVE" or o.stutus = "ACCEPT") and o.PartnersID = :user');
            $stmt->execute(['user'=>$_SESSION['PartnersID']]);
            while($row= $stmt->fetch(PDO::FETCH_ASSOC)){
                if(!isset($content[$row['ID']])){
                    $content[$row['ID']]['products'][] =['name' =>$row['Name'],
                                                         'image'=>$row['image'],
                                                         'quantity'=>$row['quantity'],
                                                         
                                                    
                              ];
                    $content[$row['ID']]['status'][]= $row['stutus'];
                    

                }
                else{
                     $content[$row['ID']]['products'][] =['name' =>$row['Name'],
                                                         'image'=>$row['image'],
                                                         'quantity'=>$row['quantity']
                                                         ];
                     $content[$row['ID']]['status'][]= $row['stutus'];
                }
            
            }
            return json_encode(['success'=>true,'content'=>$content]);
            
         }
         catch(Exception $th){
             return json_encode(["success"=> false,
                                 "message"=>$th->getMessage(),
                                 "line"=>$th->getLine(),
                                 "file"=>$th->getFile(),
                                  ]);
         }
    }
    private function delete_order(string $partner_id,string $customer_id){
        try{
            $this->conn->beginTransaction();
            $stmt= $this->conn->prepare("DELETE FROM Orders WHERE customerID=:customer and PartnersID=:partner and stutus='PENDING'");
            $stmt->execute(['customer'=>$customer_id,'partner'=>$partner_id]);
            $this->conn->commit();
            return json_encode(['success'=>true]);
         }
        catch(Exception $th){
            $this->conn->rollBack();
            return json_encode(['success'=>false,
                                 'line'=>$th->getLine(),
                                 'file'=>$th->getFile(),
                                 'mes'=>$th->getMessage()]);
        }

    }
    public function order_accept(string $id){
        $stmt = $this->conn->prepare('UPDATE Orders set stutus = "DELI" , deliveryID = :d_id where ID = :order');
        $stmt->execute(['d_id'=>$_SESSION['deliver_id'],'order'=>$id]);
        $stmt1 = $this->conn->prepare('SELECT p.Bname,pp.image,pp.Name,u.Email FROM Orders as o 
                                      JOIN Partners as p on o.PartnersID=p.ID
                                      join Users as u on u.user_id = o.customerID 
                                      join Orderdetails as ok on ok.orderID = o.ID 
                                      join Products as pp on ok.productID = pp.ID 
                                      WHERE o.ID = :order');
        $stmt1->execute([':order'=>$id]);
        $email = '';
        $html ='<table style="background-color:black;color:white;"><tbody>';
        $data=[];
        while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
            $email = $row['Email'];
            $html .= '<tr style="border=23px solid white">
                           <td style="border:4px solid black;"><img src="/page/image/'.htmlspecialchars($row['image']).'"style="width: 70px;height: 70px"></td>
                           <td><h1>'.htmlspecialchars($row['Name']).'</h1></td>

                       </tr>     
                     ';

        }
        $html.='</tbody></table>'; 
         
        
        $mail = new Email($email);
        if($mail->sendaccept($html)){
            $_SESSION['order']=['order'=>$id,'active'=>true];
           return json_encode(['success'=>true,$email,$data]);
        }
        else{
            return json_encode(['success'=>false, 'message' => $_SESSION]);
        }

    }
    public function order_retrive(string $who){
        if($who === 'd'){
            return $this->ordersForDelivery();
        }
    }
    private function ordersForDelivery(){
        $data = [];
        $stmt = $this->conn->prepare('SELECT o.ID,p.Bname,p.Address as away,c.Adress as home,p.Image from Orders as o 
                                      JOIN Customers as c on c.ID=o.customerID
                                      JOIN Partners as p on p.ID = o.PartnersID 
                                      where stutus = "READY" limit 3');
        
        $stmt->execute();
        $html ='';
        $data=[];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;

            $html .= '<div id="box_'.htmlspecialchars($row['ID']).'" class="order_box" 
                     style="background-image:url(\'/page/image/partners/'.htmlspecialchars($row['Image']).'\')">
                     <h1>'.htmlspecialchars($row['Bname']).'</h1>
                     <div id="box_'.htmlspecialchars($row['ID']).'_div" class="inside_box">
                        <h2> FROM </h2>
                        <p>address'.htmlspecialchars($row['away']).'</p>
                        <h2> DESTINATION </h2>
                        <p>address '.htmlspecialchars($row['home']).'</p>
                        <span id="'.htmlspecialchars($row['ID']).'" class="order_span" > accept </span>
                        </div>
                        
                     </div>
                      ';
        }
        if(empty($data)){
            $html='<p class"message"> there is no order at this moment </p>';
        }
        $content = '<section class="order_sect">'.$html.'</section';
        return json_encode(['success'=>true,'content'=>$content]);
    }
    public function orderInDelivery(){
        if(empty($_SESSION['order'])){
            return json_encode(['success'=>false ,'message'=>'you have no delivery pic one please']);
        }
        $stmt=$this->conn->prepare('SELECT p.image,p.Name,od.quantity,pa.Bname,pa.Image as bimage, c.Adress as pickup,pa.Address as destination 
                                    FROM Orders as o JOIN Orderdetails as od on o.ID=od.orderID 
                                    join Products as p ON od.productID = p.ID 
                                    JOIN Partners as pa on pa.ID = p.PartnersID 
                                    join Customers as c on c.ID = o.customerID 
                                    WHERE o.deliveryID = :deli_id 
                                    and o.stutus = "DELI" 
                                    and o.ID = :order_id');
        $stmt->execute([':deli_id'=>$_SESSION['deliver_id'],':order_id'=>$_SESSION['order']['order']]);
        $data = [];
        $html = '';
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data['bname'] = $row['Bname'];
             $data['bimage'] = $row['bimage'];
             $data['away'] = $row['pickup'];
             $data['from'] = $row['destination'];
             $html .= '<tr>
                          <td> <img src="/page/image/'.htmlspecialchars($row['image']).'"></td>
                          <td>'.htmlspecialchars($row['Name']).'</td>
                          <td>'.htmlspecialchars($row['quantity']).'</td>
                      </tr>';

        }
         if(empty($data)){
            return json_encode(['sucess'=>false , 'message'=>'you have no orders','ses'=>$_SESSION,':deli_id'=>$_SESSION['deliver_id'],':order_id'=>$_SESSION['order']['order']]);
        }
        $content = '<div class="current_order" style="background-image: url(\'/page/image/partners/'.htmlspecialchars($data['bimage']).'\')">
                          <h1>'.htmlspecialchars($data['bname']).'</h1>
                          <table class="current_order_table">
                              <tbody>'.$html.'</tbody>
                              <tfoot>
                                 <tr>
                                  <td> from:'.htmlspecialchars($data['from']).' </td>
                                  <td> to:'.htmlspecialchars($data['away']).' </td>
                                 </tr>
                              </tfoot>
                          </table>
                          <span id="delivered_btn" onclick="delivered()"> delivered </span>
                   </div>';
       
            return json_encode(['success'=>true,'content'=>$content]);
        
    }
    public function deliverHistory(){
        $stmt = $this->conn->prepare('SELECT o.ID,p.Bname,p.Address as away,c.Adress as home,p.Image from Orders as o 
                                      JOIN Customers as c on c.ID=o.customerID
                                      JOIN Partners as p on p.ID = o.PartnersID 
                                      where stutus = "DONE" and o.deliveryID = :deli_id ');
        $stmt->execute([':deli_id'=>$_SESSION['deliver_id']]);
        $html='';
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $html .= '<div class="order_h_box" 
                     style="background-image:url(\'/page/image/partners/'.htmlspecialchars($row['Image']).'\')">
                     <h1>'.htmlspecialchars($row['Bname']).'</h1>
                        <p>address '.htmlspecialchars($row['home']).'</p>
                        
                     </div>';

        }
        $content = '<div class="history_box">'.$html.'</div>';
        return json_encode(['success'=>true,'content'=>$content]);
    }

    public function deliveryDone(){
        try{
            $stmt = $this->conn->prepare('UPDATE Orders set stutus = "DONE" 
                                        where ID=:order and deliveryID = :deli_id');
            $stmt->execute([':order'=>$_SESSION['order']['order'],':deli_id'=>$_SESSION['deliver_id']]);
            $_SESSION['order'] = [];
            return json_encode(['success'=>true]);
            

        }catch(Exception $th){
            
            return json_encode(['success'=>false,'issue'=>
            $th->getFile().' '.$th->getLine(),'mes'=>$th->getMessage()]);
        }
    }
    public function orderManupiration(string $order_id, int $quantity,string $p_name){
        try{
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare('UPDATE Orderdetails as o JOIN Products as p on p.ID=o.productID
                                           SET o.quantity = :value 
                                           where o.orderID = :order 
                                           and p.Name = :name ');
            $stmt->execute([':value'=>$quantity,
                             ':order'=>$order_id,
                             ':name'=>$p_name]);
            $this->conn->commit();
            return json_encode(['success'=>true,':value'=>$quantity,
                             ':order'=>$order_id,
                             ':name'=>$p_name]);

        }
        catch(Exception $th){
            $this->conn->rollBack();
            return json_encode(['success'=>false,
                                 'line'=>$th->getLine(),
                                 'file'=>$th->getFile(),
                                 'mes'=>$th->getMessage()]);
        }
    }
}

