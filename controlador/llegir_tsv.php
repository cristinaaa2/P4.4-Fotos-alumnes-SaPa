<?php
session_start();

if (isset($_POST['submit-tsv'])) {
    $target_dir = "../tsv/";
    $target_file = $target_dir . basename($_FILES['arxiu']["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $delimiter = "\n";

    if (move_uploaded_file($_FILES['arxiu']['tmp_name'], $target_file) && $fileType == "tsv") {
        echo "Ficher valid, s'ha pujat l'arxiu.\n";
    } else {
        echo "ERROR no s'ha pogut pujar l'arxiu\n";
    }

    try {
        $target_dir = "../tsv/";
        $target_file = $target_dir . basename($_FILES['arxiu']["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $delimiter = "\n";

        if (move_uploaded_file($_FILES['arxiu']['tmp_name'], $target_file) && $fileType == "tsv") {
            echo "Ficher valid, s'ha pujat l'arxiu.\n";
        } else {
            echo "ERROR no s'ha pogut pujar l'arxiu\n";
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
            putenv("DADES_TSV=$json_string");

            // var_dump($arrayProcessat);
            echo "Importació correcta.";
            header("refresh:3;url=../admin/");
        } else {
            header("refresh:3;url=../admin/");
        }
    } catch (Exception $e) {
        echo "Error al importar les dades.";
    }
}

/**
 * Estilitzar l'array per poder fer el JSON
 */
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
?>