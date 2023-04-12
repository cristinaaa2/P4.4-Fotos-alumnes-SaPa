
<?php
if(isset($_GET["tutor"])) {
    try {
        $tutor = $_GET["tutor"];
        $file = "../model/classes.json";
        if(file_exists($file)) {
            $json = file_get_contents($file);
        } else {
            $json = "{\"error\": \"No hi ha dades\"}";
        }
        echo $json;
    } catch(Exception $e) {
        echo "ERROR: Al obtenir les dades del tutor.";
    }
}

if(isset($_GET["alumnes"])) {
    try {
        $alumne = $_GET["alumnes"];
        $file = "../model/classes.json";
        if(file_exists($file)) {
            $json = file_get_contents($file);
        } else {
            $json = "{\"error\": \"No hi ha dades\"}";
        }
        echo $json;
    } catch(Exception $e) {
        echo "ERROR: Al obtenir les dades dels alumnes.";
    }
}
?>