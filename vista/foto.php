<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://apis.google.com/js/platform.js"></script>
    <link rel="stylesheet" href="../vista/sources/estils.css">
    <link rel="shortcut icon" href="../vista/sources/img/logo-sapa.png" type="image/x-icon">
    <script src="../controlador/fotos.js"></script>
    <title>Foto</title>
</head>
<body class="bg-primary-subtle">
    <nav class="navbar navbar-expand-lg nonprintable">
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
    <div class="i">
        <a class="btn btn-light mx-5 mt-4" href="javascript:history.back()">Tornar</a>
        <button type="button" class="btn btn-dark mt-4" data-bs-toggle="modal" data-bs-target="#selectCam"><i class="bi bi-camera-video"></i></button>
    </div>
    <div class="sect m-5">
        <video id="video" class="position-absolute top-50 start-50 translate-middle m-5 border-5 border-dark rounded-3"></video>
        <div class="foto bg-light border rounded-5" id="botons">
            <button id="ferfoto" type="button" class="btn fs-1"><i class="bi bi-camera-fill"></i></button>
            <button class="btn fs-1" id="cancelar" hidden><i class="bi bi-x-circle-fill"></i></button>
            <button class="btn fs-2" id="guardar" hidden><i class="bi bi-save-fill"></i></button><br>
        </div>
        <canvas id="canvas" class="position-absolute top-50 start-50 translate-middle m-5"></canvas>
        <canvas id="canvas2" width="500" height="700" hidden></canvas>
    </div>
      <div class="modal fade" id="selectCam" tabindex="-1" aria-labelledby="selectCamLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="selectCamLabel">Selecciona una camara:</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select id="camera-select"></select>
            </div>
          </div>
        </div>
      </div>
</body>
</html>