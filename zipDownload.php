<?php
include "Util.php";
require_once 'FacebookConfig.php';

$postdata = file_get_contents("php://input");
$albumrequest = json_decode($postdata);
$_fbobj=new FacebookConfig();
$_fbobj->fb->setDefaultAccessToken($_SESSION["facebook_access_token"]);
$ufun = new Util();
$ufun->DeleteFolder();
$zip = new ZipArchive;
date_default_timezone_set('UTC');
$rndmString = "assets/UserData/" . $ufun->generateRandomString(26) . "_" . date("h-i");
mkdir($rndmString);
if ($zip->open($rndmString . '/album.zip', ZipArchive::CREATE) === TRUE) {
        foreach ($albumrequest->data as $key => $value) {
            $albumID = $value->useralbumid;
            $albumName = str_replace("+", " ", $value->useralbumname);
            $useralbumimages = $_fbobj->getuseralbumimages($albumID);
            foreach ($useralbumimages as $key => $value) {
                $data = file_get_contents($value['source']);
                $fp = fopen($rndmString . "/" . $albumName . $key . ".jpg", "w");
                     if (!$fp){
						 exit;
					 }
                     fwrite($fp, $data);
                $filename = $rndmString . "/" . $albumName . $key . ".jpg";
                $path = $albumName . '/' . $key . '.jpg';
                $zip->addFile($filename, $path);
            }
        }
    $zip->close();
}
echo $rndmString . "/album.zip";
?>