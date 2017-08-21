<?php
require_once 'FacebookConfig.php';
$_fbobj=new FacebookConfig();
$_fbobj->facebooklogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Album World</title>
    <!-- Required meta tags -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/image/icon.ico">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/mainstyle.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed|Raleway" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.5/angular.min.js"></script>
</head>
<body ng-app="fbalbum">
<form method="post" ng-controller="albumController">
    <div class="jumbotron jumbotron-fluid">
        <div class="container text-center">
            <h1 class="display-3" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;">Welcome to album
                world</h1>
            <p class="lead" style="font-family: 'Roboto Condensed', sans-serif;">Remember your beautiful memories in a
                blink.</p>
				<button ng-click="loginauth()" class="loginBtn loginBtn--facebook" name="loginBtn">
                    Login with Facebook
                    </button>
        </div>
    </div>
    <div class="container">
        <div class="page-header text-center ">
            <h1 id="timeline" style="color:#838282 ;font-family: 'Raleway', sans-serif;, sans-serif;">WHAT THIS
                PROVIDES</h1>
        </div>
        <ul class="timeline">
            <li>
                <div class="timeline-badge"><i class="glyphicon glyphicon-check"></i></div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title text-center">Login with facebook</h4>
                    </div>
                </div>
            </li>
            <li class="timeline-inverted">
                <div class="timeline-badge warning"><i class="glyphicon glyphicon-credit-card"></i></div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title text-center">Take a glimpse of beautiful albums</h4>
                    </div>
                </div>
            </li>
            <li>
                <div class="timeline-badge info"><i class="glyphicon glyphicon-floppy-disk"></i></div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title text-center">Download albums</h4>
                    </div>
                </div>
            </li>
            <li class="timeline-inverted">
                <div class="timeline-badge success"><i class="glyphicon glyphicon-thumbs-up"></i></div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title text-center">Move albums to google drive</h4>
                    </div>
                </div>
            </li>
               <li>
                <div class="timeline-badge"><i class="glyphicon glyphicon-floppy-disk"></i></div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title text-center">Enjoy</h4>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="navbar navbar-light bg-light text-center" style="background-color: #838282;">
        <div class="container">
            <span style="font-size:larger; margin:auto;color: #3b5998;font-family: 'Roboto Condensed', sans-serif;">Designed & developed by Smit Machchhar </span>
        </div>
</div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
angular.module("fbalbum", []).controller("albumController", function ($window, $scope, $http) {
            $scope.loginauth=function () {
                $window.location="<?php echo $_fbobj->loginUrl;?>";
            }
        });
</script>
</form>
</body>
</html>