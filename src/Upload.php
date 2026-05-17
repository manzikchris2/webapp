<?php
class Upload{
    private  array $options = ['image/jpg','image/jpeg','image/png','image/webp'];
   public function upload(string $demand,$file,string $f_name){
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
        case 'get_product':
            //cintinue
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
}