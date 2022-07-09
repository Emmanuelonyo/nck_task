<?php

function msg($status,$code,$message, $extra = []){
    return json_encode([
        "status" => $status,
        "code" => $code,
        "message" => $message,
        "data" => $extra
    ]);
}