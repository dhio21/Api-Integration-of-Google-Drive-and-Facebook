<?php
include "facebookobj.php";
if (isset($_GET['albumid'])) {
    if (isset($_SESSION['facebook_access_token'])) {
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        try {
            $profile_request = $fb->get('/me?fields=picture.width(200).height(200),id,name,cover');
            $profile = $profile_request->getGraphNode()->asArray();

            $useralbumimage_response = $fb->get("/" . $_GET['albumid'] . "/photos?fields=source,name,id");
            $useralbumimages = $useralbumimage_response->getGraphEdge()->asArray();

            $album_res= $fb->get("/" . $_GET['albumid'] . "/");
            $nameidalbum = $album_res->getGraphNode()->asArray();
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
    } else {
        header("location:index.php");
    }
} else {
    header("location:./");
}
?>
<script type="text/javascript" src="assets/js/lazyload.min.js"></script>
<link href='assets/css/simplelightbox.min.css' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="assets/js/simple-lightbox.js"></script>
<div class="container">
    <div class="row" style="margin: auto;">
        <div class="container text-center" style="margin: 15px 0px 15px 30px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" style="font-size: larger"><a href="albums.php">Album</a></li>
            <li class="breadcrumb-item active" style="font-size: larger"><?php echo $nameidalbum['name']; ?></li>
        </ol>
            <h2 class="text-center" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;"><?php echo $nameidalbum['name'];echo '\'s' ?> Image</h2>
        </div>
    </div>
    <div class="gallery" >
        <?php
        foreach ($useralbumimages as $useralbumimage) {
            $i=1;
            if (isset($useralbumimage['name']))
                echo '<a id='.$i.' href="' . $useralbumimage['source'] . '" class="big"><img data-original="' . $useralbumimage['source'] . '" height="200px" width="200px" alt="" title="' . $useralbumimage['name'] . '"/></a>';
            else
                echo '<a href="' . $useralbumimage['source'] . '" class="big"><img data-original="' . $useralbumimage['source'] . '" height="200px" width="200px" alt="" title=""/></a>';
        } $i++;?>
    </div>
</div>
<script type="text/javascript">
var myLazyLoad = new LazyLoad();
    $(function () {
        var $gallery = $('.gallery a').simpleLightbox();
    });
</script> 


