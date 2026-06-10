<?php
require "Register.php";
class Upload{
    private  array $options = ['image/jpg','image/jpeg','image/png','image/webp'];
   public function upload(string $demand, array $file,string $f_name){
    try{ 
    switch($demand){
        case 'upload':
            if(is_array($file)){
                if(in_array($file['type'],$this->options)){
                    if(move_uploaded_file($file['tmp_name'],__DIR__.'/../page/image/'.$f_name)){
                        return true;
                    }else{ 
                        return 'failed to move upload';
                    }
                }
                else{
                   return  "unsaported file ext";
                }
                }
            else{
                 return  "unsaported file ext";
                    }
            break;
        case 'change':
            if(is_array($file)){
                if(in_array($file['type'],$this->options)){
                    $this->check_file($f_name);
                 
                
                     $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

                    if(move_uploaded_file($file['tmp_name'],__DIR__.'/../page/image/partners/'.$f_name.'.'.$extension)){
                        $reg = new Register(new Database());
                        $reg->image_update($f_name.'.'.$extension);

                        return json_encode(['success'=>true]);
                    }else{ 
                        return 'failed to move upload';
                    }
                }
                else{
                   return  "unsaported file ext";
                }
                }
            else{
                 return  "unsaported file ext";
                    }
            
            break;
        case 'get_person':
            //continue
            break;
    }
               
            }
           
    
    catch (Exception $th){
        return json_encode(["success" => false,
                            "message" => $th->getMessage(),
                            "line" => $th->getLine(),
                            "file" => $th->getFile()]);
    } 
   }
   private function check_file(string $name){
    $folders = ['/../page/image/','/../page/image/customer/','/../page/image/partner/'];
    foreach($folders as $fold){
        if($file = glob(__DIR__.$fold.$name.'.*')){
            if(!empty($file)){
                unlink($file[0]);
            
            }
            
        }
    }
    
   }
   
   
   
}