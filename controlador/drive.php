<?php
// Llibreria de client de l'API de Google
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/drive.file']);


try {
  $service = new Google\Service\Drive($client);
  $file_path = "../model/profes.json";

  $file = new Google\Service\Drive\DriveFile();
  $file->setName($file_path);

  $file->setParents(array("1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg"));
  $file->setDescription("Profes");
  $file->setMimeType("application/json");

  $result = $service->files->create(
    $file,
    array(
      'data' => file_get_contents($file_path),
      'mimeType' => 'application/json',
      'uploadType' => 'multipart'
    )
  );

  echo '<a href="https://drive.google.com/open?id=' . $result->id . '/view">View File</a>';

} catch (Google_Service_Exception $e) {
  echo $e;
} catch (Exception $e) {
  echo $e;
}

?>