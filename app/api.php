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

				if (!empty($user[0])) {
					//$postData = json_decode(file_get_contents('php://input'), true);
					$results = $f3->get('DB')->exec("SELECT * FROM password where user_ins = ? or (group_id = ? and share = ?)", array($user[0]['user_id'], $user[0]['group_id'], 1));
					$allitem = [];
					foreach ($results as $result) {
						$result['link'] = str_replace(".", "", $result['link']);
						$result['password'] = $this->encriptDecript($f3, $result['password'], 'd');
						$allitem[] = $result;
					}

					header('Content-Type: application/json');
					echo json_encode($allitem);
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