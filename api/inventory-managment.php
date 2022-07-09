<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../config/autoload.php';
require '../config/AuthMiddleware.php';
require  '../model/Inventories.php';
require  '../model/User.php';

$allHeaders = getallheaders();
$auth = new Auth($conn, $allHeaders);

$result = json_encode($auth->isValid());
$result_array = json_decode($result);

$data = json_decode(file_get_contents("php://input"));

if($result_array->success == 1):

   $inventory = new Inventory($conn,$result_array->user);

   if ($_SERVER['REQUEST_METHOD'] == "GET"){
         
      $returnData = $inventory->read_inventory();
      
   }elseif($_SERVER['REQUEST_METHOD'] == "PUT"){

      $returnData = $inventory->update_inventory($data);
      
   }elseif($_SERVER['REQUEST_METHOD'] == "POST"){

      $returnData = $inventory->create_inventory($data);

   }elseif($_SERVER['REQUEST_METHOD'] == "DELETE"){

       $returnData = $inventory->delete_inventory($data);

   }else{
      
      $returnData = msg(0,404,'Page Not Found!');
   }


else:
     $returnData = $result;
endif;

echo $returnData;
