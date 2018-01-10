<?php

require_once('DB_Connect.php');

/**
* author
*/
class Pendaftaran
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

	public function getAllPendaftaran(){

		$stmt = $this->con->prepare("SELECT * FROM pasien");
		$stmt->execute();       
		return $stmt;

	}

	public function isPendaftaranExisted($nama,$NIK) {
        $result = $this->con->prepare("SELECT * from pasien WHERE nama=:nama AND NIK=:NIK");
        $result->execute(array(':nama'=>$nama,':NIK'=>$NIK));
        $userRow=$result->fetch(PDO::FETCH_ASSOC);

        if($result->rowCount() == 1)
        {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }

	public function insertPendaftaran($nama,$NIK,$jen_kelamin,$temp_lahir,$tgl_lahir,$alamat,$status,$pekerjaan,$jabatan,$lama_kerja,$agama,$suku,$telp,$jenis_pasien,$nama_rs, $poli){

		$stmt = $this->con->prepare("INSERT INTO pasien (nama,NIK,jen_kelamin,temp_lahir,tgl_lahir, alamat, status, pekerjaan, jabatan, lama_kerja, agama, suku, telp, jenis_pasien, nama_rs, poli) VALUES (:nama,:NIK,:jen_kelamin,:temp_lahir,:tgl_lahir, :alamat,:status,:pekerjaan,:jabatan,:lama_kerja,:agama,:suku,:telp,:jenis_pasien,:nama_rs,:poli)");

		$stmt->bindparam(":nama", $nama);
		$stmt->bindparam(":NIK", $NIK);
		$stmt->bindparam(":jen_kelamin", $jen_kelamin);
		$stmt->bindparam(":temp_lahir", $temp_lahir);
		$stmt->bindparam(":tgl_lahir", $tgl_lahir);
		$stmt->bindparam(":alamat", $alamat);
		$stmt->bindparam(":status", $status);
		$stmt->bindparam(":pekerjaan", $pekerjaan);
		$stmt->bindparam(":jabatan", $jabatan);
		$stmt->bindparam(":lama_kerja", $lama_kerja);
		$stmt->bindparam(":agama", $agama);
		$stmt->bindparam(":suku", $suku);
		$stmt->bindparam(":telp", $telp);
		$stmt->bindparam(":jenis_pasien", $jenis_pasien);
		$stmt->bindparam(":nama_rs", $nama_rs);
		$stmt->bindparam(":poli", $poli);

		$stmt->execute();

		return  $stmt;
	}


	public function updatePendaftaran($id, $nama, $NIK, $jen_kelamin, $temp_lahir, $tgl_lahir, $alamat,$status, $pekerjaan, $jabatan, $lama_kerja, $agama, $suku, $telp, $jenis_pasien,$nama_rs, $poli){

		$stmt= $this->con->prepare("UPDATE pasien SET nama=:nama, NIK=:NIK, jen_kelamin=:jen_kelamin, temp_lahir=:temp_lahir, tgl_lahir=:tgl_lahir, alamat=:alamat, status=:status, pekerjaan=:pekerjaan, jabatan=:jabatan, lama_kerja=:lama_kerja, agama=:agama, suku=:suku, telp=:telp, jenis_pasien=:jenis_pasien, nama_rs=:nama_rs, poli=:poli WHERE id_pendaftaran=:id");

		$stmt->bindparam(":id", $id);
		$stmt->bindparam(":nama", $nama);
		$stmt->bindparam(":NIK", $NIK);
		$stmt->bindparam(":jen_kelamin", $jen_kelamin);
		$stmt->bindparam(":temp_lahir", $temp_lahir);
		$stmt->bindparam(":tgl_lahir", $tgl_lahir);
		$stmt->bindparam(":alamat", $alamat);
		$stmt->bindparam(":status", $status);
		$stmt->bindparam(":pekerjaan", $pekerjaan);
		$stmt->bindparam(":jabatan", $jabatan);
		$stmt->bindparam(":lama_kerja", $lama_kerja);
		$stmt->bindparam(":agama", $agama);
		$stmt->bindparam(":suku", $suku);
		$stmt->bindparam(":telp", $telp);
		$stmt->bindparam(":jenis_pasien", $jenis_pasien);
		$stmt->bindparam(":nama_rs", $nama_rs);
		$stmt->bindparam(":poli", $poli);

		// $stmt->execute(array(":id_pendaftaran"=>$id));
		$stmt->execute();
		return $stmt;	

	}

    public function deletePendaftaran($id_pendaftaran){

    	$stmt = $this->con->prepare("DELETE FROM pasien WHERE id_pendaftaran=:id_pendaftaran");
    	$stmt->execute(array(":id_pendaftaran"=>$id_pendaftaran));
    	return $stmt;

    }

    public function getById($id){
    	$stmt = $this->con->prepare("SELECT * FROM pasien WHERE id_pendaftaran=:id");
    	$stmt->execute(array(":id"=>$id));
    	return $stmt;
    }
}

?>