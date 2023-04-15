<?php 
session_start();
use Google\Client;
use Google\Service\Drive;
require '../google-api-php-client--PHP8.0/vendor/autoload.php';
require '../vendor/autoload.php';

if (!isset($_SESSION['usuari'])) {
  header("Location: ../index.php");
}

putenv('GOOGLE_APPLICATION_CREDENTIALS=clave_drive.json');

// Llegir el fitxer de dades i les importa en un JSON
if (isset($_POST['submit-tsv'])) {
  try {
      if(isset($_SESSION['VAR_GLOBAL'])) {
          $dataGlobal = json_decode($_SESSION['VAR_GLOBAL'], true);
          if(!isset($dataGlobal["id"])) {
              echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Errora: No s'ha pogut crear la carpeta de Google Drive, s'ha d'especificar l'id de la carpeta.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>"; 
              header("refresh:3;url=./index.php");
              exit();
          }
      } else {
          echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Errosr: No s'ha pogut crear la carpeta de Google Drive, s'ha d'especificar l'id de la carpeta.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
          header("refresh:3;url=./index.php");
          exit();
      }
      include_once '../controlador/eliminar_fotos.php';
      $target_dir = "../tsv/";
      $target_file = $target_dir . basename($_FILES['arxiu']["name"]);
      $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
      $delimiter = "\n";
      if (move_uploaded_file($_FILES['arxiu']['tmp_name'], $target_file) && $fileType == "tsv") {
          $tsv = fopen($target_file, "r");

          if ($tsv) {
              $data = array();

              while (!feof($tsv)) {
                  $line = fgets($tsv);

                  $campsUsuari = preg_split("/[\t]/", $line);

                  if($campsUsuari[0] != "") {
                      array_push($data, $campsUsuari);
                  }
              }

              fclose($tsv);

              $arrayProcessat = estilitzarArray($data);

              $json_string = json_encode($arrayProcessat);
              $arxiu = '../model/classes.json';
              file_put_contents($arxiu, $json_string);
              $dataGlobal["classes"] = $arrayProcessat;
              $_SESSION['VAR_GLOBAL'] = json_encode($dataGlobal);
              eliminarCarpetaServidor('../fotos/*');
              general();
              echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>Importació feta correctament.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        
              header("refresh:3;url=./");
          } else {
              echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: Error al open el arxiu.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        
              header("refresh:3;url=./");
          }
      } else {
          echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: El tipus d'arxiu no és correcte.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    
          header("refresh:3;url=./");
      }
  } catch (Exception $e) {
      echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Error al importar les dades.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";

      header("refresh:3;url=./");
  }
}

if(isset($_POST["id"])) {
  $id = array();
  $id["id"] = $_POST["id"];
  $json = json_encode($id);
  $_SESSION['VAR_GLOBAL'] = $json;
  //general();
  echo "Carpetes creades correctament!";
}

function general() {
  try {
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
      $id = json_decode($_SESSION['VAR_GLOBAL'], true)["id"];
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
              'parents' => array($id)));
          $file = $driveService->files->create($fileMetadata, array(
              'fields' => 'id'));
  } catch(Exception $e) {
     echo "Error Message: ".$e;
  }
}

/**
* Estilitzar l'array per poder fer el JSON
*/
function estilitzarArray($data) {
  $arrayProcessat = array();
  try {
      for ($i = 0; $i < count($data); $i++) {
          $arrayProcessat[$i]["id"] = $data[$i][0];
          $arrayProcessat[$i]["nom"] = $data[$i][1];
          $arrayProcessat[$i]["cicle"] = $data[$i][2];
          $arrayProcessat[$i]["curs"] = preg_split("/\s/", $data[$i][3])[0];
          $arrayProcessat[$i]["grup"] = isset($data[$i][4]) ? preg_split("/\s/", $data[$i][4])[0] : "";
          if(preg_match("/^\w\./", $data[$i][0])) {
              $arrayProcessat[$i]["foto"] = "NO";
          }
      }

      return $arrayProcessat;
  } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
  }
}

include "../vista/admin/index.html";
?>