let classes = [];

function guardarIDCarpedaDrive() {
	let id = document.getElementById("IDcarpeta").value;

	if(id != "") {
		classes[0].id = id;
		console.log(classes);
	} else {
		alert("No has introduït cap ID");
	}
}

window.onload = function() {
	if(window.location.href.includes("/vista/taula.html")) {
		console.log(window.location);	
		peticioTutors();
	} else if(window.location.href.includes("/vista/classe.html")) {
		let classe = window.location.search.split("=")[1];
		peticioClasses(classe);
	}
}

function peticioTutors() {
	$.ajax({
		url: "../controlador/aplicacio.php",
        type: "GET",
        data: "tutor",
        contentType: "application/json",
        success: function (data) {
            let d =  JSON.parse(data);
			console.log(d);
			mostrarTutors(d);
        },
        error: function (xhr, status) {
            alert("No s'ha pogut mostrar els tutors");
        }
	});
}

function mostrarTutors(dades) {
	let taula = document.getElementById("tutors");
	let curs;
	let numAlumnes = 0;
	let numFotos = 0;
	for(let i = 0; i < dades.length; i++) {
		numAlumnes = 0;
		numFotos = 0;
		if(/^[a-z]*$/.test(dades[i].id)) {
			curs = dades[i].curs + dades[i].cicle + dades[i].grup;
			for(let j = 0; j < dades.length; j++) {
				if(dades[j].curs == dades[i].curs && dades[j].cicle == dades[i].cicle && dades[j].grup == dades[i].grup) {
					if(dades[j].foto) {
						numAlumnes++;
						if(dades[j].foto == "SI") {
							numFotos++;
						}
					}
				}
			}
			let tr = document.createElement("tr");
			let td = document.createElement("td");
			let text = document.createTextNode(dades[i].nom);
			td.appendChild(text);
			let td2 = document.createElement("td");
			let text2 = document.createTextNode(curs);
			td2.appendChild(text2);
			let td3 = document.createElement("td");
			let text3 = document.createTextNode(numFotos + "/" + numAlumnes);
			td3.appendChild(text3);
			tr.appendChild(td);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.setAttribute("id", curs);
			taula.appendChild(tr);

			document.getElementById(curs).addEventListener("click", function() {
				window.location.href = "classe.html?curs=" + curs;
			});
		}
	}
}

function peticioClasses(classe) {
	$.ajax({
		url: "../controlador/aplicacio.php",
        type: "GET",
        data: "alumnes",
        contentType: "application/json",
        success: function (data) {
            let d =  JSON.parse(data);
			mostrarClasse(d, classe);
        },
        error: function (xhr, status) {
            alert("No s'ha pogut mostrar les classes");
        }
	});
}

function mostrarClasse(dades, classe) {
	let taula = document.getElementById("alumnes");
	for(let i = 0; i < dades.length; i++) {
		if(!/^[a-z]*$/.test(dades[i].id)) {
			if(dades[i].curs + dades[i].cicle + dades[i].grup == classe) {
				let tr = document.createElement("tr");
				let td = document.createElement("td");
				let text = document.createTextNode(dades[i].id);
				td.appendChild(text);
				let td2 = document.createElement("td");
				let text2 = document.createTextNode(dades[i].nom);
				td2.appendChild(text2);
				let td3 = document.createElement("td");
				let text3 = document.createTextNode(dades[i].foto);
				td3.appendChild(text3);
				tr.appendChild(td);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.setAttribute("id", dades[i].id);
				taula.appendChild(tr);
				document.getElementById(dades[i].id).addEventListener("click", function() {
					window.location.href = "foto.html?alumne=" + dades[i].id + "&curs=" + classe;
				});
			}
		}
	}
}