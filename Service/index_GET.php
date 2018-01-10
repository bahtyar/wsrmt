<?php



if (isset($_REQUEST['tag'])  != '') {

	// get tag

	$tag = $_REQUEST['tag'];

	require_once '../include/DB_Functions.php';
	$db = new DB_Functions();
	//cek session dan cek login	

	// if ($db->is_loggedin()!=true && $tag != 'login') {
	// 	echo "Belum Login"; return;}


	// include db handler

		require_once '../include/DB_Pendaftaran.php';
		require_once '../include/DB_rm.php';
		require_once '../include/DB_diagnosis.php';

		$rm = new RM();
		$daftar = new Pendaftaran();
		$diag = new Diagnosis();



	// response Array
		$response = array();

	// login user

		if ($tag == 'login') {


		// Request type is check Login
			$umail = $_GET['uname'];
			$upass = $_GET['password'];
			$uname = $_GET['uname'];
			$API   = $_GET['API'];

			if($db->validateAPI($API)){
		// check for user

				$user = $db->doLogin($uname,$umail,$upass);
				if ($user != false) {
					$user_id = $_SESSION['user_session'];
					$stmt = $db->runQuery("SELECT * FROM user WHERE user_id=:user_id");
					$stmt->execute(array(":user_id"=>$user_id)); 			
					$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

					$response["success"] = 1;
					$response["user"]["id"] 	  = $userRow['user_id'];
					$response["user"]["username"] = $userRow['username'];
					$response["user"]["email"]	  = $userRow['email'];
					echo json_encode($response);
				} else {
			// user not found
			// echo json with error = 1
					$response["error"] = 1;
					$response["error_msg"] = "Salah username dan password!";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}
		} 
		//register user

		else if ($tag == 'register') {
		// Request type is Register new user
			$username	= $_GET['username'];
			$email 		= $_GET['mail'];
			$password	= $_GET['pass'];
			$nama 		= $_GET['nama'];
			$jabatan 	= $_GET['jabatan'];
			$telpon 	= $_GET['telpon'];

			$API   = $_GET['API'];

			if($db->validateAPI($API)){

			// if($db->validateAPI($API)){
		// check if user is already existed
				if ($db->isUserExisted($email)) {
			// user is already existed - error response
					$response["error"] = 2;
					$response["error_msg"] = "Email user sudah terdaftar";
					echo json_encode($response);
				} else {
			// store user
					$user = $db->register($username, $email, $password, $nama, $jabatan, $telpon);
					if ($user) {
						$stmt = $db->runQuery("SELECT * FROM user WHERE username=:username");
						$stmt->execute(array(":username"=>$username)); 			
						$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

				// user stored successfully
						$response["success"] = 1;
						$response["id"]					= $userRow["user_id"];
						$response["user"]["name"] 		= $userRow["username"];
						$response["user"]["email"] 		= $userRow["email"];
						$response["user"]["nama"] 		= $userRow["nama"];
						$response["user"]["jabatan"] 	= $userRow["jabatan"];
						$response["user"]["telpon"] 	= $userRow["telpon"];
						$response["user"]["created_at"] = $userRow["created_at"];
						echo json_encode($response);
					} else {
				// user failed
						$response["error"] = 1;
						$response["error_msg"] = "Error occurred in Registration";
						echo json_encode($response);
					}
				}
			}else {echo "API tidak cocok!";}
		} 
	//cek session
		elseif ($tag == 'islogin') {if($db->is_loggedin()){echo "ya";}else{echo "tidak";};}

	//logout
		else if ($tag == 'logout') {
			if(!$db->is_loggedin()){echo " belum login";return;}

			$API   = $_GET['API'];

			if($daftar->validateAPI($API)){

				$logout = $db->logout();
				if ($logout) {
			// user is already existed - error response
					$response["success"] = 1 ;
					$response["success_msg"] = "berhasil logout";
					echo json_encode($response);
				} else {
			// user failed
					$response["error"] = 1;
					$response["error_msg"] = "Terjadi kesalahan pada logout";
					echo json_encode($response);

				}
			}else {echo "API tidak cocok!";}
		}
		//All pendaftaran

		elseif ($tag == 'allPendaftaran') {
		// if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			if($daftar->validateAPI($API)){

				$stmt = $daftar->getAllPendaftaran();

				if ($stmt) {
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					foreach ($result as $row) {
						$response["success"] = 1;
						$response["id"]				= $row["id_pendaftaran"];
						$response["nama"] 			= $row["nama"];
						$response["NIK"] 			= $row["NIK"];
						$response["jenis kelamin"] 	= $row["jen_kelamin"];
						$response["tgl_lahir"] 		= $row["tgl_lahir"];
						$response["alamat"] 		= $row["alamat"];
						$response["jenis_pasien"] 	= $row["jenis_pasien"];
						$response["nama_rs"] 		= $row["nama_rs"];
						
						echo json_encode($response);
					}

				}
				else{
				//user failed
					$response["error"] = 1;
					$response["error_msg"] = "Data tidak dapat ditemukan";
					echo json_encode($response);
				}
			}else {echo "API tidak cocok!";}

		}
		//insert pendaftaran
		else if ($tag == 'insertPendaftaran') {
			if(!$db->is_loggedin()){echo " belum login";return;}

			$API   = $_GET['API'];

			$nama 			= $_GET['nama'];
			$NIK 			= $_GET['NIK'];
			$jen_kelamin 	= $_GET['jen_kelamin'];
			$temp_lahir 	= $_GET['temp_lahir'];
			$tgl_lahir 		= $_GET['tgl_lahir'];
			$alamat 		= $_GET['alamat'];
			$status 		= $_GET['status'];
			$pekerjaan		= $_GET['pekerjaan'];
			$jabatan 		= $_GET['jabatan'];
			$lama_kerja 	= $_GET['lama_kerja'];
			$agama 			= $_GET['agama'];
			$suku 			= $_GET['suku'];
			$telp 			= $_GET['telp'];
			$jenis_pasien 	= $_GET['jenis_pasien'];
			$nama_rs 		= $_GET['nama_rs'];
			$poli 			= $_GET['poli'];

			if($daftar->validateAPI($API)){

				$row = $daftar->isPendaftaranExisted($nama,$NIK);
				if ($row) {			

					$response["error"] = 1;
					$response["error_msg"] = "Pasien sudah terdaftar";
					echo json_encode($response);
				} else {
					$pendaftaran = $daftar->insertPendaftaran($nama,$NIK,$jen_kelamin,$temp_lahir,$tgl_lahir,$alamat,$status,$pekerjaan,$jabatan,$lama_kerja,$agama,$suku,$telp,$jenis_pasien,$nama_rs, $poli);
					if ($pendaftaran) {

						$stmt = $db->runQuery("SELECT * FROM pasien WHERE nama=:nama AND NIK=:NIK");
						$stmt->execute(array(":nama"=>$nama, ':NIK'=>$NIK)); 			
						$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
				// user stored successfully
						$response["success"] = 1;
						$response["id"]						= $userRow["id_pendaftaran"];
						$response["user"]["nama"] 			= $userRow["nama"];
						$response["user"]["NIK"] 			= $userRow["NIK"];
						$response["user"]["jenis kelamin"] 	= $userRow["jen_kelamin"];
						$response["user"]["tgl_lahir"] 		= $userRow["tgl_lahir"];
						$response["user"]["alamat"] 		= $userRow["alamat"];
						$response["user"]["jenis_pasien"] 	= $userRow["jenis_pasien"];
						$response["user"]["nama_rs"] 		= $userRow["nama_rs"];
						echo json_encode($response);
					} else {
				// user failed
						$response["error"] = 1;
						$response["error_msg"] = "Terjadi kesalahan pendaftaran";
						echo json_encode($response);
					}
				} 
			}else {echo "API tidak cocok!";}
		}			
		//update pendaftaran
		else if ($tag == 'updatePendaftaran') {
			if(!$db->is_loggedin()){echo " belum login";return;}

			$API   = $_GET['API'];


			$id 			= $_GET['id'];
			$nama 			= $_GET['nama'];
			$NIK 			= $_GET['NIK'];
			$jen_kelamin 	= $_GET['jen_kelamin'];
			$temp_lahir 	= $_GET['temp_lahir'];
			$tgl_lahir 		= $_GET['tgl_lahir'];
			$alamat 		= $_GET['alamat'];
			$status 		= $_GET['status'];
			$pekerjaan		= $_GET['pekerjaan'];
			$jabatan 		= $_GET['jabatan'];
			$lama_kerja 	= $_GET['lama_kerja'];
			$agama 			= $_GET['agama'];
			$suku 			= $_GET['suku'];
			$telp 			= $_GET['telp'];
			$jenis_pasien 	= $_GET['jenis_pasien'];
			$nama_rs 		= $_GET['nama_rs'];
			$poli 			= $_GET['poli'];

			if($daftar->validateAPI($API)){

				$row = $daftar->updatePendaftaran($id, $nama, $NIK, $jen_kelamin, $temp_lahir, $tgl_lahir, $alamat,$status, $pekerjaan, $jabatan, $lama_kerja, $agama, $suku, $telp, $jenis_pasien,$nama_rs, $poli);
				if ($row) {			

					$stmt = $daftar->runQuery("SELECT * FROM pasien WHERE nama=:nama AND NIK=:NIK");
					$stmt->execute(array(":nama"=>$nama, ':NIK'=>$NIK)); 			
					$userRow=$stmt->fetch(PDO::FETCH_ASSOC);


					$response["success"] 				= 1;
					$response["id"]						= $userRow["id_pendaftaran"];
					$response["user"]["nama"] 			= $userRow["nama"];
					$response["user"]["NIK"] 			= $userRow["NIK"];
					$response["user"]["jenis kelamin"] 	= $userRow["jen_kelamin"];
					$response["user"]["tgl_lahir"] 		= $userRow["tgl_lahir"];
					$response["user"]["alamat"] 		= $userRow["alamat"];
					$response["user"]["jenis_pasien"] 	= $userRow["jenis_pasien"];
					$response["user"]["nama_rs"] 		= $userRow["nama_rs"];

					echo json_encode($response);
				} else {

				// user failed
					$response["error"] = 1;
					$response["error_msg"] = "Terjadi kesalahan pendaftaran";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}
		}	
	//delete pendaftaran
		else if ($tag == 'deleteDaftar') {
			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			$id = $_GET['id'];

			if($daftar->validateAPI($API)){

				$row = $daftar->deletePendaftaran($id);
				if ($row) {

					$response["success"] 	= 1;
					$response["msg"]		= "data ".$id." telah di hapus";
					echo json_encode($response);
				} else {

			// user failed
					$response["error"] = 1;
					$response["error_msg"] = "Terjadi kesalahan saat menghapus!";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}
		}
	//read rm by id
		else if ($tag == 'RmById') {
			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			$id = $_GET['id'];

			if($daftar->validateAPI($API)){

				$row = $rm->getRmbyId($id);
				if ($row['no_RM']!= null) {

					$response["success"] 					= 1;
					$response["user"]["id"] 				= $row["id_pendaftaran"];
					$response["user"]["no_RM"] 				= $row["no_RM"];
					$response["user"]["nama"] 				= $row["nama"];
					$response["user"]["NIK"] 				= $row["NIK"];
					$response["user"]["jen_kelamin"] 		= $row["jen_kelamin"];
					$response["user"]["umur"] 				= $row["umur"];
					$response["user"]["alamat"] 			= $row["alamat"];
					$response["user"]["ruang"] 				= $row["ruang"];
					$response["user"]["nama_rs"] 			= $row["nama_rs"];
					$response["user"]["mrs"] 				= $row["mrs"];
					$response["user"]["jam"] 				= $row["jam"];
					$response["user"]["anamnesa"] 			= $row["anamnesa"];
					$response["user"]["riwayat_penyakit"] 	= $row["riwayat_penyakit"];
					$response["user"]["riwayat_pekerjaan"] 	= $row["riwayat_pekerjaan"];
					$response["user"]["riwayat_alergi"]		= $row["riwayat_alergi"];
					$response["user"]["keadaan_umum"] 		= $row["keadaan_umum"];
					$response["user"]["kesadaran"] 			= $row["kesadaran"];
					$response["user"]["GCS E"] 				= $row["E"];
					$response["user"]["GCS V"] 				= $row["V"];
					$response["user"]["GCS M"] 				= $row["M"];			
					$response["user"]["suhu"] 				= $row["suhu"];
					$response["user"]["nadi"] 				= $row["nadi"];
					$response["user"]["respirasi"] 			= $row["respirasi"];
					$response["user"]["TD"] 				= $row["TD"];
					$response["user"]["pemeriksaan"] 		= $row["pemeriksaan"];
					$response["user"]["penunjang"] 			= $row["penunjang"];
					$response["user"]["diagnosa_kerja"] 	= $row["diagnosa_kerja"];
					$response["user"]["diagnosa_banding"] 	= $row["diagnosa_banding"];
					$response["user"]["pelayanan"] 			= $row["pelayanan"];
					$response["user"]["nama_dr"] 			= $row["nama_dr"];
					$response["user"]["created_at"] 		= $row["created_at"];
					$response["user"]["update_at"] 			= $row["update_at"];

					echo json_encode($response);
				} else {

			// user failed
					$response["error"] = 1;
					$response["error_msg"] = "pasien  belum memiliki rekam medis";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}
		}
	//insert RM
		else if ($tag == 'insertRM') {
			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			$id 				= $_GET['id'];			
			$ruang 				= $_GET['ruang'];
			$mrs 				= $_GET['mrs'];
			$jam				= $_GET['jam'];
			$anamnesa 			= $_GET['anamnesa'];
			$riwayat_penyakit	= $_GET['riwayat_penyakit'];
			$riwayat_pekerjaan	= $_GET['riwayat_pekerjaan'];
			$riwayat_alergi		= $_GET['riwayat_alergi'];
			$keadaan_umum 		= $_GET['keadaan_umum'];
			$kesadaran			= $_GET['kesadaran'];
			$E 					= $_GET['E'];
			$V 					= $_GET['V'];
			$M 					= $_GET['M'];	
			$suhu 				= $_GET['suhu'];
			$nadi				= $_GET['nadi'];
			$respirasi			= $_GET['respirasi'];
			$TD					= $_GET['TD'];
			$pemeriksaan 		= $_GET['pemeriksaan'];
			$penunjang 			= $_GET['penunjang'];
			$diagnosa_kerja 	= $_GET['diagnosa_kerja'];
			$diagnosa_banding 	= $_GET['diagnosa_banding'];
			$pelayanan 			= $_GET['pelayanan'];	
			$nama_dr 			= $_GET['nama_dr'];

			if($daftar->validateAPI($API)){
			//ambil data NIK dari pendaftaran
				$NIK = $rm->getNIK($id);
			//cek nama dan NIK apa sudah terdaftar di RM
				$check = $rm->isRmExisted($NIK['nama'],$NIK['NIK']);
				if ($check!=true) {

					$row = $rm->insertRM($id,$ruang,$mrs,$jam,$anamnesa,$riwayat_penyakit,$riwayat_pekerjaan,$riwayat_alergi,$keadaan_umum,$kesadaran,$E,$V,$M, $suhu, $nadi, $respirasi, $TD, $pemeriksaan, $penunjang, $diagnosa_kerja, $diagnosa_banding, $pelayanan, $nama_dr);

					if ($row) {

						$stmt = $rm->runQuery("SELECT * from rm WHERE id_pendaftaran=:id");
						$stmt->execute(array(':id'=>$id));
						$userRow = $stmt->fetch(PDO::FETCH_ASSOC);


						$response["success"] 					= 1;
						$response["user"]["id"] 				= $userRow["id_pendaftaran"];
						$response["user"]["no_RM"] 				= $userRow["no_RM"];
						$response["user"]["nama"] 				= $userRow["nama"];
						$response["user"]["NIK"] 				= $userRow["NIK"];
						$response["user"]["jen_kelamin"] 		= $userRow["jen_kelamin"];
						$response["user"]["umur"] 				= $userRow["umur"];
						$response["user"]["alamat"] 			= $userRow["alamat"];
						$response["user"]["ruang"] 				= $userRow["ruang"];
						$response["user"]["nama_rs"] 			= $userRow["nama_rs"];
						$response["user"]["mrs"] 				= $userRow["mrs"];
						$response["user"]["jam"] 				= $userRow["jam"];
						$response["user"]["anamnesa"] 			= $userRow["anamnesa"];
						$response["user"]["riwayat_penyakit"] 	= $userRow["riwayat_penyakit"];
						$response["user"]["riwayat_pekerjaan"] 	= $userRow["riwayat_pekerjaan"];
						$response["user"]["riwayat_alergi"]		= $userRow["riwayat_alergi"];
						$response["user"]["keadaan_umum"] 		= $userRow["keadaan_umum"];
						$response["user"]["kesadaran"] 			= $userRow["kesadaran"];
						$response["user"]["GCS E"] 				= $userRow["E"];
						$response["user"]["GCS V"] 				= $userRow["V"];
						$response["user"]["GCS M"] 				= $userRow["M"];			
						$response["user"]["suhu"] 				= $userRow["suhu"];
						$response["user"]["nadi"] 				= $userRow["nadi"];
						$response["user"]["respirasi"] 			= $userRow["respirasi"];
						$response["user"]["TD"] 				= $userRow["TD"];
						$response["user"]["pemeriksaan"] 		= $userRow["pemeriksaan"];
						$response["user"]["penunjang"] 			= $userRow["penunjang"];
						$response["user"]["diagnosa_kerja"] 	= $userRow["diagnosa_kerja"];
						$response["user"]["diagnosa_banding"] 	= $userRow["diagnosa_banding"];
						$response["user"]["pelayanan"] 			= $userRow["pelayanan"];
						$response["user"]["nama_dr"] 			= $userRow["nama_dr"];
						$response["user"]["created_at"] 		= $userRow["created_at"];
						$response["user"]["update_at"] 			= $userRow["update_at"];

						echo json_encode($response);
					} else {echo "Terjadi kesalahan saat membuat rekam medis!";}
				} else if($check==true) {

					// user failed

					$response["error"] = 1;
					$response["error_msg"] = "Pasien telah memiliki rekam medis!";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}			
		}

		//updateRM
		else if ($tag == 'updateRM') {
			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			$id 				= $_GET['id'];
			$no_RM				= $_GET['no_RM'];			
			$nama 				= $_GET['nama'];
			$jen_kelamin 		= $_GET['jen_kelamin'];
			$alamat 			= $_GET['alamat'];
			$ruang 				= $_GET['ruang'];
			$nama_rs 			= $_GET['nama_rs'];
			$mrs 				= $_GET['mrs'];
			$jam				= $_GET['jam'];
			$anamnesa 			= $_GET['anamnesa'];
			$riwayat_penyakit	= $_GET['riwayat_penyakit'];
			$riwayat_pekerjaan	= $_GET['riwayat_pekerjaan'];
			$riwayat_alergi		= $_GET['riwayat_alergi'];
			$keadaan_umum 		= $_GET['keadaan_umum'];
			$kesadaran			= $_GET['kesadaran'];
			$E 					= $_GET['E'];
			$V 					= $_GET['V'];
			$M 					= $_GET['M'];	
			$suhu 				= $_GET['suhu'];
			$nadi				= $_GET['nadi'];
			$respirasi			= $_GET['respirasi'];
			$TD					= $_GET['TD'];
			$pemeriksaan 		= $_GET['pemeriksaan'];
			$penunjang 			= $_GET['penunjang'];
			$diagnosa_kerja 	= $_GET['diagnosa_kerja'];
			$diagnosa_banding 	= $_GET['diagnosa_banding'];
			$pelayanan 			= $_GET['pelayanan'];	
			$nama_dr 			= $_GET['nama_dr'];

			if($daftar->validateAPI($API)){
			//cari data RM dari nomor
				$result = $rm->getByNum($no_RM);
				if ($result!=null) {

					$row = $rm->updateRM($id,$nama,$jen_kelamin,$alamat,$ruang,$nama_rs,$mrs,$jam,$anamnesa,$riwayat_penyakit,$riwayat_pekerjaan,$riwayat_alergi,$keadaan_umum,$kesadaran,$E,$V,$M, $suhu,$nadi, $respirasi, $TD, $pemeriksaan, $penunjang, $diagnosa_kerja, $diagnosa_banding, $pelayanan, $nama_dr); 

					if ($row) {

						$stmt = $rm->runQuery("SELECT * from rm WHERE no_RM=:no_RM");
						$stmt->execute(array(':no_RM'=>$no_RM));
						$userRow = $stmt->fetch(PDO::FETCH_ASSOC);


						$response["success"] 					= "Berhasil update";
						$response["user"]["id"] 				= $userRow["id_pendaftaran"];
						$response["user"]["no_RM"] 				= $userRow["no_RM"];
						$response["user"]["nama"] 				= $userRow["nama"];
						$response["user"]["NIK"] 				= $userRow["NIK"];
						$response["user"]["jen_kelamin"] 		= $userRow["jen_kelamin"];
						$response["user"]["umur"] 				= $userRow["umur"];
						$response["user"]["alamat"] 			= $userRow["alamat"];
						$response["user"]["ruang"] 				= $userRow["ruang"];
						$response["user"]["nama_rs"] 			= $userRow["nama_rs"];
						$response["user"]["mrs"] 				= $userRow["mrs"];
						$response["user"]["jam"] 				= $userRow["jam"];
						$response["user"]["anamnesa"] 			= $userRow["anamnesa"];
						$response["user"]["riwayat_penyakit"] 	= $userRow["riwayat_penyakit"];
						$response["user"]["riwayat_pekerjaan"] 	= $userRow["riwayat_pekerjaan"];
						$response["user"]["riwayat_alergi"]		= $userRow["riwayat_alergi"];
						$response["user"]["keadaan_umum"] 		= $userRow["keadaan_umum"];
						$response["user"]["kesadaran"] 			= $userRow["kesadaran"];
						$response["user"]["GCS E"] 				= $userRow["E"];
						$response["user"]["GCS V"] 				= $userRow["V"];
						$response["user"]["GCS M"] 				= $userRow["M"];			
						$response["user"]["suhu"] 				= $userRow["suhu"];
						$response["user"]["nadi"] 				= $userRow["nadi"];
						$response["user"]["respirasi"] 			= $userRow["respirasi"];
						$response["user"]["TD"] 				= $userRow["TD"];
						$response["user"]["pemeriksaan"] 		= $userRow["pemeriksaan"];
						$response["user"]["penunjang"] 			= $userRow["penunjang"];
						$response["user"]["diagnosa_kerja"] 	= $userRow["diagnosa_kerja"];
						$response["user"]["diagnosa_banding"] 	= $userRow["diagnosa_banding"];
						$response["user"]["pelayanan"] 			= $userRow["pelayanan"];
						$response["user"]["nama_dr"] 			= $userRow["nama_dr"];
						$response["user"]["created_at"] 		= $userRow["created_at"];
						$response["user"]["update_at"] 			= $userRow["update_at"];

						echo json_encode($response);
					} else {echo "Terjadi kesalahan saat update rekam medis!";}
				}else{echo "Data rekam medis tidak ditemukan";}	
			} else {echo "API tidak cocok!";}		
		}
		//all RM
		else if ($tag == 'allRm') {
			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			if($daftar->validateAPI($API)){

				$stmt = $rm->getAllRm();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($result) {				
					foreach ($result as $row) {
						$response["success"] = 1;
						$response["user"]["no_RM"] 				= $row["no_RM"];
						$response["user"]["umur"] 				= $row["umur"];
						$response["user"]["jen_kelamin"] 		= $row["jen_kelamin"];
						$response["user"]["mrs"] 				= $row["mrs"];
						$response["user"]["nama_rs"] 			= $row["nama_rs"];
						$response["user"]["riwayat_penyakit"] 	= $row["riwayat_penyakit"];
						$response["user"]["diagnosa_kerja"] 	= $row["diagnosa_kerja"];
						$response["user"]["created_at"] 		= $row["created_at"];
						$response["user"]["update_at"] 			= $row["update_at"];
						echo json_encode($response);
					}

				}
				else{
			//user failed
					$response["error"] = 1;
					$response["error_msg"] = "Data tidak dapat ditemukan";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}

		}

	//delete RM
		else if ($tag == 'deleteRM') {
			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];

			$no = $_GET['no_RM'];

			if($daftar->validateAPI($API)){

				$row = $rm->deleteRM($no);
				if ($row) {

					$response["success"] 	= 1;
					$response["msg"]		= "data ".$no." telah di hapus";
					echo json_encode($response);
				} else {

			// user failed
					$response["error"] = 1;
					$response["error_msg"] = "Terjadi kesalahan saat menghapus!";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}
		}

		//get all dkiagnosis
		else if ($tag == 'allDiag') {

			if(!$db->is_loggedin()){echo " belum login";return;}
			$API   = $_GET['API'];
			if($daftar->validateAPI($API)){

				$stmt = $diag->allDiagnosis();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($result) {				
					foreach ($result as $row) {
						$response["success"] = 1;
						$response["user"]["Category"] 	= $row["category"];
						$response["user"]["Sub"] 		= $row["subcategory"];
						$response["user"]["eng"] 		= $row["eng_name"];
						$response["user"]["idn"] 		= $row["ind_name"];
						echo json_encode($response);
					}

				}
				else{
			//user failed
					$response["error"] = 1;
					$response["error_msg"] = "Data tidak dapat ditemukan";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}

		}

		//get diagnosis by category
		else if ($tag == 'diagCat') {
			if(!$db->is_loggedin()){echo " belum login";return;}

			$API   = $_GET['API'];	

			$cat = $_GET['category'];

			if($daftar->validateAPI($API)){

				$row = $diag->diagCat($cat);
				$stmt = $row->fetchAll(PDO::FETCH_ASSOC);
				if ($stmt) {				
					foreach ($stmt as $result) {
						$response["success"] 			= 1;
						$response["user"]["id"] 		= $result["id_diag"];
						$response["user"]["Category"] 	= $result["category"];
						$response["user"]["Sub"] 		= $result["subcategory"];
						$response["user"]["eng"] 		= $result["eng_name"];
						$response["user"]["idn"] 		= $result["ind_name"];

						echo json_encode($response);
					}
				} else {

			// user failed
					$response["error"] = 1;
					$response["error_msg"] = "Category tidak ditemukan";
					echo json_encode($response);
				}
			} else {echo "API tidak cocok!";}
		}

		else {
			echo "Invalid Request";
		}
	} else {
		echo "Access Denied";
	}
	?>
