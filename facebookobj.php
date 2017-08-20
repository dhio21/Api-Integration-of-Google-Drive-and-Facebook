<?php
session_start();
require_once __DIR__ . '/lib/Facebook_API/src/Facebook/autoload.php';
$fb = new Facebook\Facebook([
	'app_id' => '2023485154541682',
    'app_secret' => 'bf083d866432dfe022efd8b55b408aa2',
    'default_graph_version' => 'v2.10',
]);
$authUrl = "";
?>