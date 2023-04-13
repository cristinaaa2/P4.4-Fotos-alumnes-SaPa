<?php
use Google\Client;
use Google\Service\Drive;

function general() {
    require_once '../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $data = file_get_contents("../model/classes.json");
    $classes = json_decode($data, true);

    foreach ($classes as $classe) {
        crearCarpeta($classe["cicle"] . $classe["curs"] . $classe["grup"]);
        crearCarpetaDrive($classe["cicle"] . $classe["curs"] . $classe["grup"]);
        break;
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
        $driveService = new Drive($client);
        $fileMetadata = new Drive\DriveFile(array(
            'name' => $classe,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => array('1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg')));
        $file = $driveService->files->create($fileMetadata, array(
            'fields' => 'id'));
        //printf("Folder ID: %s\n", $file->id);
        return $file->id;
    } catch(Exception $e) {
       echo "Error Message: ".$e;
    }
}
?>