var stream = obtenirVideo();
async function obtenirVideo() {
  try {
    // if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i)) {
    //     let label = document.getElementById("labelFile");
    //     label.innerHTML = "Fes una foto o carrega'n una des del teu dispositiu";
    // } else {
    //     let div = document.getElementById("m");
    //     let p = document.createElement("p");
    //     let text = document.createTextNode("No estás usando un móvil");
    //     p.appendChild(text);
    //     div.appendChild(p);
    //     console.log("No estás usando un móvil");
    // }
      stream = await navigator.mediaDevices.getUserMedia({ video: true });
      var video = document.getElementById("video");
    //   video.style.display = "block";
      video.srcObject = stream;
      video.play();
      return stream;
  } catch (error) {
      console.error(error);
  }
}


window.onload = function() {
    var canvas = document.getElementById("canvas");
    var context = canvas.getContext("2d");
    let sw = window.innerWidth;
    let sh = window.innerHeight * 0.8;
    let w = 500 * sh / 700;
    let h = sh;
    console.log("w: " + w + " h: " + h);
    canvas.style.width = w + "px";
    canvas.style.height = h + "px";

    var video = document.getElementById("video");
    video.style.width = (window.innerWidth * 0.85) + "px";
    video.style.height = (window.innerHeight * 0.85) + "px";

    
    // Fer la foto
    var foto = document.getElementById("ferfoto");
    let cancelar = document.getElementById("cancelar");
    let guardar = document.getElementById("guardar");
    console.log(foto);
    foto.addEventListener("click", function() {
        try {
            var canvas = document.getElementById("canvas");
            var video = document.getElementById("video");
            video.style.display = "none";
            let sw = window.innerWidth;
            let sh = window.innerHeight;
            let w = 500 * sh / 700;
            let h = sh;
            let x = (sw - w) / 2;
            let y = 0;
            console.log("sw: " + sw + " sh: " + sh + " w: " + w + " h: " + h + " x: " + x + " y: " + y);
            let vidInfo = stream.getVideoTracks()[0].getSettings();
            const context = canvas.getContext("2d");
            context.canvas.width = w;
            context.canvas.height = h;
            foto.hidden = true;
            cancelar.hidden = false;
            guardar.hidden = false;
            context.drawImage(video, x, y, w, h, 0, 0, 500, 700);
        } catch (error) {
            alert("ERROR: no s'ha pogut fer la foto");
        }
    });

    // file = document.getElementById("file");
    // file.addEventListener("change", function() {
    //     try {
    //         if(file.files[0].type == "image/jpeg" || file.files[0].type == "image/jpg") {
    //             var canvas = document.getElementById("canvas");
    //             var video = document.getElementById("video");
    //             video.style.display = "none";
    //             let x = 100;
    //             let y = 0;
    //             let w = 500;
    //             let h = 700;
    //             let vidInfo = stream.getVideoTracks()[0].getSettings();
    //             const context = canvas.getContext("2d");
    //             context.canvas.width = w;
    //             context.canvas.height = h;
    //             foto.hidden = true;
    //             cancelar.hidden = false;
    //             guardar.hidden = false;
    //             var reader = new FileReader();
    //             reader.onload = function(e) {
    //                 var img = new Image();
    //                 img.onload = function() {
    //                     context.drawImage(img, x, y, w, h, 0, 0, w, h);
    //                 };
    //                 img.src = e.target.result;
    //             };
    //             reader.readAsDataURL(file.files[0]);
    //         } else {
    //             alert("El fitxer no és una imatge jpg");
    //             file.value = "";
    //         }
    //     } catch (error) {
    //         alert("ERROR: no s'ha pogut carregar la foto");
    //     }
    // });


    // Guarda la foto
    guardar.addEventListener("click", function() {
        var data = {
            foto: canvas.toDataURL("image/jpg"),
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

    onresize = function() {
        var canvas = document.getElementById("canvas");
        // var context = canvas.getContext("2d");
        let sw = window.innerWidth;
        let sh = window.innerHeight* 0.8;
        let w = 500 * sh / 700;
        let h = sh;
        canvas.style.width = w + "px";
        canvas.style.height = h + "px";
        var video = document.getElementById("video");
        video.style.width = (window.innerWidth * 0.85) + "px";
        video.style.height = (window.innerHeight * 0.85) + "px";
        console.log("w: " + w + " h: " + h);
    }
}
