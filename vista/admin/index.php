<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../vista/sources/estils.css">
    <script type="module" src="../controlador/aplicacio.js"></script>
    <link rel="shortcut icon" href="../vista/sources/img/logo-sapa.png" type="image/x-icon">
    <title>Admin</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="https://www.sapalomera.cat/">
            <img src="../vista/sources/img/logo-sapa.png" width="150px">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link text-light"><strong><?php echo $_SESSION["usuari"] ?></strong></a>
                    <a class="nav-link text-light btn btn-danger mx-2" href="../controlador/logout.php"><i class="bi bi-box-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </nav>
    <div class="content">
        <div class="m-5 col-3 bg-primary-subtle rounded">
            <div class="p-2">
                <div class="m-3">
                    <label for="IDcarpeta" class="form-label">ID de la carpeta:</label>
                    <input type="text" class="form-control" id="IDcarpeta">
                    <small>*Al posar un nou ID, importa el tsv del les classes per carregar les noves dades i es creïn les carpetes de cada curs.</small>
                </div>
                <div class="m-3">
                    <button id="Scarpeta" class="btn btn-success mb-2">Seleccionar Carpeta</button>
                    <button id="Ecarpeta" class="btn btn-danger mb-2">Eliminar Tot El Contingut</button>
                </div>
                <form action="../controlador/llegir_tsv.php" method="POST" enctype="multipart/form-data">
                    <div class="m-3">
                        <label for="formFile" class="form-label">Fitxer .tsv:</label>
                        <input type="file" class="form-control" id="formFile" name="arxiu">
                        <!-- <small>*Al carregar el fitxer .tsv es posaran les fotos a 0.</small> -->
                    </div>
                    <div class="m-3">
                        <input type="submit" class="btn btn-primary" value="Confirmar" name="submit-tsv">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>