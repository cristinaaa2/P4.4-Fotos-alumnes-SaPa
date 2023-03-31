var stream = obtenirVideo();

async function obtenirVideo() {
  try {
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
foto.addEventListener("click", function() {
    var canvas = document.getElementById("canvas");
    var video = document.getElementById("video");
    let vidInfo = stream.getVideoTracks()[0].getSettings();
	const context = canvas.getContext("2d");
    context.canvas.width = vidInfo.width;
    context.canvas.height = vidInfo.height;
    context.drawImage(video, 0, 0);
});

// Guarda la foto
var guardar = document.getElementById("guardar");
guardar.addEventListener("click", function() {
    var data = canvas.toDataURL("image/png");
    var link = document.createElement("a");
    link.download = "foto.png";
    link.href = data;
    link.click();
});
}
