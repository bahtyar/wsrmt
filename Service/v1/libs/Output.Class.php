<?php

class Output {

	public static function success($data) {

		$json = [
			'status' => true,
			// english
			// 'message' => 'İşleminiz başarıyla gerçekleştirildi.',
			// turkish
			'message' => 'İşleminiz başarıyla gerçekleştirildi.',
			'data' => $data,
		];
		
		header('Content-type: application/json; charset=utf-8');
		exit(json_encode($json));
	}

	public static function error($data) {

		$json = [
			'status' => false,
			// english
			// 'message' => 'İşleminizde hata oluştu.',
			// turkish
			'message' => 'İşleminizde hata oluştu.',
			'data' => $data,
		];
		
		header('Content-type: application/json; charset=utf-8');
		exit(json_encode($json));
	}

}