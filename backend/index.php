<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5500");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

try {

    require 'database.php';
    require 'preguntas.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


    // Manejar preflight de CORS
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    // =====================================================
    // GET /api/jugadores/top
    // =====================================================

    else if ($method === 'GET' && $path === '/api/jugadores/top') {

        $stmt = $pdo->query(
            "SELECT nombre, puntaje
             FROM jugadores
             ORDER BY puntaje DESC
             LIMIT 10"
        );

        $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($jugadores);

    }

    // =====================================================
    // GET /api/ronda
    // =====================================================

    else if ($method === 'GET' && $path === '/api/ronda'){

        
        $stmt = $pdo->query(
            "SELECT
                t.x_id,
                t.contenido,
                u.username AS usuario
            FROM tweets t
            JOIN usuarios u
                ON t.usuario_x_id = u.x_id
            ORDER BY RAND()
            LIMIT 1"
        );

        $tweet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tweet === false) {
            http_response_code(404);

            echo json_encode([
                'error' => 'No hay tweets disponibles'
            ]);

            exit;
        }

        $pregunta = obtenerPreguntaAleatoria();

        $preguntaFrontend = prepararPreguntaParaFrontend(
            $pregunta['tipo'],
            $pregunta['configuracion']
        );

        echo json_encode([
            'tweet_id' => $tweet['x_id'],
            'usuario' => $tweet['usuario'],
            'contenido' => $tweet['contenido'],
            'pregunta' => $preguntaFrontend
        ]);

        exit;
    }


    // =====================================================
    // POST /api/jugadores
    // =====================================================

    else if ($method === 'POST' && $path === '/api/jugadores') {

        $datos = json_decode(
            file_get_contents("php://input"),
            true
        );


        // Si no existe el valor, utilizamos un valor
        // por defecto.
        $nombre = trim($datos['nombre'] ?? '');
        $puntaje = $datos['puntaje'] ?? null;


        // Validar datos
        if (
            empty($nombre) ||
            strlen($nombre) > 100 ||
            !is_numeric($puntaje) ||
            $puntaje < 0 ||
            floor($puntaje) != $puntaje
        ) {

            http_response_code(400);

            echo json_encode([
                'error' => 'Datos invalidos'
            ]);

            exit;
        }


        // Insertar jugador
        $stmt = $pdo->prepare(
            'INSERT INTO jugadores (nombre, puntaje)
             VALUES (:nombre, :puntaje)'
        );

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':puntaje', $puntaje);


        if ($stmt->execute()) {

            http_response_code(201);

            echo json_encode([
                'message' => 'Jugador agregado exitosamente'
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                'error' => 'Error al agregar el jugador'
            ]);
        }

    }

    // =====================================================
    // POST /api/respuesta
    // =====================================================

    else if ($method === 'POST' && $path === '/api/respuesta') {

        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        $tweetId = $body['tweet_id'] ?? null;
        $tipo = $body['tipo'] ?? null;
        $opcionId = $body['opcion'] ?? null;

        if (!$tweetId || !$tipo || !$opcionId) {

            http_response_code(400);

            echo json_encode([
                'error' => 'Faltan datos para comprobar la respuesta'
            ]);

            exit;
        }

        $configuracion = obtenerConfiguracionPregunta($tipo);

        if ($configuracion === null) {

            http_response_code(400);

            echo json_encode([
                'error' => 'Tipo de pregunta inválido'
            ]);

            exit;
        }

        $opcion = obtenerOpcionPregunta(
            $configuracion,
            $opcionId
        );

        if ($opcion === null) {

            http_response_code(400);

            echo json_encode([
                'error' => 'Opción inválida'
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT
                t.likes,
                u.seguidores,
                u.siguiendo,
                u.cantidad_tweets,
                u.likes AS likes_usuario
            FROM tweets t
            JOIN usuarios u
                ON t.usuario_x_id = u.x_id
            WHERE t.x_id = ?"
        );

        $stmt->execute([$tweetId]);

        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datos === false) {

            http_response_code(404);

            echo json_encode([
                'error' => 'Tweet no encontrado'
            ]);

            exit;
        }

        $campo = $configuracion['campo'];
        $valorCorrecto = (int) $datos[$campo];

        $correcta = comprobarOpcion(
            $valorCorrecto,
            $opcion
        );

        echo json_encode([
            'correcta' => $correcta,
            'respuesta_correcta' => $valorCorrecto,
            'puntos' => $correcta ? 100 : 0
        ]);

        exit;
    }


    // =====================================================
    // Ruta no encontrada
    // =====================================================

    else {

        http_response_code(404);

        echo json_encode([
            'error' => 'Ruta no encontrada'
        ]);
    }


} catch (PDOException $e) {

    // Error relacionado con MySQL/PDO

    http_response_code(500);

    echo json_encode([
        'error' => 'Error de conexion con la base de datos'
    ]);

    exit;
}