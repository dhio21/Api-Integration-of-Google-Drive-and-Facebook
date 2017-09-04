<?php
require __DIR__ . "/lib/Google_API/src/Google/Client.php";
include_once 'lib/Google_API/src/Google/Auth/OAuth2.php';

class Google
{
    private $clientid = "CLIENT ID";
    private $clientsecret = "CLIENT SECRET";
    private $callbackuri = "http://localhost/fbdemo/albums.php";
    private $scope = array(
        'https://www.googleapis.com/auth/drive.file',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile');
    var $g_client;

    function __construct()
    {
        $this->g_client = new Google_Client();
        $this->g_client->setClientId($this->clientid);
        $this->g_client->setRedirectUri($this->callbackuri);
        $this->g_client->setClientSecret($this->clientsecret);
        $this->g_client->setAccessType('offline');
        $this->g_client->setScopes($this->scope);
    }

    function getuserinfo()
    {
        return (new Google_Service_Oauth2($this->g_client))->userinfo->get();
    }

    function authcredentialscode($credentialscode)
    {
        try {
            $this->g_client->authenticate($credentialscode);
            $_SESSION['token'] = $this->g_client->getAccessToken();
            header("location:albums.php");
        } catch (Exception $e) {
            print 'An error occurred: ' . $e->getMessage();
        }
    }

    function checkcredentials()
    {
        if (isset($_SESSION['token'])) {
            $this->g_client->setAccessToken($_SESSION['token']);
        }
        if ($this->g_client->getAccessToken()) {
            return true;
        } else {
            return false;
        }
    }

    function createSubFolder($service, $folderId, $folderName)
    {
        $files = $service->files->listFiles(array('q' => "'$folderId' in parents"));
        $found = false;
        foreach ($files['items'] as $item) {
            if ($item['title'] == $folderName) {
                $found = true;
                return $item['id'];
                break;
            }
        }
        if (!$found) {
            $subFolder = new Google_Service_Drive_DriveFile();
            $subFolder->setTitle($folderName);
            $subFolder->setMimeType('application/vnd.google-apps.folder');
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($folderId);
            $subFolder->setParents(array($parent));
            try {
                $subFolderMeataData = $service->files->insert($subFolder, array(
                    'mimeType' => 'application/vnd.google-apps.folder',
                ));
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
            return $subFolderMeataData->id;
        }
    }

    function getFolderExistsCreate($service, $folderName, $folderDesc)
    {
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
            if (!empty($folderDesc)) {
                $folder->setDescription($folderDesc);
            }
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

    function insertFile($service, $title, $mimeType, $filename, $folderID)
    {
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
                'uploadType' => 'multipart'
            ));
            // Return a bunch of data including the link to the file we just uploaded
            //return $createdFile;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
}
