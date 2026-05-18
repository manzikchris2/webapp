<?php
class Product
{
    private PDO $conn;
    public function __construct(private Database $db)
    {
        $this->conn = $db->get_connection();
    }
    public function get_all_products(): array
    {
        $stmt = $this->conn->query("SELECT * FROM products where stutus = 1");
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    public function get_product_by_category(string $id)
    {
        $data = [];
        $stmt = $this->conn->prepare("SELECT p.ID, p.Name,p.Price,p.image,pp.Bname,c.CategoryName as cname,c.Description as desci FROM Products as p JOIN Categories as c ON p.CategoryID = c.ID join Partners as pp on pp.ID = p.PartnersID WHERE c.ID = :id and p.stutus = 1");
        $stmt->execute(['id' => $id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $prods ='';
        foreach($data as $key => $value){
            $prods .= ' <div class="product-card" style="background-image:url('.'/page/image/'.htmlspecialchars($value['image']).'">
                         <h3>'.htmlspecialchars($value['Name']).'</h3> <p>2€</p> <p class="cat_item_b_name">By 
                         '.htmlspecialchars($value['Bname']).'</p>';
            $prods .= '<button onclick=add_to_cart("'.htmlspecialchars(urlencode($value['Bname'])).'","'.$value['ID'].'")><i class="fas fa-cart-plus "></i></button>
                                     </div>';
        }
         $html = '<section class="product_cat_box"><div class="search_index">
                            <h>'.htmlspecialchars($value['cname']).'</h>
                            <p>'.htmlspecialchars($value['desci']).'</p>
                          </div>'.$prods.'</section>';
         
        return json_encode(['success' => true, 'products' => $html]);
    }
    public function get_product_by_partner(string $partner_id)
    {
        $data = [];
        $stmt = $this->conn->prepare("SELECT p.ID, p.Name,p.Price,p.image,p.stutus,p.categoryID,pp.Bname FROM Products as p 
                                      join Partners as pp on p.PartnersID=pp.ID WHERE p.PartnersID = :partner_id ");
        $stmt->execute(['partner_id' => $partner_id]);

        while ($row = $stmt->fetch(pdo::FETCH_ASSOC)) {
            $data[] = $row;
        }
       
        $prods ='';
        foreach($data as $key => $value){
            $prods .= ' <div class="product-card" style="background-image:url('.'/page/image/'.htmlspecialchars($value['image']).'">
                         <h3>'.htmlspecialchars($value['Name']).'</h3> <p>2€</p> ';
            $prods .= '<button onclick="add_to_cart(\''.htmlspecialchars(urlencode($value['Bname'])).'\',\''.htmlspecialchars(urlencode($value['ID'])).'\')"><i class="fas fa-cart-plus "></i></button>
                                     </div>';
        }
         $html = '<section class="product_cat_box"><div class="search_index">
                            <h>'.htmlspecialchars($value['Bname']).'</h>
                            <p>this is what we talking about lokking smooth as eveer and the best u is livweeeee</p>
                          </div>'.$prods.'</section>';
       

        return json_encode(['success' => true, 'products' => $html]);
    }
    public function update(array $prod)
    {
        $this->conn->beginTransaction();
        try {

            $stmt = $this->conn->prepare('UPDATE Products set Name = :name,stutus=:stat,Price=:price,categoryID=:cat where ID = :id');
            $stmt->execute([
                'name' => $prod['name'],
                'id' => $prod['id'],
                'price' => $prod['price'],
                'stat' => $prod['stat'],
                'cat' => $prod['category']
            ]);
            $this->conn->commit();


            return json_encode(['success' => true]);
        } catch (exception $th) {
            return json_encode([
                "success" => false,
                "data" => $prod,
                "message" => $th->getMessage()
            ]);
        }
    }
    public function multi_by_partner(array $partners)
    {
        $products = [];
        try {
            $products = [];
            foreach ($partners as $key => $value) {
                $stmt = $this->conn->prepare("SELECT ID, Name, image, Price FROM Products WHERE PartnersID = :p_id and stutus = 1");
                $stmt->execute(["p_id" => $key]);
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                 
                    $products[$value][] = $row;
                }
            }
            $debug = [];
           
            $sections = '';
            foreach ($products as $partner => $product) {
                $div = "";
                foreach ($product as $key) {
                    $div .= '<div class="product-card" style="background-image:url(' . '/page/image/' . htmlspecialchars($key['image']) . ')"> ';
                    $div .= '<h3>'.htmlspecialchars($key['Name']).'</h3>';
                    $div .= '<p>'.htmlspecialchars($key['Price']).'€</p>';
                    $div .= '<button onclick=add_to_cart("'.htmlspecialchars(urlencode($partner)).'","'.htmlspecialchars($key['ID']).'")><i class="fas fa-shopping-cart"></i></button>';
                    $div .= '</div>';
                }
                $sections .= '<section id="'.htmlspecialchars($partner).'" class="product_slice_box">
                               <div class="pat_tittle"><h2>'.htmlspecialchars($partner).'</h2></div>
                                 <div class="product_slice">'.$div.' </div>
                            </section> '; 
                            
            }
            return json_encode(["success" => true, "products" => $sections, '$row' => $products]);
        } catch (Exception $th) {
            return json_encode(["success" => false, "data" => $products, "message" => $th->getMessage()]);
        }
    }
    public function best_seller()
    {
        $data = [];
        $stmt = $this->conn->prepare('SELECT pp.image,sum(o.quantity) as best,pp.Bname,pp.Address,pp.ID FROM Products as p JOIN Orderdetails as o on o.productID = p.ID JOIN Orders as oo on oo.ID = o.orderID JOIN Partners as pp on p.PartnersID = pp.ID GROUP by pp.image, pp.Bname, pp.Address, pp.ID ORDER by best desc LIMIT 7;');
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return json_encode(['success' => true, 'content' => $data]);
    }
    public function add_product(array $prod, $file)
    {
        $file_out = false;
        try {
            $this->conn->beginTransaction();
            $upload = new Upload();

            $prod_id = 'p_' . substr(uniqid(), 0, 10);
            if (!empty($prod['p-category']) && !empty($prod['p-name']) && !empty($prod['p-price'])) {
                if (isset($file)) {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $newname = $prod_id . "." . $extension;
                    $file_out = $upload->upload('upload', $file, $newname);
                    if ($file_out === true) {
                        $stmt = $this->conn->prepare("INSERT INTO `Products`(`ID`, `Name`, `Price`, `CategoryID`, `PartnersID`,`image`)
                                                VALUES (:prod_id,:p_name,:p_price,:p_cat,:p_id,:p_image)");
                        $params = [
                            'prod_id' => $prod_id,
                            'p_name' => $prod['p-name'],
                            'p_price' => $prod['p-price'],
                            'p_cat' => $prod['p-category'],
                            'p_id' => $_SESSION['PartnersID'],
                            'p_image' => $newname
                        ];
                        $stmt->execute($params);
                        $this->conn->commit();
                        return json_encode(['success' => true, 'message' => 'product uploaded']);
                    } else {
                        throw new exception("upload failed");
                    }
                } else {
                    throw new exception("image missind");
                }
            } else {
                throw new Exception("required parameters missing");
            }
        } catch (Exception $th) {
            $this->conn->rollback();
            return json_encode([
                "success" => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'data' => [$prod, $file_out, $_SESSION]
            ]);
        }
    }
}
