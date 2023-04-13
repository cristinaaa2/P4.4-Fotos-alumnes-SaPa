window.onload = function() {

    // 
    const video = document.getElementById('video');
    let canvas = document.getElementById("canvas");
    let canvas2 = document.getElementById("canvas2");
    let sw = window.innerWidth;
    let sh = window.innerHeight * 0.8;
    let w = 500 * sh / 700;
    let h = sh;
    canvas.style.width = w + "px";
    canvas.style.height = h + "px";

    
    video.style.width = (window.innerWidth * 0.85) + "px";
    video.style.height = (window.innerHeight * 0.85) + "px";

    // Canviar de càmera
    const cameraSelect = document.getElementById('camera-select');
    let currentStream;

    navigator.mediaDevices.enumerateDevices()
    .then(devices => {
        const cameras = devices.filter(device => device.kind === 'videoinput');
        cameras.forEach(camera => {
        const option = document.createElement('option');
        option.value = camera.deviceId;
        option.text = camera.label || `Camera ${cameraSelect.length + 1}`;
        cameraSelect.appendChild(option);
        });
    })
    .catch(error => {
        alert("ERROR: no s'ha pogut mostrar la llista de càmeres");
    });

    cameraSelect.addEventListener('change', event => {
        const selectedDeviceId = event.target.value;
        const constraints = {
            video: {
            deviceId: selectedDeviceId
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            const newTrack = stream.getVideoTracks()[0];
            const oldTrack = currentStream.getVideoTracks()[0];
            video.srcObject = stream;
            currentStream.removeTrack(oldTrack);
            currentStream.addTrack(newTrack);
            video.play();
        })
        .catch(error => {
            alert("ERROR: no s'ha pogut canviar la càmera");
        });
    });

    // Accedir a la càmera
    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
        currentStream = stream;
        video.play();
    })
    .catch(error => {
        alert("ERROR: no s'ha pogut accedir a la càmera");
    });

    
    // Fer la foto
    var foto = document.getElementById("ferfoto");
    let cancelar = document.getElementById("cancelar");
    let guardar = document.getElementById("guardar");
    foto.addEventListener("click", function() {
        try {
            var video = document.getElementById("video");
            video.style.display = "none";
            let sw = window.innerWidth;
            let sh = window.innerHeight;
            let w = 500 * sh / 700;
            let h = sh;
            let x = (sw - w) / 2;
            let y = 0;
            console.log("sw: " + sw + " sh: " + sh + " w: " + w + " h: " + h + " x: " + x + " y: " + y);
            let vidInfo = currentStream.getVideoTracks()[0].getSettings();
            const context = canvas.getContext("2d");
            const context2 = canvas2.getContext("2d");
            context2.canvas.width = 500;
            context2.canvas.height = 700;
            foto.hidden = true;
            cancelar.hidden = false;
            guardar.hidden = false;
            context.drawImage(video, x, y, w, h, 0, 0, 500, 700);
            context2.drawImage(video, x, y, 500, 700, 0, 0, 500, 700);
        } catch (error) {
            alert("ERROR: Hi ha hagut un error al fer la foto");
        }
    });

    // Guarda la foto
    guardar.addEventListener("click", function() {
        var data = {
            foto: canvas2.toDataURL("image/jpg"),
            alumne: window.location.search.split("=")[1].split("&")[0],
            classe: window.location.search.split("=")[2]
        };
        $.ajax({
            url: "../controlador/drive.php",
            type: "POST",
            data: data,
            success: function (data) {
                if(data == "OK") {
                  alert("La imatge s'ha guardar correctament al servidor i al drive.");  
                } else {
                    alert("Hi ha hagut un error al guardar la foto");
                }
                
            },
            error: function (xhr, status) {
                alert("No s'ha pogut guardar la foto");
            }
        });
    });

    // Cancel·la la foto
    cancelar.addEventListener("click", function() {
        try {
            var video = document.getElementById("video");
            video.style.display = "block";
            var canvas = document.getElementById("canvas");
            var context = canvas.getContext("2d");
            foto.hidden = false;
            cancelar.hidden = true;
            guardar.hidden = true;
            context.clearRect(0, 0, canvas.width, canvas.height);
        } catch (error) {
            alert("ERROR: no s'ha pogut cancel·lar la foto");
        }
    });

    // Redimensiona la càmera
    onresize = function() {
        // var canvas = document.getElementById("canvas");
        let sw = window.innerWidth;
        let sh = window.innerHeight* 0.8;
        let w = 500 * sh / 700;
        let h = sh;
        canvas.style.width = w + "px";
        canvas.style.height = h + "px";
        // var video = document.getElementById("video");
        video.style.width = (window.innerWidth * 0.85) + "px";
        video.style.height = (window.innerHeight * 0.85) + "px";
        console.log("w: " + w + " h: " + h);
    }
}
