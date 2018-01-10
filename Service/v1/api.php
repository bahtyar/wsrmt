<?php

// TODO:: Debug Mode
error_reporting(E_ALL);
ini_set("display_errors", 1);

mb_internal_encoding("utf8");
date_default_timezone_set('Europe/Istanbul');
// die(var_dump($_REQUEST));

// includes
include_once __DIR__.'/config/config.php';
include_once __DIR__.'/libs/Output.Class.php';
include_once __DIR__.'/libs/User.Class.php';

// DB Connect
$_db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
$_db->query("SET NAMES 'utf8'");
$_db->query("SET CHARACTER SET utf8");
$_db->query("SET COLLATION_CONNECTION = 'utf8_turkish_ci'");

// if there is request
if(array_key_exists('request', $_GET) && !empty($_GET['request'])) {

	// for debug
	error_log('Request Method : ' . print_r($_SERVER['REQUEST_METHOD'], true));
	error_log('Request : ' . print_r($_GET, true));
	
	// TODO:: Url parser
	$request = explode('/', $_GET['request']);

	switch ($request[0]) {
		case 'users':

			// if there is param id
			if(isset($request[1])) {

				//request[1] : mengambil nilai id user dari request yang dikirim form
				$user_id = $request[1];

				if(!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'DELETE', 'GET'])){
					header('HTTP/1.1 405 Method Not Allowed');
		      		header('Allow: GET, PUT, DELETE');
		      		Output::error('Method not exist');
				}

				if(!isset($user_id) || empty($user_id) || !is_numeric($user_id)) {
					header('HTTP/1.1 405 Reset Content');
					Output::error('Kullanıcı numarasını kontrol ediniz');
				}

				// processing for user
				// update user
				if($_SERVER['REQUEST_METHOD']=='PUT'){
					
					$error = array();

					$post = json_decode(file_get_contents("php://input"),true);

					// TODO:: Validate post 
					if(!isset($post['email']) || empty($post['email']) || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
						$error[] = 'E-posta adresinizi kontrol ediniz.';
					} 

					if(!isset($post['password']) || empty($post['password']) ){	
						$error[] = 'Şifrenizi kontrol ediniz.';
					}

					if(isset($error) && !empty($error) && is_array($error)){
						header('HTTP/1.1 405 Reset Content');
						Output::error($error);
					} else {
					
						$result = editUser($user_id, $post);
					}
				}

				// delete user
				if($_SERVER['REQUEST_METHOD']=='DELETE'){
					$result = deleteUser($user_id);
				}

				// get user
				if($_SERVER['REQUEST_METHOD']=='GET'){
					$result = getUser($user_id);
				}

				// if method does not found
				if(!$result) {
					$data = 'Tekrar deniyiniz!';
					header('HTTP/1.1 405 Reset Content');
					Output::error($data);
				} else {

					if($_SERVER['REQUEST_METHOD']=='PUT')
						$data = getUser($result);
					else if($_SERVER['REQUEST_METHOD']=='DELETE')
						$data = 'Kullanici Silindi.';
					else	
						$data = $result;

					header('HTTP/1.1 200 OK');
					Output::success($data);
				}

			} else {

				// add user
				if($_SERVER['REQUEST_METHOD']=='POST'){
				
					$error = array();

					$post = json_decode(file_get_contents("php://input"),true);
					// die(var_dump($post));

					// TODO:: Validate post 
					if(!isset($post['email']) || empty($post['email']) || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
						$error[] = 'E-posta adresinizi kontrol ediniz.';
					} 

					if(!isset($post['password']) || empty($post['password']) ){	
						$error[] = 'Şifrenizi kontrol ediniz.';
					}

					// is there user
					$data = isUser($post['email']);

					if($data!=0){
						$error[] = 'Kayıtlı e-posta adresi.';
					}

					if(isset($error) && !empty($error) && is_array($error)){
						header('HTTP/1.1 405 Reset Content');
						Output::error($error);
					} else {
						
						// add user to db
						$result = addUser($post);

						if(!$result) {
							$data = 'Tekrar deniyiniz!';
							header('HTTP/1.1 405 Reset Content');
							Output::error($data);
						} else {
							$data = getUser($result);
							header('HTTP/1.1 201 Created');
							Output::success($data);
						}

					}

				// get users
				} else if($_SERVER['REQUEST_METHOD']=='GET') {
					$data = getUsers();
					header('HTTP/1.1 200 OK');
					Output::success($data);
				} else {
					header('HTTP/1.1 405 Method Not Allowed');
		      		header('Allow: GET, POST');
		      		Output::error('Method not exist');
				}
				
			}

			break;

		case 'login':
			
			if($_SERVER['REQUEST_METHOD']=='POST'){
				
				$post = json_decode(file_get_contents("php://input"),true);
				// die(var_dump($post));

				// TODO:: Validate post 
				if(isset($post['email']) && !empty($post['email']) && isset($post['password']) && !empty($post['password']) ){					
					$data = isUser($post['email'],$post['password']);
					
					if($data!=0){
						header('HTTP/1.1 200 OK');
						Output::success($data);
					} else {
						$data = 'Kullanıcı bilgilerinizi kontrol ediniz!';
						header('HTTP/1.1 203 Non-Authoritative Information');
						Output::error($data);
					}

				} else {
					$data = 'Kullanıcı bilgilerinizi gönderiniz!';
					header('HTTP/1.1 405 Method Not Allowed');
	      			header('Allow: POST');	
	      			Output::error($data);
				}
			} else {
				$data = 'Lutfen post metodunu kullanin!';
				header('HTTP/1.1 405 Method Not Allowed');
      			header('Allow: POST');
      			Output::error($data);
			}

			break;

		case 'help':
			header('HTTP/1.1 200 OK');
			header('Content-type: text/html; charset=utf-8');
			echo 'http://basic-web-service.com/api/v1/api.php?request=users'.'<br>';
			echo 'http://basic-web-service.com/api/v1/api.php?request=users/1'.'<br>';
			echo '
			<br><br>
			Login Request POST Metod<br>
			Example parameters <br>
			email=adem.arass@gmail.com | password=123456<br>
			Url <br>
			http://basic-web-service.com/api/v1/api.php?request=login'.'<br>';
			exit();
			break;
		
		default:
			header('HTTP/1.1 405 Method Not Allowed');
      		header('Allow: GET, PUT, DELETE');
      		Output::error('Method not exist');
			break;
	}
	
	Output::success($data);
} else {
	header('HTTP/1.1 404 Not Found');
	Output::error('Method not exist');
}


/*
Examle Requests

Add user (api/v1/api.php?request=users)
{"name":"Adem","surname":"Aras","email":"example@example.com","phone":123456789,"password":123456}

User login (api/v1/api.php?request=login)
{"email":"example@example.com","password":123456}

User update (api/v1/api.php?request=users/2)
{"name":"Adem","surname":"Aras","email":"example@example.com","phone":123456789,"password":123456}

User get (api/v1/api.php?request=users/2)

User delete (api/v1/api.php?request=users/2)

*/