
<?php
if(isset($_GET["tutor"])) {
    $tutor = $_GET["tutor"];
    $file = "../model/classes.json";
    if(file_exists($file)) {
        $json = file_get_contents($file);
    } else {
        $json = [];
    }
    echo $json;
}

if(isset($_GET["alumnes"])) {
    $alumne = $_GET["alumnes"];
    $file = "../model/classes.json";
    if(file_exists($file)) {
        $json = file_get_contents($file);
    } else {
        $json = [];
    }
    echo $json;
}
?>