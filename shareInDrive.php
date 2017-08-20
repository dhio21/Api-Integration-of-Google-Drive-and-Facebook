<?php

//require __DIR__.'/google-api-php-client/autoload.php';
include "deletefolder.php";
include "facebookobj.php";
include "gpConfig.php";
include 'lib/Google_API/src/Google/Service/Drive.php';
function createSubFolder($service,$folderId,$folderName)
{
  $files =$service->files->listFiles(array('q' => "'$folderId' in parents"));
  $found = false;
    // Go through each one to see if there is already a folder with the specified name
  foreach ($files['items'] as $item) {
    if ($item['title'] == $folderName) {
        $found = true;
        return $item['id'];
        break;
    }
}
if(!$found){
  $subFolder=new Google_Service_Drive_DriveFile();
  $subFolder->setTitle($folderName);
  $subFolder->setMimeType('application/vnd.google-apps.folder');
  $parent=new Google_Service_Drive_ParentReference();
  $parent->setId($folderId);
  $subFolder->setParents(array($parent));
  try {
    $subFolderMeataData = $service->files->insert($subFolder, array(
        'mimeType' => 'application/vnd.google-apps.folder',
        ));                 
} 
catch (Exception $e) {
    print "An error occurred: " . $e->getMessage();
}
return $subFolderMeataData->id;
}
}
function getFolderExistsCreate($service, $folderName, $folderDesc) {
    // List all user files (and folders) at Drive root
    $files = $service->files->listFiles(array('q' => "trashed=false"));
    $found = false;

    // Go through each one to see if there is already a folder with the specified name
    foreach ($files['items'] as $item) {
        if ($item['title'] == $folderName) {
            $found = true;
            return $item['id'];
            break;
        }
    }
    // If not, create one
    if ($found == false) {
        $folder = new Google_Service_Drive_DriveFile();
        //Setup the folder to create
        $folder->setTitle($folderName);
        if(!empty($folderDesc))
            $folder->setDescription($folderDesc);
        $folder->setMimeType('application/vnd.google-apps.folder');
        //Create the Folder
        try {
            $createdFile = $service->files->insert($folder, array(
                'mimeType' => 'application/vnd.google-apps.folder',
                ));
            // Return the created folder's id
            return $createdFile->id;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
}
function insertFile($service, $title,  $mimeType, $filename, $folderID) {
	$file = new Google_Service_Drive_DriveFile();

	// Set the metadata
	$file->setTitle($title);
	$file->setDescription("");
	$file->setMimeType($mimeType);

	// Setup the folder you want the file in, if it is wanted in a folder
 $parent = new Google_Service_Drive_ParentReference();
 $parent->setId($folderID);
 $file->setParents(array($parent));
 try {
		// Get the contents of the file uploaded
   $data = file_get_contents($filename);

		// Try to upload the file, you can add the parameters e.g. if you want to convert a .doc to editable google format, add 'convert' = 'true'
   $createdFile = $service->files->insert($file, array(
     'data' => $data,
     'mimeType' => $mimeType,
     'uploadType'=> 'multipart'
     ));
		// Return a bunch of data including the link to the file we just uploaded
		//return $createdFile;
} catch (Exception $e) {
  print "An error occurred: " . $e->getMessage();
}
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$gClient->setAccessToken($_SESSION["token"]);
//$func=new UtilityFunction();
$fb->setDefaultAccessToken($_SESSION["facebook_access_token"]);

date_default_timezone_set('UTC');
$rndmString="assets/UserData/".generateRandomString(26)."_".date("h-i");
mkdir($rndmString);
try {
    $profile_request = $fb->get('/me?fields=name');
    $profile = $profile_request->getGraphNode()->asArray();
    $service = new Google_Service_Drive($gClient);
    $folderId=getFolderExistsCreate($service,"facebook_".str_replace(" ", "_", $profile['name'])."_album","");
    foreach ($request->data as $key => $value) {
        $albumID=$value->useralbumid;
        $albumName=str_replace("+", " ", $value->useralbumname);
        $useralbumimage_response = $fb->get("/" . $albumID . "/photos?fields=source");
        $useralbumimages = $useralbumimage_response->getGraphEdge()->asArray();
        $subFolderId=createSubFolder($service,$folderId,$albumName);
        foreach ($useralbumimages as $key => $value) {
            $data=file_get_contents($value['source']);
            $fp = fopen($rndmString."/".$albumName.$key.".jpg","w");
            if (!$fp) exit;
            fwrite($fp, $data);

            $title=$albumName.$key;
            $filename=$rndmString."/".$albumName.$key.".jpg";
            $mimeType=mime_content_type ( $filename );
            insertFile($service, $title,  $mimeType, $filename, $subFolderId);
        }
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    header("Location: ./");
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
?>