// import { existsSync, readFile, writeFile, copyFileSync, unlinkSync, writeFileSync, readFileSync } from 'fs';
let classes = [];
function obtenirClasse() {
    // Agafar les dades de classes.json
    try {
		let dades = readFileSync("../model/classes.json", "utf8");
		classes = JSON.parse(dades);
        console.log(classes);
	} catch(err) {
		console.log("No hi han dades");
		classes = [];
	}
}

function guardarIDCarpedaDrive() {
	let id = document.getElementById("IDcarpeta").value;

	if(id != "") {
		classes[0].id = id;
		console.log(classes);
	} else {
		alert("No has introduït cap ID");
	}
}

// obtenirClasse();