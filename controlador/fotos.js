let sw;
let sh;
let nh;
let vh;
let vw;
let ch;
let cw;
let canvas;
let canvas2;
let video;
window.onload = function() {
    video = document.getElementById('video');
    canvas = document.getElementById("canvas");
    canvas2 = document.getElementById("canvas2");
    initMida();

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
            initMida();
            video.style.display = "none";
            let vidInfo = currentStream.getVideoTracks()[0].getSettings();
            console.log(vidInfo);
            cw = vidInfo.height * 500 / 700;
            ch = vidInfo.height;
            x = (vidInfo.width - cw) / 2;
            y = 0;
            console.log("x: " + x + " y: " + y + " cw: " + cw + " ch: " + ch);
            const context = canvas.getContext("2d");
            const context2 = canvas2.getContext("2d");
            foto.hidden = true;
            cancelar.hidden = false;
            guardar.hidden = false;
            context.drawImage(video, x, y, cw, ch, 0, 0, cw, ch);
            context2.drawImage(video, x, y, cw, ch, 0, 0, 500, 700);
        } catch (error) {
            alert("ERROR: Hi ha hagut un error al fer la foto.");
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
            video.style.display = "block";
            initMida();
            canvas = document.getElementById("canvas");
            var context = canvas.getContext("2d");
            foto.hidden = false;
            cancelar.hidden = true;
            guardar.hidden = true;
            context.clearRect(0, 0, canvas.width, canvas.height);
        } catch (error) {
            alert("ERROR: no s'ha pogut cancel·lar la foto");
        }
    });

}

 // Redimensiona la càmera
onresize = function() {
    initMida();
}

function initMida() {
    sw = window.innerWidth;
    sh = window.innerHeight;
    let nav = document.getElementsByTagName("nav")[0];

    nh = nav.getBoundingClientRect().height;
    video.style.height = ((sh - nh) * 0.98) + "px";
    vh = video.style.height.split("px")[0];
    vw = video.getBoundingClientRect().width;
    console.log(" vw: " + vw + " vh: " + vh);
    
    canvas.width = vh * 500 / 700;
    canvas.height = vh;
    ch = canvas.width;
    cw = canvas.height;
}
