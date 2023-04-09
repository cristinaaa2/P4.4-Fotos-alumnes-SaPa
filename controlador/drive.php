<?php
// Llibreria de client de l'API de Google
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/drive.file']);


try {
  if(isset($_POST["foto"]) && isset($_POST["alumne"]) && isset($_POST["classe"])) {
    $imageData = $_POST['foto'];
    $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

    // Crear un nombre de archivo único para la imagen
    $fileName = $_POST["alumne"] . '.jpeg';

    // Guardar la imagen en una carpeta del proyecto
    file_put_contents('../fotos/' . $fileName, $decodedData);

    echo 'La imatge es va guardar correctament al servidor i al drive.';
    $service = new Google\Service\Drive($client);
    $file_path = "../fotos/" . $fileName;

    $file = new Google\Service\Drive\DriveFile();
    $file->setName($fileName);

    $file->setParents(array("1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg"));
    $file->setDescription("Foto alumne_" . $_POST["alumne"] . " de la classe_" . $_POST["classe"]);
    $file->setMimeType("image/jpeg");

    $result = $service->files->create(
      $file,
      array(
        'data' => file_get_contents($file_path),
        'mimeType' => 'image/jpeg',
        'uploadType' => 'multipart'
      )
    );

  } else {
    echo "No s'ha enviat cap imatge.";
  }

  // echo '<a href="https://drive.google.com/open?id=' . $result->id . '/view">View File</a>';

} catch (Google_Service_Exception $e) {
  echo $e;
} catch (Exception $e) {
  echo $e;
}

?>