<?php

require_once('DB_Connect.php');

/**
* 
*/
class Diagnosis
{
	private $con;
	//API key
	protected $API = 'AIzaSyAY1lULinTPFhCaqQ01s-ZkjuokhLrhqVI';

	function __construct()
	{
		$database = new Db();;
		$db = $database->dbConn();
		$this->con = $db;
	}

	public function validateAPI($api){
        
        if($this->API !== $api){
            return false;
        }else{
            
        return true;
        }
    }

	public function runQuery($sql)
	{
		$stmt = $this->con->prepare($sql);
		return $stmt;
	}

	public function redirect($url)
	{
		header("Location: $url");
	}

	public function allDiagnosis()
	{
		$stmt = $this->con->prepare("SELECT * FROM diagnosis");
		$stmt->execute();
		return $stmt;
	}

	public function diagCat($category)
	{
		$stmt = $this->con->prepare("SELECT * FROM diagnosis WHERE category=:category");
		$stmt->execute(array(':category'=>$category));
		return $stmt;
	}

	public function delete($id)
	{
		$stmt = $this->con->prepare("DELETE FROM diagnosis WHERE id_diag=:id");
		$stmt->execute(array(":id"=>$id));
		return $stmt;
	}
}

?>