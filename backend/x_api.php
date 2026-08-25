<?php

require_once 'env.php';

function obtenerTweetsDesdeX($query, $cantidad = 10) {

    cargarEnv();

    $token = getenv('X_BEARER_TOKEN');

    if (!$token) {
        throw new Exception("No se encontró X_BEARER_TOKEN");
    }

    $url = 'https://api.x.com/2/tweets/search/recent';

    $params = http_build_query([
        'query' => $query,
        'max_results' => $cantidad,
        'tweet.fields' => 'created_at,public_metrics,lang,author_id',
        'expansions' => 'author_id',
        'user.fields' => 'username,name,public_metrics,created_at'
    ]);

    $ch = curl_init($url . '?' . $params);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]
    ]);

    $respuesta = curl_exec($ch);

    if ($respuesta === false) {
        throw new Exception(
            'Error cURL: ' . curl_error($ch)
        );
    }

    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($codigo < 200 || $codigo >= 300) {
        throw new Exception(
            "X API respondió HTTP $codigo: $respuesta"
        );
    }

    return json_decode($respuesta, true);
}

function obtenerUsuarioDesdeX($userId) {

    cargarEnv();

    $token = getenv('X_BEARER_TOKEN');

    if (!$token) {
        throw new Exception("No se encontró X_BEARER_TOKEN");
    }

    $url = 'https://api.x.com/2/users/' . $userId;

    $params = http_build_query([
        'user.fields' => 'created_at,description,location,public_metrics,username,name,verified'
    ]);

    $ch = curl_init($url . '?' . $params);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]
    ]);

    $respuesta = curl_exec($ch);

    if ($respuesta === false) {
        throw new Exception(
            'Error cURL: ' . curl_error($ch)
        );
    }

    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($codigo < 200 || $codigo >= 300) {
        throw new Exception(
            "X API respondió HTTP $codigo: $respuesta"
        );
    }

    return json_decode($respuesta, true);
}