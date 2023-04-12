<?php
session_start();

if (isset($_POST['submit-tsv'])) {
    $target_dir = "../tsv/";
    $target_file = $target_dir . basename($_FILES['arxiu']["name"]);
    $delimiter = "\n";

    if (move_uploaded_file($_FILES['arxiu']['tmp_name'], $target_file)) {
        echo "File is valid, and was successfully uploaded.\n";
    } else {
        echo "Possible file upload attack!\n";
    }

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

        //Hacer un split del array y convertirlo a json
        $json_string = json_encode($arrayProcessat);
        $arxiu = '../model/classes.json';
        file_put_contents($arxiu, $json_string);

        var_dump($arrayProcessat);
        header("Location: ../admin/");
    } else {
        header("Location: ../admin/");
    }
}

function estilitzarArray($data) {
    $arrayProcessat = array();

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
}

/**
 * Comprovar si l'arxiu passat per l'usuari es correcte
 * @param target_file la ruta de la foto.
 * @param imageFileType format de la imatge
 */
function comprovarImatge($target_file, $imageFileType) {
    $error = "";
    
    $check = getimagesize($_FILES["imatge"]["tmp_name"]);
    if($check == false) {
        $error .= "ERROR. L'arxiu no és una imatge.";
    }

    //Comprovar si l'arxiu ja existeix
    if (file_exists($target_file)) {
    $error .= "ERROR. L'arxiu ja existeix.";
    }

    //Comprovar la mida de l'arxiu
    if ($_FILES["imatge"]["size"] > 500000) {
    $error .= "ERROR. El tamany de l'arxiu és molt gran.";
    }

    //Permetre només uns certs formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    $error .= "ERROR. Només arxius JPG, JPEG, PNG i GIF estàn permesos.";
    }

    return $error;
}

?>