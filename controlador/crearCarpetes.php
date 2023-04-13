<?php
use Google\Client;
use Google\Service\Drive;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$data = file_get_contents("../model/classe.json");
$classes = json_decode($data, true);

var_dump($classes);

foreach ($classes as $classe) {
    crearCarpeta($classe->cicle . $classe->curs . $classe->grup);
    crearCarpetaDrive($classe->cicle . $classe->curs . $classe->grup);
}

function crearCarpeta($classe) {
    if (!file_exists("../fotos/" . $classe)) {
        mkdir("../fotos/" . $classe, 0777);
    }
}

function crearCarpetaDrive($classe) {
    
    try {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        $fileMetadata = new Drive\DriveFile(array(
            'name' => $classe,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => array('1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg')));
        $file = $driveService->files->create($fileMetadata, array(
            'fields' => 'id'));
        printf("Folder ID: %s\n", $file->id);
        return $file->id;
    } catch(Exception $e) {
       echo "Error Message: ".$e;
    }
}
?>