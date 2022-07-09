<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../config/autoload.php';
require '../config/AuthMiddleware.php';
require  '../model/Sales.php';
require  '../model/User.php';

$allHeaders = getallheaders();
$auth = new Auth($conn, $allHeaders);

$result = json_encode($auth->isValid());
$result_array = json_decode($result);

$data = json_decode(file_get_contents("php://input"));

if($result_array->success == 1):

   $sales = new Sales($conn,$result_array->user);

   if ($_SERVER['REQUEST_METHOD'] == "GET"){
         
      $returnData = $sales->read_sales();
      
   }elseif($_SERVER['REQUEST_METHOD'] == "POST"){

      $returnData = $sales->make_purchase($data);

   }else{
      
      $returnData = msg(0,404,'Page Not Found!');
   }


else:
     $returnData = $result;
endif;

echo $returnData;
