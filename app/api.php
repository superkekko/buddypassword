<?php
class api extends authentication {
	function beforeroute($f3) {}

	function afterroute($f3) {}

	function read($f3) {
		if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
			list($tokenType, $token) = explode(' ', $authorizationHeader);

			if ($tokenType === 'Bearer' && !empty($token)) {
				$user = $f3->get('DB')->exec("SELECT * FROM user where bearer=?", $token);
				
				if(!empty($user[0])){
					$postData = json_decode(file_get_contents('php://input'), true);

					if ($postData !== null) {
						$result = $f3->get('DB')->exec("SELECT * FROM password WHERE link = ?", $postData['url']);
						
						$return_array=['data'=> array('list'=>$result[0]['list'],'tags'=>$result[0]['tags'])];
						header('Content-Type: application/json');
						echo json_encode($return_array);
					} else {
						$return_array=['data'=> array('list'=>'','tags'=>'')];
						header('Content-Type: application/json');
						echo json_encode($return_array);
					}
				}
			} else {
				header('Content-Type: application/json');
				http_response_code(400);
			}
		} else {
			header('Content-Type: application/json');
			http_response_code(400);
		}
	}	
}