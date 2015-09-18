<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

if (isset($_GET['code'])) {
    $request_token = $_GET['code'];

    $tokens = $m->auth($request_token);
    var_dump($tokens);

    // retrieve and update the users DB
    $str = file_get_contents($storedir . "/users.json");
    if ($str) {
	    $users = json_decode($str, true);
    } else {
	    $users = array();
    }
    $users[$tokens['userid']] = $tokens;
    file_put_contents($storedir . "/users.json", json_encode($users));

    //Save this token for all future request for this user
    $access_token = $tokens['access_token'];
    echo json_encode($m->get_profile($access_token));
}
?>
