<?php

class Sales{

    function __construct($conn,$user_id){
        $this->conn = $conn;
        $this->user_id = $user_id;
        $user = new User($this->conn);
        $this->fetch_user = json_decode($user->fetch_user($user_id),true);
    }

    public function read_sales(){

        try{
            
            $stmt = "SELECT 
                        products.name as product_name,
                        cart.qty as quantity,
                        cart.price,
                        cart.order_ref,
                        categories.name as category

                        FROM cart
                        JOIN products
                        ON cart.product_id = products.id
                        JOIN categories
                        ON products.categorie_id = categories.id
                        ";
            
            $data = $this->conn->read($stmt);

            if(is_array($data)){

                return msg(0,200,"Cart Retrieved Successful",$data);
                
            }else{

                return msg(0,200,"No Items in found in cart");
            }

            


        }catch(\Throwable $e){
            
            return msg(0,500,$e->getMessage());
        }

    }



    public function make_purchase($data){
        

            if(!isset($data->product_id) && !isset($data->quantity)){
                
                return msg(0,500,"Invalid Parameters");
            }

            if(empty($data->product_id) || empty($data->quantity)){
                
                return msg(0,500,"All fields are requiered");
            }

            try {

                //check the price of a product id or return false if product dosent exist or out of stork 
                $prd_arr['product_id'] =  $data->product_id;
                $check_product = "SELECT name,quantity,sale_price as price FROM products WHERE id = :product_id";
                $product_details = $this->conn->read($check_product,$prd_arr);
                if(!is_array($product_details)){

                    return msg(0,500,"Product Does not exist");
                }

                $quantity = $product_details[0]->quantity;
                $price = $product_details[0]->price;
                $product_name = $product_details[0]->name;

                if($quantity < 1 || $quantity < $data->quantity){

                    return msg(0,500,"Out of Stuck",$product_details[0]);
                }

                $prd_arr['product_id'] =  $data->product_id;
                $prd_arr['quantity'] =  $data->quantity;
                $updat_product = "UPDATE products SET `quantity`=quantity-:quantity WHERE id = :product_id";
                $resp = $this->conn->write($updat_product,$prd_arr);

                if(!$resp){
                    return msg(0,500,"Oops! Somthing Went Wrong");
                }
                //if all is pass then add to cart 

                $add_arr['user_id'] =  (int)$this->user_id;
                $add_arr['product_id'] =  $data->product_id;
                $add_arr['quantity'] =  $data->quantity;
                $add_arr['price'] =  $price*$data->quantity;

                $stmt = "INSERT INTO cart(user_id,product_id,qty,price) VALUES(:user_id,:product_id,:quantity,:price)";
                $addtocart = $this->conn->write($stmt,$add_arr);

                if($addtocart){

                    return msg(0,500,"Product has been added to cart successfuly", $add_arr);
                }else{

                    return msg(0,500,"Oops! Somthing Went Wrong");
                }                
            
            } catch (\Throwable $th) {
                return msg(0,500,$th->getMessage());
            }


    }


}