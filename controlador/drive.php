<?php

try {
  if(isset($_POST["foto"]) && isset($_POST["alumne"]) && isset($_POST["classe"])) {

    // Llibreria de client de l'API de Google
    require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
    putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive.file']);
    $parentFolderId = '1cEVsO_nPDjo-H-HM3em_yxBPRFbCQNfg';

    $imageData = $_POST['foto'];
    $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

    // Crear un nom per la imatge
    $fileName = $_POST["alumne"] . '.jpg';
    $folderName = $_POST["classe"];

    // Guardar la imagen en una carpeta del servidor
    file_put_contents('../fotos/' . $folderName . '/' . $fileName, $decodedData);

    // Guardar la imatge al drive
    $service = new Google\Service\Drive($client);
    $optParams = array(
      'q' => "mimeType='application/vnd.google-apps.folder' and trashed=false and name='".$folderName."' and '".$parentFolderId."' in parents",
      'fields' => 'files(id)'
    );
  
    $results = $service->files->listFiles($optParams);

    if (count($results->files) > 0) {
      $folderId = $results->files[0]->id;
      existeFotoDrive($service, $fileName);
      $file_path = "../fotos/" . $folderName . '/' . $fileName;

      $file = new Google\Service\Drive\DriveFile();
      $file->setName($fileName);
      $file->setParents(array($folderId));
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
      echo "ERROR: La carpeta de la classe no existeix. Contacta amb l'administrador.";
    }
  } else {
    session_start();
    if (!isset($_SESSION['usuari'])) {
      header("Location: ../index.php");
    }
    require_once "../vista/foto.php";
  }
} catch (Google_Service_Exception $e) {
  echo "ERROR: Al guardar la imatge al drive.";
} catch (Exception $e) {
  echo "ERROR: Al guardar la imatge.";
}

/**
 * Comprova si la foto ja existeix al drive i la borra.
 */
function existeFotoDrive($service, $fileName) {
  $files = $service->files->listFiles([
    'q' => "name='" . $fileName . "'"
  ]);

  foreach ($files as $file) {
    $service->files->delete($file->id);
  }
   
}

/**
 * Marca la foto del alumne com a "SI" en el JSON de classes.
 */
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