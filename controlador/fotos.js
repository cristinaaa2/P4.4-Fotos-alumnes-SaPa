var stream = obtenirVideo();
async function obtenirVideo() {
  try {
    // if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i)) {
    //     let div = document.getElementById("m");
    //     let p = document.createElement("p");
    //     let text = document.createTextNode("Estás usando un dispositivo móvil!!");
    //     p.appendChild(text);
    //     div.appendChild(p);
    //     console.log("Estás usando un dispositivo móvil!!");
    // } else {
        // let div = document.getElementById("m");
        // let p = document.createElement("p");
        // let text = document.createTextNode("No estás usando un móvil");
        // p.appendChild(text);
        // div.appendChild(p);
        // console.log("No estás usando un móvil");
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
// Fer la foto
var foto = document.getElementById("ferfoto");
let cancelar = document.getElementById("cancelar");
let guardar = document.getElementById("guardar");
console.log(foto);
foto.addEventListener("click", function() {
    var canvas = document.getElementById("canvas");
    var video = document.getElementById("video");
    video.style.display = "none";
    let x = 100;
    let y = 0;
    let w = 500;
    let h = 700;
    let vidInfo = stream.getVideoTracks()[0].getSettings();
	const context = canvas.getContext("2d");
    context.canvas.width = w;
    context.canvas.height = h;
    foto.hidden = true;
    cancelar.hidden = false;
    guardar.hidden = false;
    context.drawImage(video, x, y, w, h, 0, 0, w, h);
});

// Guarda la foto
guardar.addEventListener("click", function() {
    var data = {
        foto: canvas.toDataURL("image/jpeg"),
        alumne: window.location.search.split("=")[1].split("&")[0],
        classe: window.location.search.split("=")[2]
    };
    console.log(data);
    $.ajax({
		url: "../controlador/drive.php",
        type: "POST",
        data: data,
        success: function (data) {
            alert(data);
        },
        error: function (xhr, status) {
            alert("No s'ha pogut mostrar les classes");
        }
	});

    // Descargar la foto
    // var link = document.createElement("a");
    // link.download = window.location.search.split("=")[1] + ".jpg";
    // link.href = data;
    // link.click();
});

// Cancel·la la foto
cancelar.addEventListener("click", function() {
    var video = document.getElementById("video");
    video.style.display = "block";
    var canvas = document.getElementById("canvas");
    var context = canvas.getContext("2d");
    foto.hidden = false;
    cancelar.hidden = true;
    guardar.hidden = true;
    context.clearRect(0, 0, canvas.width, canvas.height);
});
}
