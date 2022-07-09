<?php

    Class Inventory{
        public function __construct($conn,$user_id)
        {
            $this->conn = $conn;
            $user = new User($this->conn);
            $this->fetch_user = json_decode($user->fetch_user($user_id),true);
        }

        public function create_inventory($data){
            //admin Right
            if($this->fetch_user['data'][0]['user_level'] !== "Admin"){
               
                return msg(0,403,"Access Forbiden to this User");
            }

            if(!isset($data->product_name) && !isset($data->quantity) && !isset($data->buy_price) && !isset($data->sale_price) && !isset($data->category)){
                
                return msg(0,500,"Invalid Parameters");
            }

            if(empty($data->product_name) || empty($data->quantity) || empty($data->buy_price) || empty($data->sale_price) || empty($data->category)){
                
                return msg(0,500,"All fields are requiered");
            }
            

            try {

                $arr['product_name'] =  $data->product_name;
                $arr['quantity'] =  $data->quantity;
                $arr['buy_price'] = $data->buy_price;
                $arr['sale_price'] =    $data->sale_price;
                $arr['category_id'] =  $data->category;

                $check_name['product_name'] = $data->product_name;            
                $name_stmt = "SELECT * FROM products WHERE name = :product_name";
                $name_exist = $this->conn->read($name_stmt,$check_name);
                
                if(is_array($name_exist)){
                    return msg(0,403,"Product already Exist");
                }

                $stmt = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id) VALUES(:product_name,:quantity,:buy_price,:sale_price,:category_id)";

                $insert = $this->conn->write($stmt,$arr);

                if($insert){
                
                    return msg(1,200,"Product Created Successfuly");
    
                }else{
                    return msg(0,500,"Oops! Somthing Went Wrong");
                }

            }catch (\Throwable $th) {
                return msg(0,500,$th->getMessage());
            }

        }


        public function update_inventory($data){

                        //admin Right
                        if($this->fetch_user['data'][0]['user_level'] !== "Admin"){
               
                            return msg(0,403,"Access Forbiden to this User");
                        }
            
                        if(!isset($data->product_name) && !isset($data->product_id) && !isset($data->quantity) && !isset($data->buy_price) && !isset($data->sale_price) && !isset($data->category)){
                            
                            return msg(0,500,"Invalid Parameters");
                        }
            
                        if(empty($data->product_name) || empty($data->product_id) || empty($data->quantity) || empty($data->buy_price) || empty($data->sale_price) || empty($data->category)){
                            
                            return msg(0,500,"All fields are requiered");
                        }
                        
            
                        try {
            
                            $arr['product_name'] =  $data->product_name;
                            $arr['product_id'] =  $data->product_id;
                            $arr['quantity'] =  $data->quantity;
                            $arr['buy_price'] = $data->buy_price;
                            $arr['sale_price'] =    $data->sale_price;
                            $arr['category_id'] =  $data->category;
            
                            $check_id['product_id'] = $data->product_id;            
                            $check_id['product_name'] = $data->product_name;            
                            $name_stmt = "SELECT * FROM products WHERE id != :product_id AND name =:product_name";
                            $name_exist = $this->conn->read($name_stmt,$check_id);
                            
                            if(is_array($name_exist)){
                                return msg(0,403,"Another Product with same name already Exist");
                            }
            
                            $stmt = "UPDATE products SET `name` =:product_name, `quantity`= :quantity, `buy_price`=:buy_price, `sale_price`=:sale_price, `categorie_id`= :category_id WHERE `id`=:product_id";
            
                            $insert = $this->conn->write($stmt,$arr);
            
                            if($insert){
                            
                                return msg(1,200,"Product Updated Successfuly");
                
                            }else{
                                return msg(0,500,"Oops! Somthing Went Wrong");
                            }
            
                        }catch (\Throwable $th) {
                            return msg(0,500,$th->getMessage());
                        }
        }


        public function read_inventory(){
            try{
                $stmt = "SELECT 
                            products.name as product_name,
                            products.quantity as stocks,
                            products.buy_price,
                            products.sale_price,
                            categories.name as category

                            FROM products
                            JOIN categories
                            ON products.categorie_id = categories.id
                            ";
                $data = $this->conn->read($stmt);

                return msg(1,200,"Products Retrieved Successful",$data);


            }catch(\Throwable $e){
                return msg(0,500,$e->getMessage());
            }
        }


        public function delete_inventory($data){
            //admin right
            if($this->fetch_user['data'][0]['user_level'] !== "Admin"){
               
                return msg(0,403,"Access Forbiden to this User");
            }

            if(!isset($data->product_id)){                
                return msg(0,500,"Invalid Parameters");
            }

            if(empty($data->product_id)){
                
                return msg(0,500,"Product id is requiered");
            }

            try{
                $arr['product_id'] =  $data->product_id;
                //check if the products exist
                $check_name['product_id'] = $data->product_id;            
                $name_stmt = "SELECT * FROM products WHERE id = :product_id";
                $name_exist = $this->conn->read($name_stmt,$check_name);
                
                if(!is_array($name_exist)){
                    return msg(0,403,"Product not Found");
                }
                
                $stmt = "DELETE FROM products WHERE id = :product_id";
                $delete = $this->conn->write($stmt,$arr);

                if($delete){
                
                    return msg(1,200,"Product Deleted Successfuly");
    
                }else{
                    return msg(0,500,"Opps! Somthing Went wrong");
                }

            }catch(\Throwable $th){
                return msg(0,500,$th->getMessage());
            }

        }

        

    }