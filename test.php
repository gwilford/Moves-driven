<?php
include_once 'Moves.php';
include_once 'config.php';
$m = new PHPMoves\Moves(Config::$client_id,Config::$client_secret,Config::$redirect_url);

if (isset($_GET['code'])) {
    $request_token = $_GET['code'];
    $tokens = $m->auth($request_token);
    // retrieve and merge user profile
    $profile = $m->get_profile($tokens['access_token']);
    $info = array_merge($profile, $tokens);

    // retrieve and update the users DB
    $str = file_get_contents(Config::$storedir . "/users.json");
    if ($str) {
	    $users = json_decode($str, true);
    } else {
	    $users = array();
    }
    // supplement the retrieved tokens
    $info['starts_at'] = time();
    $info['expires_at'] = date(DATE_RFC850, $info['starts_at'] + $info['expires_in']);
    // ensure the user key is stringified
    $u = sprintf("%s", $info['user_id']);
    $users[$u] = $info;
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
