<?php
session_start();
if (!isset($_SESSION['usuari'])) {
  try {
    // Llibreria de client de l'API de Google
    require_once 'google-api-php-client--PHP8.0/vendor/autoload.php';
    // Claus del client i ruta de redireccionament autoritzada
    $clientID = '460822153535-j7c9h85prrbdqbdb7oeh2h3uat230mge.apps.googleusercontent.com';
    $clientSecret = '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';
    $redirectUri = rutaLogin();
    if($redirectUri == "") {
      echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>No hi ha cap ruta de login configurada. Contacta amb l'administrador.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }
    // Crear una sol·licitud de client per accedir a l'API de Google
    $client = new Google_Client();
    $client->setClientId($clientID);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");
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

/**
 * Registra l'usuari del profe si no esta registrat.
 */
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

/**
 * Comprova si el correu del profe està registrat en el JSON o no.
 */
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

/**
 * Valida que el correu sigui d'un profe.
 */
function esProfe($email) {
  $pattern = "/^\w[^\.]\w@sapalomera\.cat$/";
  // $pattern = "/.@sapalomera.cat$/";
  if(preg_match($pattern, $email)){
    return true;
  } else {
    return false;
  }
}

/**
 * Extreu la ruta pel login de Google del fitxer config.json.
 */
function rutaLogin() {
  $file = "model/config.json";
    if(file_exists($file)) {
        $json = file_get_contents($file);
        $config = json_decode($json, true);
        if(isset($config["RUTA_LOGIN"])) {
            return $config["RUTA_LOGIN"];
        } else {
            return "";
        }
    } else {
       return "";
    }
}
?>