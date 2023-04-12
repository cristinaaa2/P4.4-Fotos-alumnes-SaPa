<?php

try {
  if(isset($_POST["foto"]) && isset($_POST["alumne"]) && isset($_POST["classe"])) {

    // Llibreria de client de l'API de Google
    require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
    putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive.file']);

    $imageData = $_POST['foto'];
    $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

    // Crear un nombre de archivo único para la imagen
    $fileName = $_POST["alumne"] . '.jpg';

    // Guardar la imagen en una carpeta del proyecto
    file_put_contents('../fotos/' . $fileName, $decodedData);

    $service = new Google\Service\Drive($client);
    existeFotoDrive($service, $fileName);
    $file_path = "../fotos/" . $fileName;

    $file = new Google\Service\Drive\DriveFile();
    $file->setName($fileName);

    $file->setParents(array("1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg"));
    $file->setDescription("Foto alumne_" . $_POST["alumne"] . " de la classe_" . $_POST["classe"]);
    $file->setMimeType("image/jpg");

    $result = $service->files->create(
      $file,
      array(
        'data' => file_get_contents($file_path),
        'mimeType' => 'image/jpg',
        'uploadType' => 'multipart'
      )
    );
    marcarFoto();
    echo "OK";
  } else {
    session_start();
    if (!isset($_SESSION['usuari'])) {
      header("Location: ../index.php");
    }
    require_once "../vista/foto.html";
  }
} catch (Google_Service_Exception $e) {
  echo "ERROR: Al guardar la imatge al drive.";
} catch (Exception $e) {
  echo "ERROR: Al guardar la imatge.";
}

function existeFotoDrive($service, $fileName) {
  $files = $service->files->listFiles([
    'q' => "name='" . $fileName . "'"
  ]);

  foreach ($files as $file) {
    $service->files->delete($file->id);
  }
   
}

function marcarFoto() {
  $file = "../model/classes.json";
  if(file_exists($file)) {
    $json_str = file_get_contents($file);
    $json = json_decode($json_str, true);
    for($i = 0; $i < count($json); $i++) {
      $curs = $json[$i]['curs']. $json[$i]['cicle'] . $json[$i]['grup'];
      if($json[$i]['id'] == $_POST['alumne'] && $curs == $_POST['classe']) {
        $json[$i]['foto'] = "SI";
        $json_str = json_encode($json);
        file_put_contents($file, $json_str);
      }
    }
  }
}

?>