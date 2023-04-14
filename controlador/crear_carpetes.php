<?php
use Google\Client;
use Google\Service\Drive;
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

if(isset($_POST["id"])) {
    echo "ID: " . $_POST["id"];
    general();
}

function general() {
    try {
        require_once '../vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();

        $data = file_get_contents("../model/classes.json");
        $classes = json_decode($data, true);

        foreach ($classes as $classe) {
            crearCarpeta($classe["cicle"] . $classe["curs"] . $classe["grup"]);
            crearCarpetaDrive($classe["cicle"] . $classe["curs"] . $classe["grup"]);
        }
    } catch(Exception $e) {
        echo "Error al crear les carpetes.";
    }
    
}

function crearCarpeta($classe) {
    if (!file_exists("../fotos/" . $classe)) {
        mkdir("../fotos/" . $classe, 0777);
    }
}

function crearCarpetaDrive($classe) {
    
    try {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(['https://www.googleapis.com/auth/drive.file']);
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
                'parents' => array('1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg')));
            $file = $driveService->files->create($fileMetadata, array(
                'fields' => 'id'));
    } catch(Exception $e) {
       echo "Error Message: ".$e;
    }
}
?>