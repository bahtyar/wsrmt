<?php

require_once('DB_Connect.php');

/**
* 
*/
class RM
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

	public function getRmbyId($id)
	{
		$stmt = $this->con->prepare("SELECT * FROM pasien INNER JOIN rm ON pasien.id_pendaftaran=rm.id_pendaftaran WHERE pasien.id_pendaftaran=:id");
		$stmt->execute(array(':id'=>$id));
		return $stmt;
	}

	public function getByNum($id)
	{
		$stmt = $this->con->prepare("SELECT * FROM rm WHERE id_rm=:id");
		$stmt->execute(array(':id'=>$id));		
		return $stmt;

	}

	public function getAllRm(){
		$stmt=$this->con->prepare("SELECT * FROM rm");
		$stmt->execute();
		return $stmt;
	}

	public function insertRM($id_pendaftaran,$ruang, $nama_rs, $mrs,$jam,$anamnesa,$riwayat_penyakit,$riwayat_pekerjaan,$riwayat_alergi,$keadaan_umum,$kesadaran,$E,$V,$M, $suhu, $nadi, $respirasi, $TD, $pemeriksaan, $penunjang, $diagnosa_kerja, $diagnosa_banding, $pelayanan, $nama_dr, $poli){

		$stmt = $this->con->prepare("SELECT * FROM pasien WHERE id_pendaftaran=".$id_pendaftaran);
		$stmt->execute();
		$result=$stmt->fetch(PDO::FETCH_ASSOC);		
		$today = date("Y-m-d");
		$diff = date_diff(date_create($result['tgl_lahir']), date_create($today));
		$umur = $diff->format('%y');

		$NIK = $result['NIK'];
		$no_RM = $result['NIK'];
		$nama = $result['nama'];
		$jen_kelamin = $result['jen_kelamin'];
		$alamat = $result['alamat'];		

		$stmt = $this->con->prepare("INSERT INTO rm(no_RM,id_pendaftaran,nama,NIK,jen_kelamin,umur,alamat,ruang,nama_rs,mrs,jam,anamnesa,riwayat_penyakit,riwayat_pekerjaan,riwayat_alergi,keadaan_umum,kesadaran,E,V,M,suhu, nadi, respirasi,TD,pemeriksaan,penunjang,diagnosa_kerja,diagnosa_banding,pelayanan,nama_dr, poli, created_at) VALUES (:no_RM,:id_pendaftaran,:nama,:NIK,:jen_kelamin,:umur,:alamat,:ruang,:nama_rs,:mrs,:jam,:anamnesa,:riwayat_penyakit,:riwayat_pekerjaan,:riwayat_alergi,:keadaan_umum,:kesadaran,:E,:V,:M,:suhu,:nadi, :respirasi,:TD,:pemeriksaan,:penunjang,:diagnosa_kerja,:diagnosa_banding,:pelayanan,:nama_dr,:poli,NOW())");

		$stmt->bindparam(":no_RM",$no_RM);
		$stmt->bindparam(":id_pendaftaran",$id_pendaftaran);
		$stmt->bindparam(":nama", $nama);
		$stmt->bindparam(":NIK", $NIK);
		$stmt->bindparam(":jen_kelamin", $jen_kelamin);
		$stmt->bindparam(":umur", $umur);
		$stmt->bindparam(":alamat", $alamat);
		$stmt->bindparam(":ruang", $ruang);
		$stmt->bindparam(":nama_rs", $nama_rs);
		$stmt->bindparam(":mrs", $mrs);
		$stmt->bindparam(":jam", $jam);
		$stmt->bindparam(":anamnesa", $anamnesa);
		$stmt->bindparam(":riwayat_penyakit", $riwayat_penyakit);
		$stmt->bindparam(":riwayat_pekerjaan", $riwayat_pekerjaan);
		$stmt->bindparam(":riwayat_alergi", $riwayat_alergi);
		$stmt->bindparam(":keadaan_umum", $keadaan_umum);
		$stmt->bindparam(":kesadaran", $kesadaran);
		$stmt->bindparam(":E", $E);
		$stmt->bindparam(":V", $V);
		$stmt->bindparam(":M", $M);
		$stmt->bindparam(":suhu", $suhu);
		$stmt->bindparam(":nadi", $nadi);
		$stmt->bindparam(":respirasi", $respirasi);
		$stmt->bindparam(":TD", $TD);
		$stmt->bindparam(":pemeriksaan", $pemeriksaan);
		$stmt->bindparam(":penunjang", $penunjang);
		$stmt->bindparam(":diagnosa_kerja", $diagnosa_kerja);
		$stmt->bindparam(":diagnosa_banding", $diagnosa_banding);
		$stmt->bindparam(":pelayanan", $pelayanan);
		$stmt->bindparam(":nama_dr", $nama_dr);
		$stmt->bindparam(":poli", $poli);

		$stmt->execute();
		return $stmt;
	}

	public function updateRM($id_rm, $id, $nama,$jen_kelamin,$alamat,$ruang,$nama_rs,$mrs,$jam,$anamnesa,$riwayat_penyakit,$riwayat_pekerjaan,$riwayat_alergi,$keadaan_umum,$kesadaran,$E,$V,$M, $suhu,$nadi, $respirasi, $TD, $pemeriksaan, $penunjang, $diagnosa_kerja, $diagnosa_banding, $pelayanan, $nama_dr, $poli)
	{		

		$stmt = $this->con->prepare("SELECT * FROM pasien WHERE id_pendaftaran=".$id);
		$stmt->execute();
		$result=$stmt->fetch(PDO::FETCH_ASSOC);		
		$today = date("Y-m-d");
		$diff = date_diff(date_create($result['tgl_lahir']), date_create($today));
		$umur = $diff->format('%y');
		
		// $no_RM = $NIK;
		$stmt = $this->con->prepare("UPDATE rm SET nama=:nama,jen_kelamin=:jen_kelamin,umur=:umur,alamat=:alamat,ruang=:ruang,nama_rs=:nama_rs,mrs=:mrs,jam=:jam,anamnesa=:anamnesa,riwayat_penyakit=:riwayat_penyakit,riwayat_pekerjaan=:riwayat_pekerjaan,riwayat_alergi=:riwayat_alergi,keadaan_umum=:keadaan_umum,kesadaran=:kesadaran,E=:E,V=:V,M=:M,suhu=:suhu, nadi=:nadi, respirasi=:respirasi,TD=:TD,pemeriksaan=:pemeriksaan,penunjang=:penunjang,diagnosa_kerja=:diagnosa_kerja,diagnosa_banding=:diagnosa_banding,pelayanan=:pelayanan,nama_dr=:nama_dr,poli=:poli,update_at=NOW() WHERE id_rm=:id_rm");

		$stmt->bindparam(":id_rm",$id_rm);
		$stmt->bindparam(":nama", $nama);
		$stmt->bindparam(":jen_kelamin", $jen_kelamin);
		$stmt->bindparam(":umur", $umur);
		$stmt->bindparam(":alamat", $alamat);
		$stmt->bindparam(":ruang", $ruang);
		$stmt->bindparam(":nama_rs", $nama_rs);
		$stmt->bindparam(":mrs", $mrs);
		$stmt->bindparam(":jam", $jam);
		$stmt->bindparam(":anamnesa", $anamnesa);
		$stmt->bindparam(":riwayat_penyakit", $riwayat_penyakit);
		$stmt->bindparam(":riwayat_pekerjaan", $riwayat_pekerjaan);
		$stmt->bindparam(":riwayat_alergi", $riwayat_alergi);
		$stmt->bindparam(":keadaan_umum", $keadaan_umum);
		$stmt->bindparam(":kesadaran",$kesadaran);
		$stmt->bindparam(":E", $E);
		$stmt->bindparam(":V", $V);
		$stmt->bindparam(":M", $M);
		$stmt->bindparam(":suhu", $suhu);
		$stmt->bindparam(":nadi", $nadi);
		$stmt->bindparam(":respirasi", $respirasi);
		$stmt->bindparam(":TD", $TD);
		$stmt->bindparam(":pemeriksaan", $pemeriksaan);
		$stmt->bindparam(":penunjang", $penunjang);
		$stmt->bindparam(":diagnosa_kerja", $diagnosa_kerja);
		$stmt->bindparam(":diagnosa_banding", $diagnosa_banding);
		$stmt->bindparam(":pelayanan", $pelayanan);
		$stmt->bindparam(":nama_dr", $nama_dr);
		$stmt->bindparam(":poli", $poli);

		$stmt->execute();
		return $stmt;		
	}

	public function deleteRM($id)
	{
		$stmt = $this->con->prepare("DELETE FROM rm WHERE id_rm=:id");
		$stmt->execute(array(":id"=>$id));
		return $stmt;
	}

	public function getNIK($id){
		$stmt = $this->con->prepare("SELECT NIK, nama from pasien WHERE id_pendaftaran=:id");
		$stmt->execute(array(':id'=>$id));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function isRmExisted($nama,$NIK) {
        $result = $this->con->prepare("SELECT * from rm WHERE nama=:nama AND NIK=:NIK");
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

}

?>