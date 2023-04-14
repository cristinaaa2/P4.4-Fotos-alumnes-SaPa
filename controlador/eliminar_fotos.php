<?php
use Google\Client;
use Google\Service\Drive;
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

if(isset($_POST['Ecarpeta'])) {
    try {
        eliminarCarpetaServidor('../fotos/*');
        echo "OK"; 
    } catch (Exception $e) {
        echo "ERROR: Hi ha hagut un error al eliminar el contingut de les carpetes." . $e;
    }
}

function eliminarCarpetaServidor($dir) {
    $files = glob($dir); // obte una llista de tots els arxius i carpetes dins de la carpeta especificada
    foreach($files as $file){
        if(is_file($file)){ // si es un arxiu, l'elimina
            unlink($file);
        } elseif(is_dir($file)){ // si es una carpeta, elimina el seu contingut i despres la carpeta
            eliminarCarpetaServidor("$file/*");
            rmdir($file);
        }
    }
}
?>