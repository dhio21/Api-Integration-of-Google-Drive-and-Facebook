<?php
require_once __DIR__ . '/lib/Facebook_API/src/Facebook/autoload.php';

class FacebookConfig
{
    public $loginUrl = "";
    var $fb;
	public $helper="";
    function __construct()
    {
		session_start();
        $this->fb = new \Facebook\Facebook([
            'app_id' => 'Enter APP ID',
            'app_secret' => 'Enter APP SECRET',
            'default_graph_version' => 'v2.10',
        ]);
		$this->fb->helper = $this->fb->getRedirectLoginHelper();
    }
	function facebooklogin(){
		try {
			if (isset($_SESSION['facebook_access_token'])) {
				$accessToken = $_SESSION['facebook_access_token'];
			} else {
				$accessToken = $this->fb->helper->getAccessToken();
			}
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		if (isset($accessToken)) {
			if (isset($_SESSION['facebook_access_token'])) {
				header("location:albums.php");
			} else {	
				$this->setcredentials($accessToken);
			}
			if (isset($_GET['code'])) {
				header('Location:albums.php');
			}
		}
		else{
			$this->loginUrl=$this->getLoginUrl();
		}
	}
	
	function getloginUrl(){
		$permissions = ['email','user_photos'];
		return $this->fb->helper->getLoginUrl('http://localhost/fbdemo/', $permissions);
	}
    function setcredentials($accessToken)
    {
        $_SESSION['facebook_access_token'] = (string)$accessToken;
        $oAuth2Client = $this->fb->getOAuth2Client();
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string)$longLivedAccessToken;
        $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
	function getuserinfomation(){
		 try {
			$profile_request = $this->fb->get('/me?fields=picture.width(200).height(200),id,name,cover');
			return $profile_request->getGraphNode()->asArray();
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			header("Location: ./");
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
	function getuseralbums($userid){
		try{
			$useralbums_response = $this->fb->get("/" . $userid . "/albums?fields=picture,name,id");
			return $useralbums_response->getGraphEdge()->asArray();
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			header("Location: ./");
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
	function getuseralbumimages($albumid){
		try{
			$useralbumimage_response = $this->fb->get("/" . $albumid . "/photos?fields=source,name,id");
			$useralbumimages=$useralbumimage_response->getGraphEdge();
			$bkpuseralbumimages=$useralbumimages;
           		while($this->fb->next($bkpuseralbumimages)!=null)
           		{
                		$tmp=$this->fb->next($bkpuseralbumimages)->asArray();
                		$useralbumimages=$useralbumimages->asArray();
                		$useralbumimages=array_merge($useralbumimages,$tmp);
                		$bkpuseralbumimages=$this->fb->next($bkpuseralbumimages);
            		}
	   		return $useralbumimages;
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			header("Location: ./");
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
}

?>
