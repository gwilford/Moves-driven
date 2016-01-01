<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

// retrieve and update the users DB
$str = file_get_contents(Config::$storedir . "/users.json");
$start = "20150730";
$end = "20150814";
$users = json_decode($str, true);
foreach($users as $key => $user) {
	echo $user['user_id'] . "\n";
	if (!$m->validate_token($user['access_token'])) {
		$refresh = $m->refresh($user['access_token']);
		echo json_encode($refresh, JSON_PRETTY_PRINT);
		if (isset($refresh['error'])) {
			unset($users[$key]);
		} else {
			// supplement the retrieved tokens
			$refresh['starts_at'] = time();
			$refresh['expires_at'] = date(DATE_RFC850, 
						      $refresh['starts_at'] + $refresh['expires_in']);
			$user = array_merge($user, $refresh);
			$users[$key] = $user;
		}
		file_put_contents(Config::$storedir . "/users.json", json_encode($users, JSON_PRETTY_PRINT));
	}
	file_put_contents(Config::$storedir . "/extract" . $key . ".json", 
		json_encode($m->get_range($user['access_token'],
			    '/user/storyline/daily', $start, $end), JSON_PRETTY_PRINT));
}
?>
