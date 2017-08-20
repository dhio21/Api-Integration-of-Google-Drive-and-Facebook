<?php
//session_start();
//Include Google client library
include_once 'lib/Google_API/src/Google/Client.php';
include_once 'lib/Google_API/src/Google/Auth/OAuth2.php';
/*
 * Configuration and setup Google API
 */
$clientId = '99526189653-i54unaujh37nggti9c4105n7d3ak4vrj.apps.googleusercontent.com'; //Google client ID
$clientSecret = 'l3qx1R6WEZyUkqqgKTVYXSQF'; //Google client secret
$redirectURL = 'http://localhost/fbjasmin/Album.php'; //Callback URL
$SCOPES = array(
    'https://www.googleapis.com/auth/drive.file',
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile');

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Album World');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);
$gClient->setScopes($SCOPES);

$google_oauthV2 = new Google_Service_Oauth2($gClient);
?>