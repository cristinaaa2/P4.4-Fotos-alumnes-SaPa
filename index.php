<?php
session_start();
if (!isset($_SESSION['usuari'])) {
  try {
    // if(isset($_POST["login"])) {
      // Llibreria de client de l'API de Google
      require_once 'google-api-php-client--PHP8.0/vendor/autoload.php';
      // Claus del client i ruta de redireccionament autoritzada
      $clientID = '460822153535-j7c9h85prrbdqbdb7oeh2h3uat230mge.apps.googleusercontent.com';
      $clientSecret = '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';
      $redirectUri = 'http://localhost/Client/P4.4%20-%20Fotos%20alumnes%20SaPa/P4.4-Fotos-alumnes-SaPa/index.php';
    
      // Crear una sol·licitud de client per accedir a l'API de Google
      $client = new Google_Client();
      $client->setClientId($clientID);
      $client->setClientSecret($clientSecret);
      $client->setRedirectUri($redirectUri);
      $client->addScope("email");
      $client->addScope("profile");
    // }
    // Autenticar el codi de Google OAuth Flow
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if(isset($token['access_token'])) {
          $client->setAccessToken($token['access_token']);
        } else {
          header("Location: index.php");
        }
        
        // Obtenir informació del perfil
        $google_oauth = new Google\Service\Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $email =  $google_account_info->email;
        $name =  $google_account_info->name;

        // Si no està registrat, fa el registre introduint les dades a la base de dades.
        
        if(!esProfe($email)) {
          echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>No pots iniciar sessió amb aquest compte de correu.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
          if (comprovarCorreuRegistrat($email)) {
            $_SESSION['usuari'] = $email;
            header("Location: vista/taula.php");
          } else {
            registrarProfe($name, $email);
            $_SESSION['usuari'] = $email;
            header("Location: vista/taula.php");
          }
        }
      
    } else {
      // Si no s'ha autenticat, redirigeix a la pàgina de login
      if(isset($_POST["login"])) {
        header('Location: ' . $client->createAuthUrl());
      }
    }
    } catch(Exception $e){
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: no s'ha pogut iniciar sessio.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
      }
} else {
  echo "Ja has iniciat sessió.";
  header("Location: vista/taula.php");
}
require_once "vista/index.html";

function registrarProfe($name, $email) {
  $file = "model/profes.json";
  if(file_exists($file)) {
    $json = file_get_contents($file);
    $json = json_decode($json, true);
  } else {
    $json = array();
  }
  $json[] = array("nom" => $name, "correu" => $email);
  $json = json_encode($json);
  file_put_contents($file, $json);
}

function comprovarCorreuRegistrat($email) {
  $file = "model/profes.json";
  if(file_exists($file)) {
    $json = file_get_contents($file);
    $json = json_decode($json, true);
  } else {
    $json = array();
  }
  foreach ($json as $key => $value) {
    if ($value['correu'] == $email) {
      return true;
    }
  }
  return false;
}


function esProfe($email) {
  // $pattern = "/^.[\.].@sapalomera.cat$/";
  $pattern = "/.@sapalomera.cat$/";
  if(preg_match($pattern, $email)){
    return true;
  } else {
    return false;
  }
}
?>