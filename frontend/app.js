let tweetActual = null;
let preguntaActual = null;
let puntajeActual = 0;
let rondaActual = 0;
let tweetsUsados = [];
let proximaRonda = null;

const totalRondas = 10;
const inputNombre = document.getElementById("nombreJugador");
const btnEmpezar = document.getElementById("btnEmpezar");

const inicio = document.getElementById("inicio");
const juego = document.getElementById("juego");

const jugador = document.getElementById("jugador");
const puntaje = document.getElementById("puntaje");

const usuario = document.getElementById("usuario");
const contenido = document.getElementById("contenido");

const resultado = document.getElementById("resultado");

const btnSiguiente = document.getElementById("btnSiguiente");

const fin = document.getElementById("fin");
const puntajeFinal = document.getElementById("puntajeFinal");
const btnGuardar = document.getElementById("btnGuardar");
const mensajeGuardado = document.getElementById("mensajeGuardado");
const btnJugarDeNuevo = document.getElementById("btnJugarDeNuevo");

btnEmpezar.addEventListener("click", empezarJuego);

btnSiguiente.addEventListener("click", siguienteRonda);

btnGuardar.addEventListener("click",guardarPuntaje);

btnJugarDeNuevo.addEventListener("click",volverAlInicio);



async function empezarJuego() {

    const nombre = inputNombre.value;

    if(nombre.trim() === "") {
        alert("⚠️Por favor, ingresa tu nombre⚠️");
        return;
    }

    btnGuardar.disabled = false;

    jugador.textContent = nombre;

    inicio.hidden = true;
    juego.hidden = false;
    fin.hidden = true;

    rondaActual = 1;
    tweetsUsados = [];
    puntajeActual = 0;

    puntaje.textContent = puntajeActual;

    btnSiguiente.hidden = true;

    await cargarTweet();
}


async function cargarTweet() {

    try {

        let ronda;

        if (proximaRonda !== null) {

            ronda = proximaRonda;
            proximaRonda = null;

        } else {

            ronda = await obtenerRonda();
        }

        if (tweetsUsados.includes(ronda.tweet_id)) {
            return cargarTweet();
        }

        mostrarRonda(ronda);

    } catch (error) {

        console.error(
            "Error al cargar la ronda:",
            error
        );

        resultado.textContent =
            "⚠️ No se pudo cargar la ronda.";
    }
}

async function comprobarRespuesta(event) {

    const boton = event.target;

    const opcionElegida = boton.dataset.opcion;

    const botones =
        document.querySelectorAll("#opciones button");

    botones.forEach(function(boton) {
        boton.disabled = true;
    });

    try {
        resultado.textContent = "Comprobando...";

        const respuesta = await fetch(
            "http://localhost:8000/api/respuesta",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    tweet_id: tweetActual.tweet_id,
                    tipo: preguntaActual.tipo,
                    opcion: opcionElegida
                })
            }
        );

        if (!respuesta.ok) {
            throw new Error(
                "El servidor respondió con HTTP " +
                respuesta.status
            );
        }

        const datos = await respuesta.json();

        const respuestaFormateada =
            Number(datos.respuesta_correcta)
                .toLocaleString("es-ES");

        if (datos.correcta) {

            resultado.textContent =
                "¡Correcto! +" +
                datos.puntos +
                " puntos. La respuesta era " +
                respuestaFormateada +
                ".";

            puntajeActual += datos.puntos;

            puntaje.textContent = puntajeActual;

        } else {

            resultado.textContent =
                "Incorrecto. La respuesta era " +
                respuestaFormateada +
                ".";
        }

        btnSiguiente.hidden = false;
        
        if (rondaActual < totalRondas) {
          precargarSiguienteRonda();
        }

    } catch (error) {

        console.error(
            "Error al comprobar la respuesta:",
            error
        );

        resultado.textContent =
            "⚠️ No se pudo comprobar la respuesta.";

        // Si falló la petición permitimos intentar otra vez.
        botones.forEach(function(boton) {
            boton.disabled = false;
        });
    }
}

function mostrarPregunta(pregunta) {

    preguntaActual = pregunta;

    document.getElementById("textoPregunta").textContent =
        pregunta.texto;

    const opciones = document.getElementById("opciones");

    opciones.innerHTML = "";

    pregunta.opciones.forEach(function(opcion) {

        const boton = document.createElement("button");

        boton.textContent = opcion.texto;
        boton.dataset.opcion = opcion.id;

        boton.addEventListener(
            "click",
            comprobarRespuesta
        );

        opciones.appendChild(boton);
    });
}

async function obtenerRonda() {

    const respuesta = await fetch(
        "http://localhost:8000/api/ronda"
    );

    if (!respuesta.ok) {
        throw new Error(
            "El servidor respondió con HTTP " +
            respuesta.status
        );
    }

    return await respuesta.json();
}

function mostrarRonda(ronda) {

    tweetActual = ronda;
    preguntaActual = ronda.pregunta;

    tweetsUsados.push(ronda.tweet_id);

    usuario.textContent = "@" + ronda.usuario;
    contenido.textContent = ronda.contenido;

    document.getElementById("ronda").textContent =
        "Ronda " + rondaActual + "/" + totalRondas;

    resultado.textContent = "";

    mostrarPregunta(ronda.pregunta);
}

async function precargarSiguienteRonda() {
    try {
        let ronda = await obtenerRonda();

        while (tweetsUsados.includes(ronda.tweet_id)) {
            ronda = await obtenerRonda();
        }

        proximaRonda = ronda;

    } catch (error) {
        console.error(
            "Error al precargar la siguiente ronda:",
            error
        );
    }
}

async function siguienteRonda() {

  rondaActual++;
  btnSiguiente.hidden = true;

  if (rondaActual > totalRondas) {
    terminarJuego();
    return;
  }

  await cargarTweet();
}

function terminarJuego() {
  juego.hidden = true;
  fin.hidden = false;
  puntajeFinal.textContent = puntajeActual;
  mensajeGuardado.textContent = "";
  btnGuardar.disabled = false;
}

async function leaderboard() {

  const respuesta = await fetch(
    "http://localhost:8000/api/jugadores/top"
  );
  
  const jugadores = await respuesta.json();

    const lista = document.getElementById("listaLeaderboard");

  lista.innerHTML = "";

  jugadores.forEach(function(jugador) {
        const item = document.createElement("li");

        item.textContent =
            jugador.nombre + " - " + jugador.puntaje + " puntos";

        lista.appendChild(item);
    });
}

async function guardarPuntaje() {
    
    btnGuardar.disabled = true;


    try {

        const datos = {
            nombre: inputNombre.value,
            puntaje: puntajeActual
        };

        const respuesta = await fetch(
            "http://localhost:8000/api/jugadores",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(datos)
            }
        );

        if (!respuesta.ok) {
            throw new Error("No se pudo guardar el puntaje");
        }

        const resultado = await respuesta.json();

        console.log(resultado);
        mensajeGuardado.textContent = "¡Puntaje guardado!";
        await leaderboard();

    } catch (error) {

        console.error(error);

        // Si falló, permitimos volver a intentarlo
        btnGuardar.disabled = false;
    }
}

function volverAlInicio() {

    fin.hidden = true;
    juego.hidden = true;
    inicio.hidden = false;

    inputNombre.value = "";
    mensajeGuardado.textContent = "";

    inputNombre.focus();
}

document.addEventListener("DOMContentLoaded", function() {
    leaderboard();
});