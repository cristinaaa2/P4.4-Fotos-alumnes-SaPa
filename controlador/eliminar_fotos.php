<?php
use Google\Client;
use Google\Service\Drive;
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

if(isset($_POST['Ecarpeta'])) {
    try {
        eliminarCarpetaServidor('../fotos/*');
        eliminarCarpetaDrive();
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

function eliminarCarpetaDrive() {
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive']);
    $driveService = new Google\Service\Drive($client);
    $folderId = '1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg';
    eliminarRecursiuDrive($folderId, $driveService);
}

function eliminarRecursiuDrive($folderId, $driveService) {
    echo "Folder ID: " . $folderId;
    $optParams = array(
        'q' => "trashed=false and '".$folderId."' in parents",
        'fields' => 'files(id, name, mimeType)'
    );
    $results = $driveService->files->listFiles($optParams);
    foreach ($results->files as $file) {
        // echo "Files: " . $file->name;
        if ($file->mimeType == 'application/vnd.google-apps.folder') {
            // Si es una carpeta, torna a cridar la funcio
            // echo "Carpeta eliminada: " . $file->name;
            eliminarRecursiuDrive($file->id, $driveService);
            $driveService->files->delete($file->id);
            
        } else {
            // Si es un arxiu, l'elimina
            $driveService->files->delete($file->id);
        }
    }
}
?>