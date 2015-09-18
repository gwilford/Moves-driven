<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

if (isset($_GET['code'])) {
    $request_token = $_GET['code'];
    $tokens = $m->auth($request_token);

    // retrieve and update the users DB
    $str = file_get_contents(Config::$storedir . "/users.json");
    if ($str) {
	    $users = json_decode($str, true);
    } else {
	    $users = array();
    }
    $users[$tokens['user_id']] = $tokens;
    file_put_contents(Config::$storedir . "/users.json", json_encode($users, JSON_PRETTY_PRINT));
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
    </head>
    <body>
        Thank you - you are now registered with Moves-driven
    </body>
</html>
