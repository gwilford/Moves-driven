<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

// retrieve and update the users DB
$str = file_get_contents(Config::$storedir . "/users.json");
$users = json_decode($str, true);
foreach($users as $key => $user) {
	echo sprintf("%s", $user['user_id']) . "\n";
	echo json_encode($m->get_profile($user['access_token']), JSON_PRETTY_PRINT);
			    
}
?>
