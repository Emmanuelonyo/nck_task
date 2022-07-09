<?php   


    class Database {
        //DATABASE PARAMS
        public function connect(){
            $this->db = null;

            try {
                $this->db = new PDO("mysql:host=".DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                
                echo 'Connection Error: ' . $e->getMessage();

            }

             return $this->db;
            
        }


    public function read($query,$data = [])
	{

		$db = $this->connect();
		$stm = $db->prepare($query);

		if(count($data) == 0)
		{
			$stm = $db->query($query);
			$check = 0;
			if($stm){
				$check = 1;
			}
		}else{

			$check = $stm->execute($data);
			
		}

		if($check)
		{
			$data = $stm->fetchAll(PDO::FETCH_OBJ);
			if(is_array($data) && count($data) > 0)
			{
				return $data;
			}

			return false;
		}else
		{
			return false;
		}
	}

	public function write($query,$data = [])
	{

		$db = $this->connect();
		$stm = $db->prepare($query);

		if(count($data) == 0)
		{
			$stm = $db->query($query);
			$check = 0;
			if($stm){
				$check = 1;
			}
		}else{

			$check = $stm->execute($data);
		}

		if($check)
		{
			return true;
		}else
		{
			return false;
		}
	}

    }