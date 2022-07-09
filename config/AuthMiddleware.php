<?php
require  __DIR__. '/JwtHandler.php';

class Auth extends JwtHandler
{
    protected $db;
    protected $headers;
    protected $token;

    public function __construct($db, $headers)
    {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isValid()
    {




        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            // if(!isset($_SESSION['token']) || "Bearer " .$_SESSION['token']."" !== $this->headers['Authorization']):
            //     return [
            //         "success" => 0,
            //         "message" => "Unauthorized Access"
            //     ];
            //     exit();
    
            // endif;

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchUser($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }

    protected function fetchUser($user_id)
    {
        try {
            $arr['user_id'] = $user_id;
            $fetch_user_by_id = "SELECT * FROM `users` WHERE `user_id`=:user_id";
            
            $data = $this->db->read($fetch_user_by_id,$arr);

            if ($data) :                
                return $data[0]->user_id;
            else :
                
                return [
                    "success" => 0,
                    "message" => "User not found"
                ];

            endif;
        } catch (PDOException $e) {
            return [
                "success" => 0,
                "message" => "Something Went Wrong"
            ];
        }
    }
}