<?php

class Util
{
    function DeleteFolder()
    {
        $dir = "assets/UserData/";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != "..") {
                        $intime = strtotime(str_replace("-", ":", explode("_", $file)[1]));
                        $addminutes_intime = date("h:i", strtotime("+5 minutes", $intime));
                        if ($addminutes_intime <= date("h:i")) {
                            $this->Delete($dir . $file);
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
    function Delete($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                $this->Delete(realpath($path) . '/' . $file);
            }
            rmdir($path);
        } else if (is_file($path) === true) {
            unlink($path);
        }
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}