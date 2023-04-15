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
                if(general()) { 
                    echo "La carpeta s'ha guardat correctament. Les carpetes s'han creat correctament.";
                } else {
                    echo "La carpeta s'ha guardat correctament. ERROR: Hi ha hagut un error al crear les carpetes.";
                }
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

function general() {
    try {
        $data = file_get_contents("../model/classes.json");
        $classes = json_decode($data, true);
        $folderId = IdCarpetaPare();
        if($folderId == "") {
            return false;
        } 
        foreach ($classes as $classe) {
            crearCarpeta($classe["cicle"] . $classe["curs"] . $classe["grup"]);
            crearCarpetaDrive($classe["cicle"] . $classe["curs"] . $classe["grup"], $folderId);
        }
        return true;
    } catch(Exception $e) {
        return false;
    }
}

function generarCarpetesTSV() {
    try {
        $data = file_get_contents("../model/classes.json");
        $classes = json_decode($data, true);
        $folderId = IdCarpetaPare();
        if($folderId == "") {
            return false;
        } 
        foreach ($classes as $classe) {
            crearCarpetaTSV($classe["cicle"] . $classe["curs"] . $classe["grup"]);
            crearCarpetaDriveTSV($classe["cicle"] . $classe["curs"] . $classe["grup"], $folderId);
        }
        return true;
    } catch(Exception $e) {
        return false;
    }
}


function crearCarpeta($classe) {
    try {
       if (!file_exists("../fotos/" . $classe)) {
            mkdir("../fotos/" . $classe, 0777);
        } 
        // else {
        //     eliminarCarpetaServidor("../fotos/" . $classe . "/*");
        // } 
    } catch(Exception $e) {
        echo "ERROR: Error al crear la carpeta del servidor.";
    }
}

function crearCarpetaTSV($classe) {
    try {
       if (!file_exists("../fotos/" . $classe)) {
            mkdir("../fotos/" . $classe, 0777);
        } 
    } catch(Exception $e) {
        echo "ERROR: Error al crear la carpeta del servidor.";
    }
    
}

function crearCarpetaDrive($classe, $folderId) { 
    try {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(['https://www.googleapis.com/auth/drive']);
        $driveService = new Google\Service\Drive($client);

        $results = $driveService->files->listFiles([
            'q' => "name='".$classe."'"
        ]);
        if (count($results->files) > 0) {
            $driveService->files->delete($results->files[0]->id);
        }
            $fileMetadata = new Google\Service\Drive\DriveFile(array(
                'name' => $classe,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => array($folderId)));
            $file = $driveService->files->create($fileMetadata, array(
                'fields' => 'id'));
    } catch(Exception $e) {
       echo "ERROR: Error al crear la carpeta del drive.";
    }
}

function crearCarpetaDriveTSV($classe, $folderId) { 
    try {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(['https://www.googleapis.com/auth/drive']);
        $driveService = new Google\Service\Drive($client);

        $results = $driveService->files->listFiles([
            'q' => "name='".$classe."'"
        ]);
        if (count($results->files) < 1) {
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