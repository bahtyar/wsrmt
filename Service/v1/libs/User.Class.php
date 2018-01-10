<?php

/**
* @desc get user lists
*
*/
function getUsers() {
	global $_db;

	$query = "SELECT * FROM users";

	$result = $_db->query($query);

	if(!$result) {
		die('Veritabani hatasi: '.$_db->error);
	}

	//MYSQLI_ASSOC
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$rows[] = $row;
	}

	//die(var_export($rows));
	
	$result->free();

	if(empty($rows))
		return 0;

	return $rows;
}

/**
* @desc get user
* @param int $user_id user id
* @return user info
*/
function getUser($user_id) {
	global $_db;

	$query = "SELECT * FROM users WHERE id='" . $user_id . "'";

	$result = $_db->query($query);

	if(!$result) {
		die('Veritabani hatasi: '.$_db->error);
	}
	
	// MYSQLI_ASSOC
	$data = $result->fetch_array(MYSQLI_ASSOC);
	
	$result->free();

	if(empty($data))
		return 0;

	return $data;
}

/**
* @desc is there user
*
*/
function isUser($email, $password = false) {
	global $_db;

	if($password!=false){
		$query = "SELECT * FROM users WHERE email = '" . $email . "' AND password = '" . cryptPassword($password) . "'";
	} else {
		$query = "SELECT * FROM users WHERE email = '" . $email . "'";
	}

	$result = $_db->query($query);

	if(!$result) {
		die('Veritabani hatasi: '.$_db->error);
	}
	//MYSQLI_ASSOC
	$data = $result->fetch_array(MYSQLI_ASSOC);
	// die(var_export($data));
	//echo var_dump($client_id).'<br>';
	$result->free();

	if(empty($data))
		return 0;

	return $data;
}

function deleteUser($user_id) {
	global $_db;
	/* delete user */
	$query = "DELETE FROM users WHERE id='". $user_id ."'";

	$delete_result = $_db->query($query);

	if(!$delete_result) {
		return array(false, 'Geçici hata, lütfen tekrar deneyiniz.');
	}

	return true;
}

function addUser($data) {
	global $_db;
			
	/* add user */
	$query = "INSERT INTO users SET `name` = '" . $data['name'] . "', surname='" . $data['surname'] . "', 
			email='" . $data['email'] . "', password='" . cryptPassword($data['password']) . "', phone='" . $data['phone'] . "'";

	$report_add_result = $_db->query($query);
	
	if(!$report_add_result) {
		die('Veritabani hatasi: '.$_db->error);
	}

	return $_db->insert_id;
}

function editUser($user_id, $data) {
	global $_db;
			
	/* edit user */
	$query = "UPDATE users SET `name` = '" . $data['name'] . "', surname='" . $data['surname'] . "', 
			email='" . $data['email'] . "', password='" . cryptPassword($data['password']) . "', phone='" . $data['phone'] . "' 
			WHERE `id` = '" . $user_id . "'";

	$report_add_result = $_db->query($query);
	
	if(!$report_add_result) {
		die('Veritabani hatasi: '.$_db->error);
	}

	return $user_id;
}

function cryptPassword($password) {
    return hash("sha256", $password . PASSWORD_SALT);
}