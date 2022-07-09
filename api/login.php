<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../config/autoload.php';
require '../config/JwtHandler.php';
require '../model/User.php';

$user = new User($conn);



$data = json_decode(file_get_contents("php://input"));

$returnData = [];

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):

    $returnData = msg(0,404,'Page Not Found!');

else:


    $returnData = $user->login($data);
    


endif;

echo $returnData;