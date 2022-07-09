<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../config/autoload.php';
require '../config/AuthMiddleware.php';
require  '../model/User.php';

$allHeaders = getallheaders();
$auth = new Auth($conn, $allHeaders);

$result = json_encode($auth->isValid());
$result_array = json_decode($result);

if($result_array->success == 1):
   $user = new User($conn);
   $returnData = $user->fetch_user($result_array->user);
else:
     $returnData = $result;
endif;

echo $returnData;
