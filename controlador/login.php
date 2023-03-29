<?php
// Llibreria de client de l'API de Google
require_once '../google-api-php-client--PHP8.0/vendor/autoload.php';

session_start();
if (!isset($_SESSION['usuari'])) {
  // Claus del client i ruta de redireccionament autoritzada
  $clientID = '460822153535-j7c9h85prrbdqbdb7oeh2h3uat230mge.apps.googleusercontent.com';
  $clientSecret = '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';
  $redirectUri = 'http://localhost/Client/P4.4%20-%20Fotos%20alumnes%20SaPa/controlador/login.php';
    
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
    $client->setAccessToken($token['access_token']);
    
    // Obtenir informació del perfil
    $google_oauth = new Google\Service\Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;

    // Si no està registrat, fa el registre introduint les dades a la base de dades.
    try {
      if (comprovarCorreuRegister($email)) {
        $_SESSION['usuari'] = $name;
        header("Location: ../vista/taula.html");
      } else {
        registrarProfe($name, $email);
      }
    } catch(Exception $e){
      echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: no s'ha pogut iniciar sessio.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }
  } else {
    // Si no s'ha autenticat, redirigeix a la pàgina de login
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
  }
  // require_once "../vista/index.html";
} else {
  header("Location: ../vista/taula.html");
}

function registrarProfe($name, $email) {
  $file = "../model/profes.json";
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

function comprovarCorreuRegister($email) {
  $file = "../model/profes.json";
  if(file_exists($file)) {
    echo "hola";
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
?>