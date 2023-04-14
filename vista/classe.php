<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../vista/sources/img/logo-sapa.png" type="image/x-icon">
    <link rel="stylesheet" href="sources/estils.css">
    <script type="module" src="../controlador/aplicacio.js"></script>
    <title>Document</title>
</head>
<body>
    <?php
        session_start();
        if (!isset($_SESSION['usuari'])) {
            header("Location: ../index.php");
        }
    ?>
    <nav class="navbar navbar-expand-lg nonprintable">
        <a class="navbar-brand" href="https://www.sapalomera.cat/">
            <img src="sources/img/logo-sapa.png" width="150px">
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
    <a class="btn btn-light mx-5 mt-4" href="javascript:history.back()">Tornar</a>
    <ul class="nav nav-tabs m-5" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="taula-tab" data-bs-toggle="tab" data-bs-target="#taula-tab-pane" type="button" role="tab" aria-controls="taula-tab-pane" aria-selected="true"><i class="bi bi-table"></i></button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="cards-tab" data-bs-toggle="tab" data-bs-target="#cards-tab-pane" type="button" role="tab" aria-controls="cards-tab-pane" aria-selected="false"><i class="bi bi-person-vcard"></i></button>
        </li>
    </ul>
    <div class="tab-content m-5" id="myTabContent">
        <div class="tab-pane fade show active" id="taula-tab-pane" role="tabpanel" aria-labelledby="taula-tab" tabindex="0">
            <div class="m-5 col-10">
                <table class="table table-info table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody id="alumnes">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="cards-tab-pane" role="tabpanel" aria-labelledby="cards-tab" tabindex="0">
            <div class="row row-cols-1 row-cols-md-5 g-4" id="img">

            </div>
        </div>
    </div>   
</body>
</html>