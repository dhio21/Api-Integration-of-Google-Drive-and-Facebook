<nav class="navbar navbar-light bg-light justify-content-between">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;">ALBUM
            WORLD</a>
        <?php if (!isset($_SESSION['token'])) {
            echo '<button ng-click="googleauth(\''.$authUrl.'\')" class="loginBtn text-nowrap loginBtn--google text-md-center text-lg-right text-sm-left" name="loginBtn">
                Login with Google
            </button>';
        } else {
            echo '<div class="dropdown">
                    <button class="btn dropdown-toggle"
                            style="color: #3b5998;font-family: \'Roboto Condensed\', sans-serif;background-color: transparent"
                            type="button"
                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <img src="'.$gpUserProfile['picture'].'" class="img" height="40px" width="40px" style="margin-right: 5px"/>
                        ' . $gpUserProfile['name'] . '
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="logout.php?session=token">Logout</a>
                    </div>
                </div>
            </div>';
          }
        ?>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="fb-profile" style="margin: auto;">
            <?php
            echo '<img align="left" class="fb-image-lg" src="' . $profile['cover']['source'] . '" alt="' . $profile['name'] . "\'s cover image" . '"/>';
            echo '<img align="left" class="fb-image-profile img-thumbnail" src="' . $profile['picture']['url'] . '" alt="' . $profile['name'] . "\'s profile image" . '"/>';
            ?>
            <div class="fb-profile-text">
                <div class="dropdown">
                    <button class="btn dropdown-toggle"
                            style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;background-color: transparent"
                            type="button"
                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <?php echo $profile['name']; ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="logout.php?session=facebook_access_token">Logout</a>
                    </div>
                </div
            </div>
        </div>
    </div>