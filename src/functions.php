<?php
   function file_request($path){
     $filename = basename($path);
                        $extension = pathinfo($filename,PATHINFO_EXTENSION);
                        if($extension === 'css'){
                            header("content-Type:text/css");
                            $file = __DIR__.'/../page/css/'.$filename;
                            readfile($file);
                            exit();
                        }
                         if($extension === 'js'){
                            header("content-Type:text/js");
                            $file = __DIR__.'/../page/js/'.$filename;
                            readfile($file);
                            exit();
                        } 
                        if($extension === 'jpeg'){
                            $file = __DIR__.'/../page/image/'.$filename;
                            readfile($file);
                            exit();
                        } 
                         if($extension === 'png'){
                            $file = __DIR__.'/../page/image/png/'.$filename;
                            readfile($file);
                            exit();
                        } 
                        
   }