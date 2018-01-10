<?php

class Db{
	private $host = "localhost";
	private $user = "root";
	private $pass = "";
	private $db_name = "rmt";
	public $con;

	public function dbConn()
	{
		$this->con = null;
		try{		
			$this->con = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,$this->user,$this->pass);
			$this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			echo "Connection error: ".$e->getMessage();

		} 
		return $this->con;
	}

}


?>
