<?php

try {
  include_once './crear_carpetes.php';
  require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';
  if(isset($_POST["foto"]) && isset($_POST["alumne"]) && isset($_POST["classe"])) {
    if(comprovarAlumne($_POST["alumne"], $_POST["classe"])) {
      echo "La foto no s'ha pogut guardar perquè aquest alumne no existeix. Comprova que les dades dels alumnes són correctes, si no carrega de nou el .tsv.";
    } else {
      // Llibreria de client de l'API de Google
      putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');
      $parentFolderId = IdCarpetaPare();
      if($parentFolderId == "") {
        echo "ERROR: No hi ha cap carpeta pare configurada. Configura-la a la pagina d'admin";
      } else {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(['https://www.googleapis.com/auth/drive.file']);
        

        $imageData = $_POST['foto'];
        $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

        // Crear un nom per la imatge
        $fileName = $_POST["alumne"] . '.jpg';
        $folderName = $_POST["classe"];

        // Guardar la imagen en una carpeta del servidor
        if (!file_exists('../fotos/' . $folderName)) {
          mkdir('../fotos/' . $folderName, 0777, true);
        }
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
          echo "ERROR: La carpeta de la classe no existeix. Contacta amb l'administrador o configura un altre carpeta del drive.";
        }
      }
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
    if($json != null) {
      for($i = 0; $i < count($json); $i++) {
        $curs = $json[$i]['cicle'] . $json[$i]['curs']. $json[$i]['grup'];
        if($json[$i]['id'] == $_POST['alumne'] && $curs == $_POST['classe']) {
          $json[$i]['foto'] = "SI";
          $json_str = json_encode($json);
          file_put_contents($file, $json_str);
        }
      }
    }
  }
}

/**
 * Comprova si l'alumne amb la classe esta al JSON
 */
function comprovarAlumne($alumne, $classe) {
  $file = "../model/classes.json";
  if(file_exists($file)) {
    $json_str = file_get_contents($file);
    $json = json_decode($json_str, true);
    if($json != null) {
      for($i = 0; $i < count($json); $i++) {
        $curs = $json[$i]['cicle'] . $json[$i]['curs']. $json[$i]['grup'];
        if($json[$i]['id'] == $alumne && $curs == $classe) {
          return false;
        }
      }
      return true;
    }
    return true;
  }
}

?>