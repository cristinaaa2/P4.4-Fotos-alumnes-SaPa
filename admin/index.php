<?php 
session_start();
if (!isset($_SESSION['usuari'])) {
  header("Location: ../index.php");
}
require_once "../vista/admin/index.html";
?>