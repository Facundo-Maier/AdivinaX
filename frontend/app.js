let tweetActual = null;
let preguntaActual = null;
let puntajeActual = 0;
let rondaActual = 0;
let tweetsUsados = [];

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


const tiposPregunta = [
    {
        tipo: "likes",
        texto: "¿Cuántos likes tiene este tweet?",
        rangos: [
            { min: 0, max: 0, texto: "0" },
            { min: 1, max: 2, texto: "1 - 2" },
            { min: 3, max: 10, texto: "3 - 10" },
            { min: 11, max: Infinity, texto: "11+" }
        ]
    },

    {
        tipo: "seguidores",
        texto: "¿Cuántos seguidores tiene el autor?",
        rangos: [
            { min: 0, max: 100, texto: "0 - 100" },
            { min: 101, max: 500, texto: "101 - 500" },
            { min: 501, max: 2500, texto: "501 - 2.500" },
            { min: 2501, max: Infinity, texto: "2.501+" }
        ]
    },

    {
        tipo: "siguiendo",
        texto: "¿A cuántas cuentas sigue el autor?",
        rangos: [
            { min: 0, max: 200, texto: "0 - 200" },
            { min: 201, max: 500, texto: "201 - 500" },
            { min: 501, max: 1000, texto: "501 - 1.000" },
            { min: 1001, max: Infinity, texto: "1.001+" }
        ]
    },

    {
        tipo: "tweets_usuario",
        texto: "¿Cuántos posts ha publicado aproximadamente esta cuenta?",
        rangos: [
            { min: 0, max: 4000, texto: "0 - 4.000" },
            { min: 4001, max: 15000, texto: "4.001 - 15.000" },
            { min: 15001, max: 55000, texto: "15.001 - 55.000" },
            { min: 55001, max: Infinity, texto: "55.001+" }
        ]
    },

    {
        tipo: "likes_usuario",
        texto: "¿Cuántos likes ha dado esta cuenta?",
        rangos: [
            { min: 0, max: 4000, texto: "0 - 4.000" },
            { min: 4001, max: 40000, texto: "4.001 - 40.000" },
            { min: 40001, max: 120000, texto: "40.001 - 120.000" },
            { min: 120001, max: Infinity, texto: "120.001+" }
        ]
    }
];

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

        const respuesta = await fetch(
            "http://localhost:8000/api/tweets/random"
        );

        if (!respuesta.ok) {
            throw new Error(
                "El servidor respondió con HTTP " + respuesta.status
            );
        }

        const tweet = await respuesta.json();

        if (tweetsUsados.includes(tweet.x_id)) {
            return cargarTweet();
        }

        tweetActual = tweet;

        tweetsUsados.push(tweet.x_id);

        usuario.textContent = "@" + tweet.usuario;
        contenido.textContent = tweet.contenido;

        document.getElementById("ronda").textContent =
            "Ronda " + rondaActual + "/" + totalRondas;

        resultado.textContent = "";

        generarPregunta();

    } catch (error) {

        console.error("Error al cargar el tweet:", error);

        resultado.textContent =
            "⚠️ No se pudo cargar el tweet.";
    }
}


function comprobarRespuesta(event) {

    const boton = event.target;

    const min = Number(boton.dataset.min);
    const max = Number(boton.dataset.max);

    const respuestaCorrecta = obtenerRespuestaCorrecta();
    
    const respuestaFormateada = respuestaCorrecta.toLocaleString("es-ES");

    const botones =
    document.querySelectorAll("#opciones button");

        
    botones.forEach(function(boton) {
        boton.disabled = true;
    });

    if (
        respuestaCorrecta >= min &&
        respuestaCorrecta <= max
    ) {

        resultado.textContent =
            "¡Correcto! +100 puntos. La respuesta era " +
            respuestaFormateada + ".";

        puntajeActual += 100;
        puntaje.textContent = puntajeActual;

    } else {

        resultado.textContent =
            "Incorrecto. La respuesta era " + respuestaFormateada + ".";
    }
    btnSiguiente.hidden = false;
}

function generarPregunta() {

    const indice = Math.floor(
        Math.random() * tiposPregunta.length
    );

    preguntaActual = tiposPregunta[indice];

    document.getElementById("textoPregunta").textContent =
        preguntaActual.texto;

    generarOpciones();
}


function generarOpciones() {

    const opciones = document.getElementById("opciones");

    opciones.innerHTML = "";

    preguntaActual.rangos.forEach(function(rango) {

        const boton = document.createElement("button");

        boton.textContent = rango.texto;

        boton.dataset.min = rango.min;
        boton.dataset.max = rango.max;

        boton.addEventListener(
            "click",
            comprobarRespuesta
        );

        opciones.appendChild(boton);
    });
}

function obtenerRespuestaCorrecta() {

    if (preguntaActual.tipo === "likes") {
        return tweetActual.likes;
    }

    if (preguntaActual.tipo === "seguidores") {
        return tweetActual.seguidores;
    }

    if (preguntaActual.tipo === "siguiendo") {
        return tweetActual.siguiendo;
    }

    if (preguntaActual.tipo === "tweets_usuario") {
        return tweetActual.cantidad_tweets;
    }

    if (preguntaActual.tipo === "likes_usuario") {
        return tweetActual.likes_usuario;
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