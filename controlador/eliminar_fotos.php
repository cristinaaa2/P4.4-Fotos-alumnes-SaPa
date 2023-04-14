<?php

if(isset($_POST['id'])) {
    // rmdirRecursive('../fotos/');
    echo "OK";
}

function rmdirRecursive($dir) {
    $files = scandir($dir);
   
    foreach ($files as $file) {
        $file = $dir . '/' . $file;
        if (is_dir($file)) {
            rmdirRecursive($file);
            rmdir($file);
        } else {
            unlink($file);
        }
    }
}
 
// remove directory /home/nash/tmp
$dir = '/home/nash/tmp';
rmdirRecursive($dir);
?>