<?php
// Tancar la sessió, treu les variables de _SESSION i porta a la pàgina del Login.
session_start();

session_destroy();

$_SESSION = array();

header("Location: ../vista/index.html");
?>