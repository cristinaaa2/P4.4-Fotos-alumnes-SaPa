<?php
use Google\Client;
use Google\Service\Drive;
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
include_once './eliminar_fotos.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

if(isset($_POST["id"])) {
    $id = $_POST["id"];
    try {
        $file = "../model/config.json";
        if(file_exists($file)) {
            if(comprovarCarpetaDrive($id)) {
                $json = file_get_contents($file);
                $config = json_decode($json, true);
                $config["FOLDER_ID"] = $_POST["id"];
                $json = json_encode($config);
                file_put_contents($file, $json);
                eliminarCarpetaServidor('../fotos/*');
                eliminarDadesJSON();
                echo "El ID de la carpeta s'ha guardat correctament.";
            } else {
                echo "ERROR: La carpeta no existeix o no l'has compartit amb el correu de l'aplicació. (sapafoto@articles-366108.iam.gserviceaccount.com)";
            }
            
        } else {
           echo "ERROR: No hi ha dades per guardar la carpeta."; 
        }
        
    } catch(Exception $e) {
        echo "ERROR: Hi ha hagut un error al guardar la carpeta."; 
    } 
}

/**
 * Comporova que existeixi la carpeta pare i que hi hagin dades en les classes i crea les carpetes dels alumnes en el servidor i en el drive.
 */
function generarCarpetesTSV() {
    try {
        $data = file_get_contents("../model/classes.json");
        $classes = json_decode($data, true);
        $folderId = IdCarpetaPare();
        if($folderId == "") {
            return false;
        } 
        for($i = 0; $i < count($classes); $i++) {
            if(preg_match("/^\w+$/", $classes[$i]["id"])) {
                crearCarpeta($classes[$i]["cicle"] . $classes[$i]["curs"] . $classes[$i]["grup"]);
                crearCarpetaDrive($classes[$i]["cicle"] . $classes[$i]["curs"] . $classes[$i]["grup"], $folderId);
            }
        }
        return true;
    } catch(Exception $e) {
        return false;
    }
}

/**
 * Crea les carpetes dels cursos en el servidor.
 */
function crearCarpeta($classe) {
    try {
       if (!file_exists("../fotos/" . $classe)) {
            mkdir("../fotos/" . $classe, 0777);
        }
    } catch(Exception $e) {
        echo "ERROR: Error al crear la carpeta del servidor.";
    }
}

/**
 * Crea les carpetes dels cursos en el Drive
 */
function crearCarpetaDrive($classe, $folderId) { 
    try {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(['https://www.googleapis.com/auth/drive']);
        $driveService = new Google\Service\Drive($client);
        $optParams = array(
            'q' => "mimeType='application/vnd.google-apps.folder' and parents in '" . $folderId . "' and trashed=false and name='".$classe."'",
            'fields' => 'files(id, name)'
        );
        $results = $driveService->files->listFiles($optParams);
        if (count($results->getFiles()) > 0) {
            // $driveService->files->delete($results->files[0]->id);
        } else {
             $fileMetadata = new Google\Service\Drive\DriveFile(array(
            'name' => $classe,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => array($folderId)));
        $file = $driveService->files->create($fileMetadata, array(
            'fields' => 'id'));  
        }
    } catch(Exception $e) {
       echo "ERROR: Error al crear la carpeta del drive.";
    }
}

/**
 * Comprova si la carpeta existeix i està compartida.
 */
function comprovarCarpetaDrive($id) {
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive']);
    $driveService = new Google\Service\Drive($client);

    $optParams = array(
    'fields' => $id
    );
    try {
    $file = $driveService->files->get($id, $optParams);
    } catch (Google_Service_Exception $e) {
        if ($e->getCode() == 404) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * Retorna, si hi ha, l'id de la carpeta pare que hi ha configurat en el JSON de config.
 */
function IdCarpetaPare() {
    $file = "../model/config.json";
    if(file_exists($file)) {
        $json = file_get_contents($file);
        $config = json_decode($json, true);
        if(isset($config["FOLDER_ID"])) {
            return $config["FOLDER_ID"];
        } else {
            return "";
        }
    } else {
       return "";
    }
}
?>