<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

// retrieve and update the users DB
$str = file_get_contents(Config::$storedir . "/users.json");
$today = new DateTime();
echo $today->format('Ymd') . "\n";
$interval = DateInterval::createfromdatestring('+1 day');

$users = json_decode($str, TRUE);
foreach($users as $key => $user) {
	echo $user['user_id'] . "\n";
	// validate user token
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
	
	// read in existing extract file
	$file = Config::$storedir . "/extract" . $key . ".json";
	if (is_file($file)) {
		$moves = json_decode(file_get_contents($file), TRUE);
	} else {
		$moves = array();
	}
	//print_r($moves);
	//exit;
	//echo array_keys($moves);
	$date = new DateTime($user['profile']['firstDate']);
	// cycle through all *new* dates 1 days at a time
	while ($date <= $today) {
		$day = $date->format('Ymd');
		if (!isset($moves[$day]) || $moves[$day] == "null") {
			echo $day . ", ";
			$ret = $m->get_range($user['access_token'],
                          		'/user/storyline/daily', $day, $day); 
			sleep(1);
			if ($ret && $ret != NULL) {
				$moves[$day] = $ret;
				$date->add($interval);
			} else {
				echo "Hourly rate limit of 2000 requests exceeded; try again at the top of the hour\n";
				exit;
			}
		} else {
			$date->add($interval);
		}
	}
	echo "\n";
	file_put_contents(Config::$storedir . "/extract" . $key . ".json", 
		//json_encode($moves, JSON_PRETTY_PRINT));
		json_encode($moves));
			    
}
?>
