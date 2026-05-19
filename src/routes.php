<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/functions.php";

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;



function route(string $method,string $path)
{
    spl_autoload_register(function ($class) {
        require __DIR__ . "/$class.php";
    });
    set_exception_handler("ErrorHandler::handle_exception");

    $router = new RouteCollector();
    $router->any('/', function () {
        header("location:/welcome");
    });
    $router->POST('/login', function () {
        try {
            $jdata = file_get_contents('php://input');
            $data = json_decode($jdata, true);
            if (!isset($data['user']) || !isset($data['pass'])) {
                throw new Exception("email and password are required");
            }
            $login = new Login(new Database());


            return $login->sign_in($data['user'], $data['pass'], "customer");
        } catch (Exception $e) {
            return json_encode(["error" => $e->getMessage()]);
        }
    });

    $router->post('/register/customer', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $register = new Register(new Database());
        try {
            if (!isset($data['c-f-email']) || !isset($data['c-f-pass']) || !isset($data['c-f-name'])) {
                throw new Exception("email  password and name are required");
            }
            $res = $register->register(
                'customer',
                $data['c-f-name'],
                $data['c-f-surname'] ?? null,
                $data['c-f-address'] ?? null,
                $data['c-f-email'],
                $data['c-f-phone'] ?? null,
                $data['c-f-pass']
            );
            return $res;
        } catch (Exception $e) {
            return json_encode(["success" => false, "message" => $e->getMessage(), "data" => $data]);
        }
    });

    $router->post('/register/partner', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $register = new Register(new Database());
        if (!isset($data['p-email']) || !isset($data['p-pass']) || !isset($data['p-r-name']) || !isset($data['p-tel']) ||  !isset($data['p-address'])) {
            return json_encode(["error" => "missing required please fill them in", "data" => $data]);
        } else {
            return $register->register("partner", $data['p-name'] . " " . $data['p-surname'], $data['p-r-name'], $data['p-address'], $data['p-email'], $data['p-tel'], $data['p-pass']);
        }
    });
    $router->post('/login/partner', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $login = new Login(new Database());
        if (!isset($data['p-user']) || !isset($data['p-password'])) {
            return json_encode(["error" => "email and password are required"]);
        } else {
            return $login->sign_in($data["p-user"], $data['p-password'], "partner");
        }
    });
    $router->post('/deliver/login',function(){
        $data = file_get_contents('php://input');
        $data = json_decode($data,true);
        $login = new Login(new Database());
        if(empty($data['d-user']) || empty($data['d-pass']) ){
            return json_encode(['success'=>false,'message' => 'email and password required',$data]);
        }else{
             return $login->check_deliver($data);
        }

    });
    $router->post('/deliver/register',function(){
        $data = file_get_contents('php://input');
        $data = json_decode($data,true);
        $register = new Register(new Database());
        return $register->register_deliver($data);
    });
    $router->post('/customer/OTP/{counter}', function ($counter) {
        if ($counter == 5) {
            header('location:/logout/customer');
            exit();
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $login = new Login(new Database());
        return $login->otp_management($data,'c');
    });
     $router->post('/deliver/OTP/{counter}', function ($counter) {
        if ($counter == 5) {
            header('location:/logout/customer');
            exit();
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $login = new Login(new Database());
        return $login->otp_management($data,'d');
    });
    $router->post('/customer/address', function () {
        Checkpoint::check('customer');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (empty($data['address'])) {
            return json_encode(['message' => $data]);
        } else {
            $register = new Register(new Database());
            return $register->address($data['address']);
        }
    });

    $router->get('/customer/profile', function () {
        Checkpoint::check('customer');
        $customer = new Login(new Database());
        return $customer->profile_customer();
    });
    $router->post('/customer/update_profile', function () {
        Checkpoint::check('customer');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $customer = new register(new Database);
        return $customer->update_user($data);
    });
    $router->get('/partner/update_products',function(){
       if( Checkpoint::check('partner') !== true){
        return  Checkpoint::check('partner');
       }
        $prod = new Product(new Database());
        return $prod->get_by_partner();
      

    });
    $router->get('/partner/categories',function(){
         $category = new Categories(new Database());
         $cat = $category->get_all_categories();
         $form = '<form class="container" id="add_product-form">
                <div id="upload_container" class="container">
                    <div id="drop_area">
                           <div class="upload-text">Click to browse here </div>
                           <div class="upload-hint">Supports JPG, PNG, GIF, WebP (Max 10MB)</div>
                           <div class="upload-hint">Use 1:1 images for more clear vision</div>
                           <input type="file" id="imageInput" accept="image/jpeg,image/png,image/gif,image/webp"> 
                           <div id="img_display" >
                               <h1 id ="pname">/<h1>
                               <p id="p_price"></p>
                           </div>
                    </div>
                    
                </div>
                
                <label for="p-name"> name</label>
                <input type="text" id="p-name">
                <label for="p-category"> category</label>
                <select id="p-category">
                <option disabled selected></option>';
            foreach($cat as $id => $name){
                $form .= '<option value="'.htmlspecialchars($id).'">'.htmlspecialchars($name).'</option>';
            }
            $form .= '</select>
                        <label for="p-price"> price</label>
                        <input type="number" id="p-price"  min="0" >
                        <button type="submit" id="add-product-button">add product</button>
                        <p class="form_err"></p>
                        </form>';
         
        return json_encode(['success'=>true,'content'=>$form]);
    });
    $router->get('/categories', function () {
        $category = new Categories(new Database());
        $cat = $category->get_all_categories();
        $partner = $category->get_all_partners();
        $categories = "";
        foreach ($cat as $key => $value) {
            $categories .= '<button class="category-btn" onclick="loadProducts(\'' .
                htmlspecialchars($key) . '\', \'cat\')">' .
                htmlspecialchars($value) . '</button>';
        };

        $bus = '';
        foreach ($partner as $key => $value) {
            $bus .= '<button class="category-btn" onclick="loadProducts(\'' .
                htmlspecialchars($key) . '\', \'part\')">' .
                htmlspecialchars($value) . '</button>';
        };

        $html = '
           <div id="cats" class="cat_container">' . $categories . '</div>
           <div id="pats"class="cat_container">' . $bus . '</div>
         ';
        return json_encode(['success' => true, 'content' => $html]);
    });
    $router->get('/products/all', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $product = new Product(new Database());
        $cat = new Categories(new Database);
        $partnes = $cat->get_all_partners();
        return $product->multi_by_partner($partnes);
    });

    $router->any('/products/category', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $product = new Product(new Database());
        if (isset($data['cat_id'])) {
            return $product->get_product_by_category($data['cat_id']);
        } elseif (isset($data['part_id'])) {
            return $product->get_product_by_partner($data['part_id']);
        } else {
            return json_encode(['success' => false]);
        }
    });
    $router->get('/products/partner', function () {


        $prod_id = $_SESSION['PartnersID'];
        $product = new Product(new Database());
        return $product->get_product_by_partner($prod_id);
    });
    $router->post('/product/update', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $product = new Product(new Database());
        return $product->update($data);
    });

    $router->get('/product/best', function () {
        Checkpoint::check('customer');
        $product = new Product(new Database);
        return $product->best_seller();
    });



    $router->any('/files', function () {
        return json_encode($_FILES);
    });
    $router->any("/error", function () {});
    $router->post("/order/delete", function () {
        Checkpoint::check('customer');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (isset($data['p_id'])) {
            $customer = $_SESSION['customer_id'];
            $partners = $data['p_id'];
            $ocd = ['customer' => $customer, 'partner' => $partners];
            $order = new Order(new Database());
            return $order->order_management($ocd, 'delete');
        }
    });
    $router->post('/orders/accept', function () {
        Checkpoint::check('partner');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $order = new Order(new Database());
        return $order->order_management($data, 'accept');
    });
    $router->post('/order/quantity_change', function () {
        Checkpoint::check('customer');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (!isset($data['id']) || !isset($data['quantity']) || !isset($data['pname'])) {
            return json_encode(['success' => true]);
        }
        $order = new Order(new Database());
        return $order->orderManupiration($data['id'], $data['quantity'], urldecode($data['pname']));
    });
    $router->post('/add_product', function () {
        Checkpoint::check('partner');

        if (isset($_FILES['product_img']) && isset($_POST['metadata'])) {
            $file =  $_FILES['product_img'];
            $upload = new Product(new Database());
            $prod_id = substr(uniqid("prod_"), 0, 8);
            $data = json_decode($_POST['metadata'], true);
            if (empty($_FILES) || empty($data)) {
                return json_encode(["success" => false]);
            }
            return $upload->add_product($data, $file);
        } else {
            return json_encode(['success' => false, 'message' => 'data missing']);
        }
    });
    $router->post("/orders/partner", function () {
        Checkpoint::check('customer');
        $cid = $_SESSION["customer_id"];
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $info = ["customer" => $cid, "partner" => $data['id']];

        $order = new Order(new Database);
        return $order->order_management($info, "partner");
    });

    $router->post("/order", function () {
        Checkpoint::check('customer');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (isset($data['partner']) || isset($data['product'])) {
            $order = new Order(new Database());
            return $order->order_management($data, "assign");
        } else {
            return json_encode(["succes" => false, "message" => "missing data"]);
        }
    });
    $router->get("/order/cart", function () {
        Checkpoint::check('customer');
        $cutomer['id'] = $_SESSION['customer_id'];
        $orders = new Order(new Database());
        return $orders->order_management($cutomer, "retrive");
    });

    $router->any("/logout/customer", function () {
        return Checkpoint::logout('c');
    });
    $router->get('/deliver/logout',function(){
       return Checkpoint::logout('d');
    });
    $router->any("/logout/partner", function () {
        return Checkpoint::logout('p');
    });
    $router->get('/deliver/retive',function(){
        if(Checkpoint::check('deliver') !== true){
            return Checkpoint::check('deliver');;
        }else if(Checkpoint::rider() ==! true){
           return Checkpoint::rider();

        }

        $order = new Order(new Database());
        return $order->order_retrive('d');

    });
    $router->get('/deliver/accept/{order_id}',function($order_id){
         if(Checkpoint::check('deliver') !== true){
            return Checkpoint::check('deliver');;
        }if(Checkpoint::rider() !== true){
            return Checkpoint::rider();
        }

        $order_i = urldecode($order_id);
        $order = new Order(new Database());
        return $order->order_accept($order_i);

    });
    $router->get('/deliver/current_orders',function(){
         if(Checkpoint::check('deliver') !== true){
            return Checkpoint::check('deliver');
        }
        $order = new Order(new Database());
        return $order->orderInDelivery();
    });
    $router->get('/deliver/done',function(){
         if(Checkpoint::check('deliver') !== true){
            return Checkpoint::check('deliver');;
        }
        $order = new Order(new Database());
        return $order->deliveryDone();

    });
    $router->get('/deliver/history',function(){
         if(Checkpoint::check('deliver') !== true){
            return Checkpoint::check('deliver');;
        }
        $order = new Order(new Database());
        return $order->deliverHistory();
    });

    $router->post("/search", function () {
         if(Checkpoint::check('customer') !== true){
            return Checkpoint::check('customer');;
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (isset($data["Key"])) {
            $search = new Search(new Database());
            return $search->search($data["Key"]);
        } else {
            return json_encode(["succes" => false, "data" => $data]);
        }
    });

    $router->post('/search/retrive', function () {
         if(Checkpoint::check('customer') !== true){
            return Checkpoint::check('customer');;
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $search = new Search(new Database());
        return $search->searched($data);
    });
    $router->any('/search/ready', function () {
         if(Checkpoint::check('partner') !== true){
            return Checkpoint::check('partner');;
        }
        $search = new Search(new Database());
        return $search->ready();
    });

    $router->get('/partner/all', function () {
        if(Checkpoint::check('partner') !== true){
            return Checkpoint::check('partner');;
        }
        $order = new Order(new Database());
        return $order->order_management([], 'active');
    });
    $router->get('/partner/get_profile',function(){
        if(Checkpoint::check('partner') !== true){
            return json_encode(['success'=>false,'message'=>'login']);
        }
        return json_encode(['success'=>true,'content'=>$_SESSION['PartnersID']]);
    });
    $router->get('/partner/profile', function () {
        if(Checkpoint::check('partner') !== true){
            return Checkpoint::check('partner');;
        }
        $login = new Login(new Database());
        return $login->profile_partner();
    });
    $router->post('/partner/update_profile', function () {
        if(Checkpoint::check('partner') !== true){
            return Checkpoint::check('partner');;
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $register = new Register(new Database());
        return $register->update_partner($data);
    });



    $router->any('/forgot/check', function () {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $register = new register(new Database());
        $exist = $register->check_mail($data['email'],'c');
        if ($exist) {
            $mail = new Email($data['email']);
            return json_encode(['success' => true, 'issue' => $mail->sendreset()]);
        } else {
            return json_encode(['success' => false, $data]);
        }
    });

    $router->get('reset/{origin}/{email}', function ($origin, $email) {
        header("content-type:text/html");
        readfile(__DIR__ . '/../page/reset.html');
        exit();
    });

    $router->get('/welcome', function () {
        header("content-type: text/html");
        readfile(__DIR__ . '/../page/home.html');
        exit();
    });
    $router->get('/retrive', function () {
        header("content-type: text/html");
        readfile(__DIR__ . '/../page/forgot.html');
        exit();
    });
     $router->get('/deliver', function () {
        header("content-type: text/html");
        readfile(__DIR__ . '/../page/deliver.html');
        exit();
    });
       $router->get('/deliver/murugo', function () {
        if(Checkpoint::check('deliver') !== true){
            return Checkpoint::check('deliver');;
        }
        header("content-type: text/html");
        readfile(__DIR__ . '/../page/delivery_home.html');
        exit();
    });
    $router->get('/home', function () {
        if(Checkpoint::check('customer') !== true){
            header('location:/welcome');
        }
        header("content-type: text/html");
        readfile(__DIR__ . '/../page/home2.html');
        exit();
    });
    $router->get('/payment', function () {
        if(Checkpoint::check('customer') !== true){
           header('location:/welcome');
        }
        header("content-type:text/html");
        readfile(__DIR__ . '/../page/payment.html');
        exit();
    });
    $router->get('/partner', function () {
        header("content-type:text/html");
        readfile(__DIR__ . '/../page/partner.html');
        exit();
    });
    $router->post('/change/{option}', function ($option) {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (!isset($data['email']) || empty($data['email'])) {
            return json_encode(['success' => false]);
        }
        if ($option === 'customer') {
            $register = new Register(new Database());
            return $register->change_pass($data['pass'], $data['email']);
        }
    });
    $router->get('/partner_home', function () {
        if(Checkpoint::check('partner') !== true){
            header('location:/welcome');
        }
        header("content-type:text/html");
        readfile(__DIR__ . '/../page/partner_home.html');
        exit();
    });
    $router->any('/payment/order', function () {
        if(Checkpoint::check('customer') !== true){
            return Checkpoint::check('customer');
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $data['customer'] = $_SESSION['customer_id'];
        $payment = new payment(new Database());
        return $payment->payment($data, 'retrive');
    });
    $router->post('/pay', function () {
        if(Checkpoint::check('customer') !== true){
            return Checkpoint::check('customer');;
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $data['customerID'] = $_SESSION['customer_id'];
        if (!empty($data['payement_id']) || !empty($data['number'])) {
            $payment = new Payment(new Database());
            return $payment->payment($data, 'pay');
        } else {
            return json_encode('data need');
        }
    });

    $dispatcher = new Dispatcher($router->getData());
    try {
        $login = !str_contains($path, "/login");
        $register = !str_contains($path, "/register");
        $check = $login && $register;
        if ($check) {
            //print_r($check);
            if (isset($_COOKIE['PHPSESSID'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_id($_COOKIE['PHPSESSID']);
                    session_start();
                    $is_home = str_contains($path, "/home");
                    $is_retrive = str_contains($path, "/reset");
                    if ($is_home) {
                        file_request($path);
                        header("content-type: text/html");
                        readfile(__DIR__ . '/../page/home2.html');
                        exit();
                    }
                    if ($is_retrive) {
                        file_request($path);
                    }
                }
            } else {
            }
        }
        $resp = $dispatcher->dispatch($method, $path);
        if (is_array($resp)) {
            foreach ($resp as $key => $values) {
                echo $key . ' : ' . $values;
            }
        }
        else{
            echo $resp;
        }
    } catch (HttpRouteNotFoundException $e) {
        header("content-type:text/html");
        readfile(__DIR__ . '/../page/notfound.html');
        exit();
    }
}
