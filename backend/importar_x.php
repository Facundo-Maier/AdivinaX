<?php

require 'x_api.php';
require 'database.php';

try {

    $inicio = gmdate(
        'Y-m-d\TH:i:s\Z',
        strtotime('-6 days')
    );

    $fin = gmdate(
        'Y-m-d\TH:i:s\Z',
        strtotime('-2 days')
    );

    $resultado = obtenerTweetsDesdeX(
        'a lang:es -is:retweet -is:reply',
        50,
        $inicio,
        $fin
    );

    $usuarios = $resultado['includes']['users'] ?? [];
    $tweets = $resultado['data'] ?? [];

    $pdo->beginTransaction();

    // Guardar usuarios
    foreach ($usuarios as $usuario) {

        $metricas = $usuario['public_metrics'] ?? [];

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (
                x_id,
                username,
                nombre,
                descripcion,
                fecha_creacion,
                verificado,
                seguidores,
                siguiendo,
                likes,
                listas,
                media,
                cantidad_tweets,
                raw_json
            )
            VALUES (
                :x_id,
                :username,
                :nombre,
                :descripcion,
                :fecha_creacion,
                :verificado,
                :seguidores,
                :siguiendo,
                :likes,
                :listas,
                :media,
                :cantidad_tweets,
                :raw_json
            )
            ON DUPLICATE KEY UPDATE
                username = VALUES(username),
                nombre = VALUES(nombre),
                descripcion = VALUES(descripcion),
                fecha_creacion = VALUES(fecha_creacion),
                verificado = VALUES(verificado),
                seguidores = VALUES(seguidores),
                siguiendo = VALUES(siguiendo),
                likes = VALUES(likes),
                listas = VALUES(listas),
                media = VALUES(media),
                cantidad_tweets = VALUES(cantidad_tweets),
                raw_json = VALUES(raw_json)'
        );

        $fechaCreacion = isset($usuario['created_at'])
            ? date('Y-m-d H:i:s', strtotime($usuario['created_at']))
            : null;

        $stmt->execute([
            ':x_id' => $usuario['id'],
            ':username' => $usuario['username'],
            ':nombre' => $usuario['name'],
            ':descripcion' => $usuario['description'] ?? null,
            ':fecha_creacion' => $fechaCreacion,
            ':verificado' => isset($usuario['verified'])
                ? (int) $usuario['verified']
                : null,
            ':seguidores' => $metricas['followers_count'] ?? 0,
            ':siguiendo' => $metricas['following_count'] ?? 0,
            ':likes' => $metricas['like_count'] ?? 0,
            ':listas' => $metricas['listed_count'] ?? 0,
            ':media' => $metricas['media_count'] ?? 0,
            ':cantidad_tweets' => $metricas['tweet_count'] ?? 0,
            ':raw_json' => json_encode(
                $usuario,
                JSON_UNESCAPED_UNICODE
            )
        ]);
    }

    // Guardar tweets
    foreach ($tweets as $tweet) {

        $metricas = $tweet['public_metrics'] ?? [];

        $stmt = $pdo->prepare(
            'INSERT INTO tweets (
                x_id,
                usuario_x_id,
                contenido,
                fecha,
                idioma,
                likes,
                cant_comentarios,
                cant_retweets,
                cant_citas,
                cant_bookmarks,
                impresiones,
                raw_json
            )
            VALUES (
                :x_id,
                :usuario_x_id,
                :contenido,
                :fecha,
                :idioma,
                :likes,
                :cant_comentarios,
                :cant_retweets,
                :cant_citas,
                :cant_bookmarks,
                :impresiones,
                :raw_json
            )
            ON DUPLICATE KEY UPDATE
                contenido = VALUES(contenido),
                fecha = VALUES(fecha),
                idioma = VALUES(idioma),
                likes = VALUES(likes),
                cant_comentarios = VALUES(cant_comentarios),
                cant_retweets = VALUES(cant_retweets),
                cant_citas = VALUES(cant_citas),
                cant_bookmarks = VALUES(cant_bookmarks),
                impresiones = VALUES(impresiones),
                raw_json = VALUES(raw_json)'
        );

        $fecha = date(
            'Y-m-d H:i:s',
            strtotime($tweet['created_at'])
        );

        $stmt->execute([
            ':x_id' => $tweet['id'],
            ':usuario_x_id' => $tweet['author_id'],
            ':contenido' => $tweet['text'],
            ':fecha' => $fecha,
            ':idioma' => $tweet['lang'] ?? null,
            ':likes' => $metricas['like_count'] ?? 0,
            ':cant_comentarios' => $metricas['reply_count'] ?? 0,
            ':cant_retweets' => $metricas['retweet_count'] ?? 0,
            ':cant_citas' => $metricas['quote_count'] ?? 0,
            ':cant_bookmarks' => $metricas['bookmark_count'] ?? 0,
            ':impresiones' => $metricas['impression_count'] ?? 0,
            ':raw_json' => json_encode(
                $tweet,
                JSON_UNESCAPED_UNICODE
            )
        ]);
    }

    $pdo->commit();

    header('Content-Type: application/json');

    echo json_encode([
        'message' => 'Importación completada',
        'usuarios_procesados' => count($usuarios),
        'tweets_procesados' => count($tweets)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}