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
    <link rel="stylesheet" href="sources/estils.css">
    <script type="module" src="../controlador/aplicacio.js"></script>
    <title>Tutors</title>
</head>
<body>
    <?php
        session_start();
        if (!isset($_SESSION['usuari'])) {
            header("Location: ../index.php");
        }
    ?>
    <nav class="navbar navbar-expand-lg">
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
    <div class="m-5 col-6">
        <table class="table table-info">
            <thead class="table-dark">
                <tr>
                    <th>Tutor</th>
                    <th>Curs</th>
                    <th>Num Alumnes</th> 
                </tr>
            </thead>
            <tbody id="tutors">
            </tbody>
        </table>
    </div>
</body>
</html>