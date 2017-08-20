<?php
include "deletefolder.php";
require_once 'facebookobj.php';

$postdata = file_get_contents("php://input");
$albumrequest = json_decode($postdata);

$fb->setDefaultAccessToken($_SESSION["facebook_access_token"]);

$zip = new ZipArchive;
date_default_timezone_set('UTC');
$rndmString="assets/UserData/".generateRandomString(26)."_".date("h-i");
mkdir($rndmString);
if ($zip->open($rndmString . '/album.zip', ZipArchive::CREATE) === TRUE) {
    try {
        foreach ($albumrequest->data as $key => $value) {
            $albumID = $value->useralbumid;
            $albumName = str_replace("+", " ", $value->useralbumname);
            $useralbumimage_response = $fb->get("/" . $albumID . "/photos?fields=source");
            $useralbumimages = $useralbumimage_response->getGraphEdge()->asArray();
            foreach ($useralbumimages as $key => $value) {
                $data = file_get_contents($value['source']);
                $fp = fopen($rndmString . "/" . $albumName . $key . ".jpg", "w");
                     if (!$fp) exit;
                     fwrite($fp, $data);
                $filename = $rndmString . "/" . $albumName . $key . ".jpg";
                $path = $albumName . '/' . $key . '.jpg';
                $zip->addFile($filename, $path);
            }
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        // redirecting user back to app login page
        header("Location: ./");
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other lqocal issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $zip->close();
}
echo $rndmString . "/album.zip";
?>