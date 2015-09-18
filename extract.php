<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

// retrieve and update the users DB
$str = file_get_contents(Config::$storedir . "/users.json");
$start = "20150730";
$end = "20150714";
$users = json_decode($str, true);
foreach($users as $key => $user) {
	file_put_contents(Config::$storedir . "/extract" . $key . ".json", 
		json_encode($m->get_range($user['access_token'],'/user/storyline/daily', $start, $end),
			    JSON_PRETTY_PRINT));
}
?>
