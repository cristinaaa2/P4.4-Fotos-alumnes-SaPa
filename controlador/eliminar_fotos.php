<?php
use Google\Client;
use Google\Service\Drive;
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');
/**
 * Elimina les carpetes del servidor i les dades del fitxer classes.json.
 */
if(isset($_POST['Ecarpeta'])) {
    try {
        eliminarCarpetaServidor('../fotos/*');
        eliminarDadesJSON();
        // marcarFotoNo();
        echo "Les carpetes s'han eliminat correctament.";
    } catch (Exception $e) {
        echo "ERROR: Hi ha hagut un error al eliminar el contingut de les carpetes.";
    }
}

/**
 * Elimina el contingut de la carpeta especificada.
 */
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

/**
 * Marca la foto de cada alumne a NO.
 */
function marcarFotoNo() {
    $file = "../model/classes.json";
    if(file_exists($file)) {
        $json_str = file_get_contents($file);
        $json = json_decode($json_str, true);
        for($i = 0; $i < count($json); $i++) {
           if(isset($json[$i]["foto"])) {
                $json[$i]["foto"] = "NO";
                $json_str = json_encode($json);
                file_put_contents($file, $json_str);
            } 
        }
        
    }
}

/**
 * Elimina les dades del fitxer classes.json.
 */
function eliminarDadesJSON() {
    $file = "../model/classes.json";
    if(file_exists($file)) {
        file_put_contents($file, '');
    }
}
?>