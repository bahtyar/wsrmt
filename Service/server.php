<?php


if(array_key_exists('tag', $_REQUEST) && !empty($_REQUEST['tag'])) {

	$tag = $_REQUEST['tag'];

	require_once '../include/DB_Functions.php';
	$db = new DB_Functions();

	// if (!$db->is_loggedin()==true && $tag != 'login') {
	// 	echo "Belum Login"; return;}

	require_once '../include/DB_Pendaftaran.php';
	require_once '../include/DB_rm.php';
	require_once '../include/DB_diagnosis.php';

	$rm = new RM();
	$daftar = new Pendaftaran();
	$diag = new Diagnosis();

	// response Array
	$response = array();

	if ($tag == 'login') {

		if($_SERVER['REQUEST_METHOD']=='POST'){	

			if(isset($_POST['uname']) && !empty($_POST['uname']) && isset($_POST['password']) && !empty($_POST['password'])){

				$API   = $_POST['API'];
				$uname = $_POST['uname'];
				$umail = $_POST['uname'];
				$upass = $_POST['password'];

				if($db->validateAPI($API)){

					$user = $db->doLogin($uname,$umail,$upass);
					if ($user != false) {
						$user_id = $_SESSION['user_session'];
						$stmt = $db->runQuery("SELECT * FROM user WHERE user_id=:user_id");
						$stmt->execute(array(":user_id"=>$user_id)); 	
						$rows = array();
						if ($stmt) {
							while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$rows['data'] = $result;
							} 
							header('Content-type: application/json; charset=utf-8');
							echo json_encode($rows);
							return ;
						}
					} else {
				// user not found
				// echo json with error = 1
						$rows['data'] = "error";
						$rows['data'] = "Kombinasi username dan password salah!";
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);
						return ;
					}
				} else {echo "API tidak cocok!"; return;}
			} else {echo "Isi username dan password dengan benar!"; return;}
		} else {echo "Gunakan method POST !";}
	} 
		//register user

	else if ($tag == 'register') {
		// Request type is Register new user
		if($_SERVER['REQUEST_METHOD']=='POST'){

			$API  		= $_POST['API'];
			$username	= $_POST['username'];
			$email 		= $_POST['email'];
			$password	= $_POST['password'];
			$nama 		= $_POST['nama'];
			$jabatan 	= $_POST['jabatan'];
			$telpon 	= $_POST['telpon'];			

			if($db->validateAPI($API)){			
					// check if user is already existed
				if ($db->isUserExisted($email)) {
					// user is already existed - error response
					$response["error"] = 2;
					$response["error_msg"] = "Email user sudah terdaftar";
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($response);					
				} else {
					// store user
					$user = $db->register($username, $email, $password, $nama, $jabatan, $telpon);
					if ($user) {
						$stmt = $db->runQuery("SELECT * FROM user WHERE username=:username");
						$stmt->execute(array(":username"=>$username)); 
						$rows = array();
						if ($user) {
							while ($result = $stmt->fetch(PDO::FETCH_ASSOC) ) {
								$rows['data'] = $result;
							}
							header('Content-type: application/json; charset=utf-8');
							echo json_encode($rows);							
						}					
					} else {
							// user failed
						$rows['data'] = "error";
						$rows['data'] = "Terjadi kesalahan saat mendaftar!";
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);
					}
				}
			}else {echo "API tidak cocok!"; return;}
		}else {echo "Gunakan method POST !"; return;}			
	} 

	//cek session
	else if ($tag == 'islogin') 
	{ 
		if($_SERVER['REQUEST_METHOD']=='GET'){
			
			$rows = array();
			if($db->is_loggedin())
			{
				$user_id = $_SESSION['user_session'];
				$stmt = $db->runQuery("SELECT * FROM user WHERE user_id=:user_id");
				$stmt->execute(array(":user_id"=>$user_id));
				while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;
				}			
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
				
			}
			else
			{		
				echo "Belum Login";	
				return false;
			}
		}		
	}

	//logout
	else if ($tag == 'logout') {
			// if(!$db->is_loggedin()){echo " belum login";return;}
		if($_SERVER['REQUEST_METHOD']=='POST'){
				// collect value of input field
			$API   = hmtlspecialchars($_POST['API']);

			if($db->validateAPI($API)){

				$logout = $db->logout();
				if ($logout) {
					// user is already existed - error response
					$response["success"] = 1 ;
					$response["success_msg"] = "berhasil logout";
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($response);
				} else {
					// user failed
					$response["error"] = 1;
					$response["error_msg"] = "Terjadi kesalahan pada logout";
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($response);

				}
			}else {echo "API tidak cocok!"; return;}
		}else {echo "Gunakan method POST !"; return;}
	}
		//All pendaftaran

	else if ($tag == 'allPendaftaran') {
		// if(!$db->is_loggedin()){echo " belum login";return;}
		if($_SERVER['REQUEST_METHOD']=='GET'){
				// collect value of input field		

			$stmt = $daftar->getAllPendaftaran();
			$rows = array();

			if ($stmt) {						
				while ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;		
				}						
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);			
			}
			else{
				//user failed
				$response["error"] = 1;
				$response["error_msg"] = "Data tidak dapat ditemukan";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
			}				
		}else {echo "Gunakan method GET !"; return;}
	}

	//get pendaftaran by id
	else if ($tag == 'PendaftaranById') {

		if($_SERVER['REQUEST_METHOD']=='GET'){

			$id = $_GET['id'];	

			$stmt = $daftar->getById($id);
			$rows = array();

			if ($stmt) {						
				while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;		
				}						
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);			
			}
			else{
				//user failed
				$response["error"] = 1;
				$response["error_msg"] = "Data tidak dapat ditemukan";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
			}				
		}else {echo "Gunakan method GET !"; return;}
	}

		//insert pendaftaran
	else if ($tag == 'insertPendaftaran') {

		if($_SERVER['REQUEST_METHOD']=='POST'){

				// collect value of input field
			$API 			= $_POST['API'];
			$nama 			= $_POST['nama'];
			$NIK 			= $_POST['NIK'];
			$jen_kelamin 	= $_POST['jen_kelamin'];
			$temp_lahir 	= $_POST['temp_lahir'];
			$tgl_lahir 		= $_POST['tgl_lahir'];
			$alamat 		= $_POST['alamat'];
			$status 		= $_POST['status'];
			$pekerjaan		= $_POST['pekerjaan'];
			$jabatan 		= $_POST['jabatan'];
			$lama_kerja 	= $_POST['lama_kerja'];
			$agama 			= $_POST['agama'];
			$suku 			= $_POST['suku'];
			$telp 			= $_POST['telp'];
			$jenis_pasien 	= $_POST['jenis_pasien'];
			$nama_rs 		= $_POST['nama_rs'];

			if($daftar->validateAPI($API)){

				$row = $daftar->isPendaftaranExisted($nama,$NIK);
				if ($row) {			

					$response["error"] = 1;
					$response["error_msg"] = "Pasien sudah terdaftar";
					echo json_encode($response);
				} else {
					$pendaftaran = $daftar->insertPendaftaran($nama,$NIK,$jen_kelamin,$temp_lahir,$tgl_lahir,$alamat,$status,$pekerjaan,$jabatan,$lama_kerja,$agama,$suku,$telp,$jenis_pasien,$nama_rs);

					if ($pendaftaran) {

						$stmt = $db->runQuery("SELECT * FROM pasien WHERE nama=:nama AND NIK=:NIK");
						$stmt->execute(array(":nama"=>$nama, ':NIK'=>$NIK)); 
						$rows = array();

						while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$rows['data'] = $result;
						}						
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);
					} else {
						// user failed
						$rows['data'] = "error";
						$rows['data'] = "Terjadi kesalahan pendaftaran";
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);
					}
				} 
			}else {echo "API tidak cocok!"; return;}
		}else {echo "Gunakan method POST !"; return;}
	}			
		//update pendaftaran
	else if ($tag == 'updatePendaftaran') {

		if($_SERVER['REQUEST_METHOD']=='POST'){

			// collect value of input field
			$API   			= $_POST['API'];
			$id 			= $_POST['id'];				
			$nama 			= $_POST['nama'];
			$NIK 			= $_POST['NIK'];
			$jen_kelamin 	= $_POST['jen_kelamin'];
			$temp_lahir 	= $_POST['temp_lahir'];
			$tgl_lahir 		= $_POST['tgl_lahir'];
			$alamat 		= $_POST['alamat'];
			$status 		= $_POST['status'];
			$pekerjaan		= $_POST['pekerjaan'];
			$jabatan 		= $_POST['jabatan'];
			$lama_kerja 	= $_POST['lama_kerja'];
			$agama 			= $_POST['agama'];
			$suku 			= $_POST['suku'];
			$telp 			= $_POST['telp'];
			$jenis_pasien 	= $_POST['jenis_pasien'];
			$nama_rs 		= $_POST['nama_rs'];

			if($daftar->validateAPI($API)){

				$row = $daftar->updatePendaftaran($id, $nama, $NIK, $jen_kelamin, $temp_lahir, $tgl_lahir, $alamat,$status, $pekerjaan, $jabatan, $lama_kerja, $agama, $suku, $telp, $jenis_pasien,$nama_rs);
				if ($row) {			

					$stmt = $daftar->runQuery("SELECT * FROM pasien WHERE nama=:nama AND NIK=:NIK");
					$stmt->execute(array(":nama"=>$nama, ':NIK'=>$NIK)); 
					$rows = array();
					while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$rows['data'] = $result;
					}	
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($rows);
				} else {

				// user failed
					$rows['data'] = "error";
					$rows['data'] = "Terjadi kesalahan ketika update";
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($rows);
				}
			} else {echo "API tidak cocok!"; return;}
		}else {echo "Gunakan method POST !"; return;}
	}	
	//delete pendaftaran
	else if ($tag == 'deleteDaftar') {

		if($_SERVER['REQUEST_METHOD']=='GET'){				

			$id = $_GET['id'];			

			$row = $daftar->deletePendaftaran($id);
			if ($row) {

				$response["success"] 	= 1;
				$response["msg"]		= "data ".$id." telah di hapus";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
				return;
			} else {

			// user failed
				$response["error"] = 1;
				$response["error_msg"] = "Terjadi kesalahan saat menghapus!";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
				return ;
			}			
		}else {echo "Gunakan method POST !"; return;}
	}

	//all RM
	else if ($tag == 'allRm') {

		if($_SERVER['REQUEST_METHOD']=='GET'){			

			$stmt = $rm->getAllRm();
			$rows = array();

			if ($stmt) {
				while ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;
				}	
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}
			else{
			//user failed
				$rows['data'] = "error";
				$rows['data'] = "Data tidak dapat ditemukan";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}			
		}else {echo "Gunakan method GET !"; return;}
	}

	//get rm by id_daftar
	else if ($tag == 'RmById') {
		if($_SERVER['REQUEST_METHOD']=='GET'){			

			$id = $_GET['id'];

			$row = $rm->getRmbyId($id);
			$rows = array();

			if ($row) {
				while ($result = $row->fetchAll(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;
				}
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows); return;				
			} else if($row==null){
					// user failed
				$rows['data'] = "error";
				$rows['data'] = "pasien  belum memiliki rekam medis";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}			
		}else {echo "Gunakan method POST !"; return;}
	}

	// get rm by id_rm
	else if ($tag == 'RmByNum') {
		if($_SERVER['REQUEST_METHOD']=='GET'){			

			$id_rm = $_GET['rm'];

			$row = $rm->getByNum($id_rm);
			$rows = array();

			if ($row) {
				while ($result = $row->fetch(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;
				}
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows); return;				
			} else {
					// user failed
				$rows['data'] = "error";
				$rows['data'] = "pasien  belum memiliki rekam medis";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}			
		}else {echo "Gunakan method GET !"; return;}
	}

	//insert RM
	else if ($tag == 'insertRM') {
		if($_SERVER['REQUEST_METHOD']=='POST'){

			$API  				= $_POST['API'];
			$id 				= $_POST['id'];			
			$ruang 				= $_POST['ruang'];
			$nama_rs			= $_POST['nama_rs'];
			$mrs 				= $_POST['mrs'];
			$jam				= $_POST['jam'];
			$anamnesa 			= $_POST['anamnesa'];
			$riwayat_penyakit	= $_POST['riwayat_penyakit'];
			$riwayat_pekerjaan	= $_POST['riwayat_pekerjaan'];
			$riwayat_alergi		= $_POST['riwayat_alergi'];
			$keadaan_umum 		= $_POST['keadaan_umum'];
			$kesadaran			= $_POST['kesadaran'];
			$E 					= $_POST['E'];
			$V 					= $_POST['V'];
			$M 					= $_POST['M'];	
			$suhu 				= $_POST['suhu'];
			$nadi				= $_POST['nadi'];
			$respirasi			= $_POST['respirasi'];
			$TD					= $_POST['TD'];
			$pemeriksaan 		= $_POST['pemeriksaan'];
			$penunjang 			= $_POST['penunjang'];
			$diagnosa_kerja 	= $_POST['diagnosa_kerja'];
			$diagnosa_banding 	= $_POST['diagnosa_banding'];
			$pelayanan 			= $_POST['pelayanan'];
			$nama_dr 			= $_POST['nama_dr'];
			$poli 				= $_POST['poli'];			

			if($daftar->validateAPI($API)){
					//ambil data NIK dari pendaftaran
				$NIK = $rm->getNIK($id);
					//cek nama dan NIK apa sudah terdaftar di RM
				
				if ($NIK!=null) {

					$execute = $rm->insertRM($id,$ruang, $nama_rs, $mrs,$jam,$anamnesa,$riwayat_penyakit,$riwayat_pekerjaan,$riwayat_alergi,$keadaan_umum,$kesadaran,$E,$V,$M, $suhu, $nadi, $respirasi, $TD, $pemeriksaan, $penunjang, $diagnosa_kerja, $diagnosa_banding, $pelayanan, $nama_dr, $poli);

					if ($execute) {

						$stmt = $rm->runQuery("SELECT * from rm WHERE id_pendaftaran=:id");
						$stmt->execute(array(':id'=>$id));
						$rows = array();

						while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$rows['data'] = $result;
						}
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);

					} else {echo "Terjadi kesalahan saat membuat rekam medis!";}
				} else {

					// user failed
					$rows['data'] = "error";
					$rows['data'] = "Pasien belum memiliki rekam medis!";
					header('Content-type: application/json; charset=utf-8');
					echo json_encode($rows);
				}
			} else {echo "API tidak cocok!"; return;}	
		}else {echo "Gunakan method POST !"; return;}	
	}

		//updateRM
	else if ($tag == 'updateRM') {
		if($_SERVER['REQUEST_METHOD']=='POST'){

			$id_rm 				= $_POST['id_rm'];
			$id 				= $_POST['id'];
			$no_RM				= $_POST['no_RM'];
			$nama 				= $_POST['nama'];
			$jen_kelamin 		= $_POST['jen_kelamin'];
			$alamat 			= $_POST['alamat'];
			$ruang 				= $_POST['ruang'];				
			$nama_rs 			= $_POST['nama_rs'];
			$mrs 				= $_POST['mrs'];
			$jam				= $_POST['jam'];
			$anamnesa 			= $_POST['anamnesa'];
			$riwayat_penyakit	= $_POST['riwayat_penyakit'];
			$riwayat_pekerjaan	= $_POST['riwayat_pekerjaan'];
			$riwayat_alergi		= $_POST['riwayat_alergi'];
			$keadaan_umum 		= $_POST['keadaan_umum'];
			$kesadaran			= $_POST['kesadaran'];
			$E 					= $_POST['E'];
			$V 					= $_POST['V'];
			$M 					= $_POST['M'];	
			$suhu 				= $_POST['suhu'];
			$nadi				= $_POST['nadi'];
			$respirasi			= $_POST['respirasi'];
			$TD					= $_POST['TD'];
			$pemeriksaan 		= $_POST['pemeriksaan'];
			$penunjang 			= $_POST['penunjang'];
			$diagnosa_kerja 	= $_POST['diagnosa_kerja'];
			$diagnosa_banding 	= $_POST['diagnosa_banding'];
			$pelayanan 			= $_POST['pelayanan'];
			$nama_dr 			= $_POST['nama_dr'];
			$poli 				= $_POST['poli'];
			$API  				= $_POST['API'];

			if($daftar->validateAPI($API)){
			//cari data RM dari nomor
				$number = $rm->getByNum($no_RM);
				if ($number!=null) {

					$row = $rm->updateRM($id_rm, $id, $nama,$jen_kelamin,$alamat,$ruang,$nama_rs,$mrs,$jam,$anamnesa,$riwayat_penyakit,$riwayat_pekerjaan,$riwayat_alergi,$keadaan_umum,$kesadaran,$E,$V,$M, $suhu,$nadi, $respirasi, $TD, $pemeriksaan, $penunjang, $diagnosa_kerja, $diagnosa_banding, $pelayanan, $nama_dr, $poli); 

					if ($row) {

						$stmt = $rm->runQuery("SELECT * from rm WHERE id_rm=:id_rm");
						$stmt->execute(array(':id_rm'=>$id_rm));
						$rows = array();

						while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$rows['data'] = $result;
						}										
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);

					} else {
						$rows['data'] = "error";
						$rows['data'] = "Terjadi kesalahan saat update rekam medis!";
						header('Content-type: application/json; charset=utf-8');
						echo json_encode($rows);						
					}
				}else{echo "nomor rekam medis tidak ditemukan !";return;}	
			} else {echo "API tidak cocok!";return;}	
		}else {echo "Gunakan method POST !"; return;}
	}

	//delete RM
	else if ($tag == 'deleteRM') {			
		if($_SERVER['REQUEST_METHOD']=='GET'){			

			$id_rm = $_GET['id'];	

			$row = $rm->deleteRM($id_rm);
			if ($row) {

				$response["success"] 	= 1;
				$response["msg"]		= "data telah di hapus";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response);
				return ;
			} else {

			// user failed
				$response["error"] = 1;
				$response["error_msg"] = "Terjadi kesalahan saat menghapus!";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($response); return;
			}			
		}else {echo "Gunakan method GET !"; return;}
	}

		//get all dkiagnosis
	else if ($tag == 'allDiag') {

		if($_SERVER['REQUEST_METHOD']=='GET'){			

			$stmt = $diag->allDiagnosis();
			$rows = array();

			if ($stmt) {
				while ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
					$rows['data'] = $result;
				}				

				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}
			else{
			//user failed
				$rows['data'] = "error";
				$rows['data'] = "Data tidak dapat ditemukan";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}			
		} else {echo "Gunakan method GET !"; return;}
	}

		//get diagnosis by category
	else if ($tag == 'diagCat') {
		if($_SERVER['REQUEST_METHOD']=='GET'){			

			$cat = $_GET['category'];			

			$row = $diag->diagCat($cat);
			$rows = array();

			if ($row) {

				while ($stmt = $row->fetchAll(PDO::FETCH_ASSOC)) {
					$rows['data'] = $stmt;
				}
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}
			else {
			// user failed
				$rows['data'] = "error";;
				$rows['data'] = "Category tidak ditemukan";
				header('Content-type: application/json; charset=utf-8');
				echo json_encode($rows);
			}			
		} else {echo "Gunakan method GET !"; return;}
	}

	else {
		echo "Invalid Request";
	}
} else {
	echo "Access Denied";
}
?>
