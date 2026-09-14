<?php

function cargarEnv() {

    // Si Docker (u otro entorno) ya cargó las variables,
    // no necesitamos leer el archivo .env.
    if (
        getenv('DB_HOST') !== false &&
        getenv('DB_NAME') !== false &&
        getenv('DB_USER') !== false &&
        getenv('DB_PASSWORD') !== false
    ) {
        return;
    }

    // Para desarrollo local fuera de Docker
    $archivo = __DIR__ . '/../.env';

    if (!file_exists($archivo)) {
        throw new Exception("No se encontró el archivo .env");
    }

    $lineas = file(
        $archivo,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lineas as $linea) {

        $linea = trim($linea);

        if ($linea === '' || str_starts_with($linea, '#')) {
            continue;
        }

        [$nombre, $valor] = explode('=', $linea, 2);

        putenv(
            trim($nombre) . '=' . trim($valor)
        );
    }
}