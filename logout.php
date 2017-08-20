<?php
session_start();
if (isset($_GET['session']))
    if ($_GET['session'] == "facebook_access_token")
        session_destroy();
    else
        unset($_SESSION[$_GET['session']]);
header("location:./");
?>