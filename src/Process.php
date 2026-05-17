<?php
class Process{
  public function __construct(private Product $geteway,
                              private Category $catgeteway,
                              private Login $db
                              ){

  }
    public function urlprocessor(string $url,?string $id): void{
      if ($id === null) {
          $this->multiprocess($_SERVER['REQUEST_METHOD']);
      } else {
          $this->singleprocess($_SERVER['REQUEST_METHOD'], $id);
      }

     
    }
    private function multiprocess(string $method): void{
          switch ($method){
            case "GET":
              echo json_encode($this->geteway->get_all_products());
              break;
            }
    }
     
    private function singleprocess(string $method,string $id){
        switch ($method){
            case "GET":
              echo json_encode($this->geteway->get_product_by_category($id));
              break;
        }
    }
    public function get_cat(){ 
      echo json_encode($this->catgeteway->get_all_categories());
    }
}