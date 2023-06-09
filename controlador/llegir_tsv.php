<?php
// Llegir el fitxer de dades i les importa en un JSON
if (isset($_POST['submit-tsv'])) {
    try {
        include_once './crear_carpetes.php';
        $target_dir = "../tsv/";
        $target_file = $target_dir . basename($_FILES['arxiu']["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $delimiter = "\n";
        if (move_uploaded_file($_FILES['arxiu']['tmp_name'], $target_file) && $fileType == "tsv") {
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

                $dataBDD = file_get_contents("../model/classes.json");
                $classes = json_decode($dataBDD, true);
                $existeix = false;
                if($classes !== null) {
                    //afegir l'usuari del tsv al json si no existeix a la base de dades i si existeix, actualitzar les dades
                    for ($j = 0; $j < count($arrayProcessat); $j++) {
                        $existeix = false;
                        for ($i = 0; $i < count($classes); $i++) {
                            if ($classes[$i]["id"] == $arrayProcessat[$j]["id"]) {
                                if($classes[$i]["curs"] != $arrayProcessat[$j]["curs"]) {
                                    $classes[$i]["curs"] = $arrayProcessat[$j]["curs"];
                                    if(isset($arrayProcessat[$j]["foto"])) {
                                        $classes[$i]["foto"] = "NO";
                                    }
                                } else if($classes[$i]["grup"] != $arrayProcessat[$j]["grup"]) {
                                    $classes[$i]["grup"] = $arrayProcessat[$j]["grup"];
                                    if(isset($arrayProcessat[$j]["foto"])) {
                                        $classes[$i]["foto"] = "NO";
                                    }
                                } else if($classes[$i]["cicle"] != $arrayProcessat[$j]["cicle"]) {
                                    $classes[$i]["cicle"] = $arrayProcessat[$j]["cicle"];
                                    if(isset($arrayProcessat[$j]["foto"])) {
                                        $classes[$i]["foto"] = "NO";
                                    }
                                }
                                $existeix = true;
                            }
                        }
                        if(!$existeix) {
                            array_push($classes, $arrayProcessat[$j]);
                        }
                    }
                    $json_string = json_encode($classes);
                } else {
                    $json_string = json_encode($arrayProcessat);
                }

                $arxiu = '../model/classes.json';
                file_put_contents($arxiu, $json_string);

                if(generarCarpetesTSV()) {
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>Importació feta correctament.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                    require_once "../admin/index.php";
                    header("refresh:3;url=../admin/");
                } else {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: Error al crear les carpetes.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                    require_once "../admin/index.php";
                    header("refresh:3;url=../admin/");
                }
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: Error al open el arxiu.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                require_once "../admin/index.php";
                header("refresh:3;url=../admin/");
            }
        } else {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>ERROR: El tipus d'arxiu no és correcte.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            require_once "../admin/index.php";
            header("refresh:3;url=../admin/");
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Error al importar les dades.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        require_once "../admin/index.php";
        header("refresh:3;url=../admin/");
    }
}

/**
 * Estilitzar l'array per poder fer el JSON
 */
function estilitzarArray($data) {
    $arrayProcessat = array();
    try {
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
    } catch (Exception $e) {
        echo "Error: Al estilitzar l'array.";
    }
}
?>