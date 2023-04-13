
<?php

/**
 * Rep la petició de l'usuari i retorna les dades del JSON de les classes.
 */
if(isset($_GET["classes"])) {
    try {
        $file = "../model/classes.json";
        if(file_exists($file)) {
            $json = file_get_contents($file);
        } else {
            $json = "{\"error\": \"No hi ha dades\"}";
        }
        echo $json;
    } catch(Exception $e) {
        echo "{\"error\": \"No hi ha dades\"}";
    }
}
?>