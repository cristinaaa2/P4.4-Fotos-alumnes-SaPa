let classes = [];

window.onload = function() {
	if(window.location.href.includes("/vista")) {	
		peticioClasses();
	} else if(window.location.href.includes("admin")) {
		document.getElementById("Scarpeta").addEventListener("click", function() {
			guardarIDCarpetaDrive();
		});
		document.getElementById("Ecarpeta").addEventListener("click", function() {
			eliminarContingut();
		});
	}
}

function guardarIDCarpetaDrive() {
	let id = document.getElementById("IDcarpeta").value;

	if(id != "") {
		$.ajax({
			url: "../controlador/crear_carpetes.php",
			type: "POST",
			data: {id: id},
			success: function (data) {
				alert(data)
			},
			error: function (xhr, status) {
				alert("No s'ha pogut guardar l'ID");
			}
		});
	} else {
		alert("No has introduït cap ID");
	}
}

function eliminarContingut() {
	$.ajax({
		url: "../controlador/eliminar_fotos.php",
		type: "POST",
		data: "Ecarpeta",
		success: function (data) {
			alert(data)
		},
		error: function (xhr, status) {
			alert("ERROR: No s'ha pogut eliminar el contingut");
		}
	});
}

/**
 * Peticio AJAX per obtenir les dades dels tutors
 */
function peticioClasses() {
	$.ajax({
		url: "../controlador/aplicacio.php",
        type: "GET",
        data: "classes",
        contentType: "application/json",
        success: function (data) {
			let d =  JSON.parse(data);
			if(d.error && d.error == "No hi ha dades") {
				alert("No hi ha cap alumne");
			} else {
				if(window.location.href.includes("/vista/taula.php")) {	
					mostrarTutors(d);
				} else if(window.location.href.includes("/vista/classe.php")) {
					let classe = window.location.search.split("=")[1];
					mostrarClasse(d, classe);
				}
			}
        },
        error: function (xhr, status) {
            alert("No s'ha pogut mostrar els tutors");
        }
	});
}

/**
 * Mostra els tutors amb el nombre d'alumnes que té cada tutor i el nombre de fotos que té cada tutor en una taula.
 * @param {object} dades dades de les classes
 */
function mostrarTutors(dades) {
	try {
		let taula = document.getElementById("tutors");
		let curs;
		let numAlumnes = 0;
		let numFotos = 0;
		taula.innerHTML = "";
		for(let i = 0; i < dades.length; i++) {
			numAlumnes = 0;
			numFotos = 0;
			if(/^[a-z]*$/.test(dades[i].id)) {
				curs = dades[i].cicle + dades[i].curs + dades[i].grup;
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
					let curs = this.id;
					window.location.href = "classe.php?curs=" + curs;
				});
			}
		}
	} catch (error) {
		alert("ERROR: no s'ha pogut mostrar la taula de classes");
	}
}

/**
 * Mostra els alumnes de la classe seleccionada en una taula i crida la funcio per mostrar en cards
 * @param {*} dades dades de les classes
 * @param {*} classe classe seleccionada
 */
function mostrarClasse(dades, classe) {
	try {
		let taula = document.getElementById("alumnes");
		taula.innerHTML = "";
		for(let i = 0; i < dades.length; i++) {
			if(!/^[a-z]*$/.test(dades[i].id)) {
				if(dades[i].cicle + dades[i].curs + dades[i].grup == classe) {
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
						let id = this.id;
						window.location.href = "../controlador/drive.php?alumne=" + id + "&curs=" + classe;
					});
				}
			}
		}
		mostrarClasseCard(dades, classe);
	} catch (error) {
		alert("ERROR: no s'ha pogut mostrar la taula d'alumnes");
	}
}

/**
 * Mostra els alumnes de la classe seleccionada en cards
 * @param {*} dades dades de les classes
 * @param {*} classe classe seleccionada
 */
function mostrarClasseCard(dades, classe) {
	try {
		let img = document.getElementById("img");
		img.innerHTML = "";
		let card = "";
		let file = "";
		for (let i = 0; i < dades.length; i++) {
			if(!/^[a-z]*$/.test(dades[i].id)) {
				if(dades[i].curs + dades[i].cicle + dades[i].grup == classe) {
					if(dades[i].foto == "SI") {
						file = "../fotos/" + dades[i].id + ".jpg";
					} else {
						file = "../vista/sources/img/user.png";
					}
					card = "<div class='col' id='"+ dades[i].id + "C'>" + 
					"<div class='card h-100'>" +
					"<img src='" + file + "' class='card-img-top' alt='" + dades[i].id + "'>" +
					"<div class='card-body'>" +
					"<h5 class='card-title'>" + dades[i].nom + "</h5>" +
					"<p class='card-text'>" + dades[i].curs + " " + dades[i].cicle +  " " + dades[i].grup + "</p>" +
					"</div>" +
					"</div>" +
					"</div>";
					img.innerHTML += card;
					document.getElementById(dades[i].id + "C").addEventListener("click", function() {
						let id = this.id;
						window.location.href = "../controlador/drive.php?alumne=" + id + "&curs=" + classe;
					});
				}
			}
		}
	} catch (error) {
		alert("ERROR: no s'ha pogut mostrar les imatges de la classe");
	}
}