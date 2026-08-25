<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5500");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

try {

    require 'database.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


    // Manejar preflight de CORS
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit;
    }


    // =====================================================
    // GET /api/tweets/random
    // =====================================================

    if ($method === 'GET' && $path === '/api/tweets/random') {

        $stmt = $pdo->query(
            'SELECT
                t.x_id,
                t.usuario_x_id,
                t.contenido,
                t.fecha,
                t.idioma,
                t.likes,
                t.cant_comentarios,
                t.cant_retweets,
                t.cant_citas,
                t.cant_bookmarks,
                t.impresiones,

                u.username AS usuario,
                u.nombre AS nombre_usuario,
                u.descripcion AS descripcion_usuario,
                u.fecha_creacion AS fecha_creacion_usuario,
                u.verificado,
                u.seguidores,
                u.siguiendo,
                u.likes AS likes_usuario,
                u.listas,
                u.media,
                u.cantidad_tweets

            FROM tweets t
            JOIN usuarios u
                ON t.usuario_x_id = u.x_id
            ORDER BY RAND()
            LIMIT 1'
        );

        $tweet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tweet === false) {
            http_response_code(404);

            echo json_encode([
                'error' => 'No hay tweets disponibles'
            ]);

            exit;
        }

        echo json_encode($tweet);
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