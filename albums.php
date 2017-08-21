<?php
include "FacebookConfig.php";
include 'Google.php';
require_once 'lib/Google_API/src/Google/Service/Drive.php';

$_fbobj=new FacebookConfig();
$gClient = new Google();
if (isset($_GET['code'])) {
    $gClient->authcredentialscode($_GET['code']);
}
if ($gClient->checkcredentials()) {
    $gpUserProfile = $gClient->getuserinfo();
} else {
    $authUrl = $gClient->g_client->createAuthUrl();
}
if (isset($_SESSION['facebook_access_token'])) {
    $_fbobj->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    $profile=$_fbobj->getuserinfomation();
    $useralbums = $_fbobj->getuseralbums($profile['id']);
    $albumjson = json_encode($useralbums);
} else {
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $profile['name'].'\'s' ?> Album World</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" type="image/x-icon" href="assets/image/icon.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="assets/css/HoldOn.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/css/mainstyle.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed|Raleway" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!--Javascript-->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.5/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
    <script src="assets/js/HoldOn.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
<style type="text/css">
       a.album_title {
            text-decoration: none;
            color: #3b5998;
            font-size: larger;
            font-family: 'Roboto Condensed', sans-serif;
        }

        a.album_title:hover {
            cursor: pointer;
            text-decoration: underline;
        }
        .btn:hover{
            cursor: pointer;
        }
        </style>
</head>
<body ng-app="fbalbum" ng-controller="albumController">
<?php require_once 'Profile.php'; ?>
<div class="row" id="albums" style="margin: auto;">
    <div class="container">
        <h2 style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;" class="text-center
">Your albums</h2>
        <div class="row text-center">
            <div class="col col-lg-3 col-xl-3 col-md-4 col-sm-12" style="margin-top: 10px;">
                <button class="btn btn-outline-primary" ng-click="download_Multiple_Album(1)" id="downloadmultiple"
                        disabled>
                    Download <span class="badge badge-info">{{albumselected}}</span> album
                    <span class="sr-only">unread messages</span>
                </button>
            </div>
            <div class="col col-lg-3 col-xl-3 col-md-4 col-sm-12" style="margin-top: 10px;">
                <button class="btn btn-outline-primary" ng-click="download_All_Album(1)">Download all <i class="fa fa-download" aria-hidden="true"></i>
            </button>
            </div>
            <div class="col col-lg-3 col-xl-3 col-md-4 col-sm-12" style="margin-top: 10px;">
                <?php if (isset($_SESSION['token'])) {?>
                    <button class="btn btn-outline-danger" ng-click="download_Multiple_Album(2)" id="sharemultiple"
                        ng-disabled="sharestate">
                    Share <span class="badge badge-info">{{albumselected}}</span> album to drive
                    <span class="sr-only">unread messages</span>
                </button>
                <?php } else {?>
					<button class="btn btn-outline-danger" disabled>
						Share <span class="badge badge-info">{{albumselected}}</span> album to drive
						<span class="sr-only">unread messages</span>
					</button>
				<?php }?>
            </div>
            <div class="col col-lg-3 col-xl-3 col-md-4 col-sm-12" style="margin-top: 10px;">
                <?php echo '<button class="btn btn-outline-danger" ng-click="download_All_Album(2)"';
                if (!isset($_SESSION['token'])) {
                    echo 'disabled';
                }
                echo '>Share all to drive
            </button>' ?>
            </div>
        </div>
    </div>
    <?php
    $i = 0;
    foreach ($useralbums as $useralbum) {
        ?>
        <div class="polaroid card" style="margin: 5px auto;">
            <?php echo '<img src="' . $useralbum['picture']['url'] . '" class="card-img-top" alt="Norway" height="170px" width="100%;">' ?>
            <div class="contain card-body">
                <div class="container-fluid">
                <?php echo '<a  href ng-click="loadimage(\'AlbumImage.php?albumid=' . $useralbum['id'] . '\')" class="album_title">' . $useralbum['name'] . '</a>'; ?>
                    <div class="album_select_download">
                        <?php echo '<label for="' . $useralbum['id'] . '" class="btn btn-outline-primary">Select ';
                        echo '<input type="checkbox" name="' . $useralbum['name'] . '" id="' . $useralbum['id'] . '" ng-model="isalbum[' . $i . ']" ng-true-value="true" ng-false-value="false" ng-change="addalbum(' . $i . ',\'' . $useralbum['name'] . '\',' . $useralbum['id'] . ')"/>';
                        echo '</label>' ?>
                    </div>
                    <div class="album_select_download">
                        <?php echo '<button style="cursor:pointer;"  class="btn btn-outline-dark" ng-click="singledownload(\'' . $useralbum["name"] . '\',' . $useralbum["id"] . ',1)">';
                        echo '<i class="fa fa-download" aria-hidden="true"></i></button>'; ?>
                        <?php echo '<button style="cursor:pointer;" class="btn btn-outline-danger"';
                        if (!isset($_SESSION['token'])) {
                            echo 'disabled';
                        }
                        echo ' ng-click="singledownload(\'' . $useralbum["name"] . '\',' . $useralbum["id"] . ',2)">';
                        echo '<i class="fa fa-google" aria-hidden="true"></i></button>'; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php $i++;
    } ?>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to download</h5>
                </div>
                <div class="modal-body">
                    <h6 id="filename" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;"></h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                    <button ng-click="downloadfolder(file.folder)" ng-model="file.folder" type="button"
                            class="btn btn-primary">Download
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="gshareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Shared your album</h5>
                </div>
                <div class="modal-body">
                    <h6 id="infotouser" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;"></h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="navbar navbar-light bg-light text-center" style="background-color: #838282;">
        <div class="container">
            <span style="font-size:larger;margin:auto;color: #3b5998;font-family: 'Roboto Condensed', sans-serif;">Designed & developed by Smit Machchhar </span>
        </div>
</div>
<!-- Modal -->
<script type="text/javascript">
    var albumjson=<?php print_r($albumjson); ?>;
</script>
<script type="text/javascript" src="assets/Controller/AlbumController.js"></script>
</body>
</html>