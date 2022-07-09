<?php


class User{

     public $conn = "";
     
    function __construct($conn)
    {
        $this->conn = $conn;
       
        
    }

    
    public function login($data){
        $request = $data;

        if(!isset($request->email) && !isset($request->password)){
            return msg(0,500,"invalid parameters");
        }
        
        if(empty($request->email) || empty($request->password)){
            return msg(0,500,"Email and password is required");
        }

        try {
            $arr['email'] = $request->email;
            
            $stmt = "SELECT * FROM `users` WHERE `email` = :email LIMIT 1";
            $data1 = $this->conn->read($stmt,$arr);
            $data = json_decode(json_encode($data1[0]),true);
           
            
            if(is_array($data) && $request->password == password_verify($request->password, $data['password'])){
                //VERIFY PASSWORD 
                $jwt = new JwtHandler();
                $token = $jwt->jwtEncodeData("NCK Task", ["email"=>$data['email'], "user_id" => $data['user_id'], "firstname" => $data['firstname'], "lastname"=> $data['lastname']]);
                return msg(0,200,"Login Successful",["token"=>$token]);

            }else{
                return msg(0,500,"Invalid Email or Password");
            }


        } catch (\Throwable $th) {
            
            return msg(0,500,$th->getMessage());
        }
        
    }


    public  function register($data){
        $request = $data;
        if(!isset($request->email) || !isset($request->password) || !isset($request->phone) || !isset($request->firstname) || !isset($request->lastname)){
            return msg(0,402,"invalid parameters");
        }
        
        if(empty($request->email) || empty($request->password) || empty($request->phone) || empty($request->firstname) || empty($request->lastname)){
            $fields = ["firstname","lastname","email","phone","password"];
            return msg(0,500,"all fields are requiered",$fields);
        }

        try {

            $arr['firstname'] = $request->firstname;
            $arr['lastname'] = $request->lastname;
            $arr['phone'] = $request->phone;
            $arr['email'] = $request->email;
            $arr['password'] = password_hash($request->password,PASSWORD_BCRYPT);
            
            //check if user exist 

            $check_email['email'] = $request->email;            
            $email_stmt = "SELECT * FROM users WHERE email = :email";
            $email_exist = $this->conn->read($email_stmt,$check_email);
            
            if(is_array($email_exist)){
                                
                return msg(0,403,"Email already Exist");
            }

            $check_phone['phone'] = $request->phone;            
            $phone_stmt = "SELECT * FROM users WHERE phone = :phone";
            $phone_exist = $this->conn->read($phone_stmt,$check_phone);

            if(is_array($phone_exist)){
                return msg(0,403,"Phone number already Exist");
            }


            $stmt = "INSERT INTO users(`firstname`,`lastname`,`phone`,`email`,`password`) VALUES(:firstname,:lastname,:phone,:email,:password)";
            $data = $this->conn->write($stmt,$arr);

            if($data){
                
                return msg(0,200,"Registration Successful");

            }else{
                return msg(0,500,"Invalid Email or Password");
            }


        } catch (\Throwable $th) {
            
            return msg(0,500,$th->getMessage());
        }

    }


    public function fetch_user($data){
        try {

            $arr['user_id'] = $data;

            $stmt = "SELECT 
                        users.firstname, 
                        users.lastname, 
                        users.phone, 
                        users.email, 
                        user_level.name as user_level, 
                        users.created_at, 
                        users.updated_at  
                        FROM users
                        JOIN user_level
                            ON users.level_id = user_level.id
                        WHERE users.user_id = :user_id";
            $data = $this->conn->read($stmt,$arr);

            if($data){
                
                return msg(0,200,"Profile Retrieved Successful",$data);

            }else{
                return msg(0,500,"Invalid Email or Password");
            }


        } catch (\Throwable $th) {
            
            return msg(0,500,$th->getMessage());
        }
    }


}