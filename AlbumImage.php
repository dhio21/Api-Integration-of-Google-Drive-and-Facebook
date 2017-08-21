<?php
include "FacebookConfig.php";
if (isset($_GET['albumid'])) {
	$_fbobj=new FacebookConfig();
    if (isset($_SESSION['facebook_access_token'])) {
        $_fbobj->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
			$useralbumimages=$_fbobj->getuseralbumimages($_GET['albumid']);
			$user_res=$_fbobj->fb->get("/".$_GET['albumid']."?fields=name");
			$albumnameid=$user_res->getGraphNode()->asArray();
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
            <li class="breadcrumb-item active" style="font-size: larger"><?php echo $albumnameid['name']; ?></li>
        </ol>
            <h2 class="text-center" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;"><?php echo $albumnameid['name'];echo '\'s' ?> Image</h2>
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


