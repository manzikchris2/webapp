<?php
class Search{
    private PDO $conn;
    public function __construct(Database $db){
        $this->conn = $db->get_connection();
    }
    public function search(string $key){
        try{
            $content = [];
            $content["partner"] = $this->bypartner($key);
            $content["product"] = $this->byproduct($key);
            if(empty($content["partner"]) && empty($content["product"])){
                return json_encode(['success'=>false]);
            }
            else{
                $div1 = '<div class="search_item_box">
                              <h2 class="search_title">Restorant</h2>';
                foreach($content['partner'] as $cont => $value){
                    $div1 .= '<span class="search-partner-item" onclick=get_prod("'.htmlspecialchars(urlencode($value['Bname'])).'",\'part\')>';
                    $div1 .= '<img src="/page/image/partners/'.htmlspecialchars($value['image']).'">';
                    $div1 .= '<h2 class="prod_name">'.htmlspecialchars($value['Bname']).'</h2>';
                    $div1 .= '<p></p>';
                    $div1 .= '</span>';
                }
                $div1 .='</div>';
                $div2 = '<div class="search_item_box">
                              <h2 class="search_title">product</h2>';
                foreach($content['product'] as $cont => $value){
                    $div2 .= '<span class="search-product-item" onclick=get_prod(\''.htmlspecialchars(urlencode($value['Name'])).'\',\'prod\')>';
                    $div2 .= '<img src="/page/image/'.htmlspecialchars($value['image']).'" alt="/image/mainp.jpg">';
                    $div2 .= '<h2 class="prod_name">'.htmlspecialchars($value['Name']).'</h2>';
                    $div2 .= '<h2 class="prod_b_name">From '.htmlspecialchars($value['Bname']).'</h2>';
                    $div2 .= '</span>';
                }
                $div2 .= '</div>';
                $html = $div1.$div2;
                /*if (contents.product && contents.product.length > 0) {
                let div2 = ``;
                contents.product.forEach((prod) => {
                    
                });
                div2 += `</div>`;
                div += div2; */ 

                return json_encode(["success"=>true,
                                   "content"=>$html,
                                   'con'=>$div1]);
                }

        }
        catch(Exception $th){
            return json_encode(["success"=> false,
                                "message"=>$th->getMessage(),
                                "line"=> $th->getLine(),
                                "file"=>$th->getFile()]);
        }
        }
    private function bypartner(string $key){
        $data = [];
        $search_key = "%".$key."%";
        $stmt = $this->conn->prepare("SELECT p.Bname, p.image, pu.Tel From Partners as p 
                                     join Partners_users as pu on p.ID = pu.PartnersID 
                                     WHERE p.Bname like :given 
                                     limit 5");
        $stmt->execute(["given"=>$search_key]);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
        return $data;

    }
    private function byproduct(string $key){
        $data = [];
        $search_key= "%".$key."%";
        $stmt = $this->conn->prepare("SELECT p.ID, p.Name,p.image,b.Bname From Products as p 
                                     join Partners as b on p.PartnersID = b.ID 
                                    WHERE p.Name like :given 
                                    limit 5");
        $stmt->execute(["given"=>$search_key]);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[]=$row; 
        }
        return $data;
    }
    public function searched(array $name){
        try{
            $data=[];
            $key = urldecode($name['name']);
            $html = '<section class="product_slice_box">';
            if($name['pref']==='prod'){
                $stmt=$this->conn->prepare("SELECT pr.ID,pr.image,pr.Name,pr.Price,p.Bname 
                                            from Products as pr join Partners as p on pr.PartnersId = p.ID
                                            where pr.Name = :name"); 
                 $stmt->execute(["name"=>$key]);
                 while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                    $data[]=$row;
                    }
                    $html = '<div class="search_index">
                            <h>Category</h>
                            <p>this is what we talking about lokking smooth as eveer and the best u is livweeeee</p>
                            </div>
                            <div class="saerch_content">';
                    foreach($data as  $cat){
                        $html .= '<div class="product-card-search" style="background-image:url(\'/page/image/'.htmlspecialchars($cat['image']).'\')">';
                        $html .=  ' <h2>'.htmlspecialchars($cat['Name']).'</h2>';
                        $html .=    '<p>'.htmlspecialchars($cat['Price']) .'€</p>';
                        $html .=     '<p>by '.htmlspecialchars($cat['Bname']).'</p>';
                        $html .=   '<button onclick="add_to_cart(\''.htmlspecialchars(urlencode($cat['Bname'])).'\',\''.htmlspecialchars($cat['ID']).'\')">Add To Cart</button>';
                        $html .=   '</div>';
                    }
                    $html.= '</div></section>';
                    

                    return json_encode(["success"=>true,"content"=>$html,"action"=>"product"]);
            }
            if($name['pref']==='part'){
                $stmt = $this->conn->prepare("SELECT pr.ID, pr.image,pr.Name,pr.Price,p.Bname 
                                            from Products as pr join Partners as p on pr.PartnersId = p.ID 
                                            where p.Bname = :name");
                $stmt->execute(["name"=>$key]);
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                    $data[]=$row;
                    }

                    $html .= '<div class="search_index">
                            <h>'.htmlspecialchars($data[0]['Bname']).'</h>
                            <p>this is what we talking about lokking smooth as eveer and the best u is livweeeee</p>
                          </div>
                          <div class="saerch_content">';
                    foreach($data as  $part ){
                         $html .= '<div class="product-card-search" style="background-image:url(\'/page/image/'.htmlspecialchars($part['image']).'\')">';
                        $html .=  ' <h2>'.htmlspecialchars($part['Name']).'</h2>';
                        $html .=    '<p>'.htmlspecialchars($part['Price']) .'€</p>';
                        $html .=   '<button onclick="add_to_cart(\''.htmlspecialchars(urlencode($part['Bname'])).'\',\''.htmlspecialchars($part['ID']).'\')">Add To Cart</button>';
                        $html .=   '</div>';
                    }
                    $html .= '</div></section>';

                return json_encode(["success"=>true,"content"=>$html,'partner'=>$data,"action"=>"partner"]);
            }
        }
        catch(EXCEPTION $th){
            return json_encode(["success"=>false,
                                 "message"=>$th->getMessage(),
                                 "line"=>$th->getLine(),
                                 "filr"=>$th->getFile(),
                                 "data"=>$name]);
        }
    }
    public function ready(){
        $stmt = $this->conn->prepare('SELECT o.ID,oo.quantity,p.Name,p.image,o.stutus FROM `Orders` as o 
                                     JOIN Orderdetails as oo on o.ID = oo.orderID 
                                     JOIN Products as p on p.ID = oo.productID 
                                     WHERE (o.stutus = "READY" or o.stutus = "DONE")  and o.PartnersID = :user');
                                    $stmt->execute(['user'=>$_SESSION['PartnersID']]);
        while($row= $stmt->fetch(PDO::FETCH_ASSOC)){
                if(!isset($content[$row['ID']])){
                    $content[$row['ID']]['products'][] =['name' =>$row['Name'],
                                                         'image'=>$row['image'],
                                                         'quantity'=>$row['quantity'],
                                                         
                                                    
                              ];
                    

                }
                else{
                     $content[$row['ID']]['products'][] =['name' =>$row['Name'],
                                                         'image'=>$row['image'],
                                                         'quantity'=>$row['quantity']
                                                         ];
                     
                }
            
            }
            return json_encode(['success'=>true,'content'=>$content]);
    }
}