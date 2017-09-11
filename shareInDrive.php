<?php

include "Util.php";
include "FacebookConfig.php";
include "Google.php";
include 'lib/Google_API/src/Google/Service/Drive.php';

$utilfun = new Util();
$_fbobj=new FacebookConfig();
$gClient = new Google();

$utilfun->DeleteFolder();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$gClient->g_client->setAccessToken($_SESSION["token"]);
$_fbobj->fb->setDefaultAccessToken($_SESSION["facebook_access_token"]);

date_default_timezone_set('UTC');
$rndmString = "assets/UserData/" . $utilfun->generateRandomString(26) . "_" . date("h-i");
mkdir($rndmString);
    $profile_request = $_fbobj->fb->get('/me?fields=name');
    $profile = $profile_request->getGraphNode()->asArray();
    $service = new Google_Service_Drive($gClient->g_client);
    $folderId = $gClient->getFolderExistsCreate($service, "facebook_" . str_replace(" ", "_", $profile['name']) . "_album", "");
    $albumsize=count($request->data);
    foreach ($request->data as $key => $value) {
        $albumsize-=1;
        $albumID = $value->useralbumid;
        $albumName = str_replace("+", " ", $value->useralbumname);
        $useralbumimages = $_fbobj->getuseralbumimages($albumID);
        $subFolderId = $gClient->createSubFolder($service, $folderId, $albumName);
        foreach ($useralbumimages as $keyimg => $valueimg) {
            $data = file_get_contents($valueimg['source']);
            $fp = fopen($rndmString . "/" . $albumName . $keyimg . ".jpg", "w");
            if (!$fp) {
                exit;
            }
            fwrite($fp, $data);
            $title = $albumName . $keyimg;
            $filename = $rndmString . "/" . $albumName . $keyimg . ".jpg";
            $mimeType = mime_content_type($filename);
            $gClient->insertFile($service, $title, $mimeType, $filename, $subFolderId);
        }
        if($albumsize==0){
            break;
        }
    }
    echo "Success";
?>