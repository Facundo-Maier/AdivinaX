<?php

function obtenerTiposPregunta() {

    return [

        'likes' => [
            'campo' => 'likes',
            'texto' => '¿Cuántos likes tiene este tweet?',
            'opciones' => [
                [
                    'id' => 'likes_0',
                    'texto' => '0',
                    'min' => 0,
                    'max' => 0
                ],
                [
                    'id' => 'likes_1_2',
                    'texto' => '1 - 2',
                    'min' => 1,
                    'max' => 2
                ],
                [
                    'id' => 'likes_3_10',
                    'texto' => '3 - 10',
                    'min' => 3,
                    'max' => 10
                ],
                [
                    'id' => 'likes_11_mas',
                    'texto' => '11+',
                    'min' => 11,
                    'max' => PHP_INT_MAX
                ]
            ]
        ],

        'seguidores' => [
            'campo' => 'seguidores',
            'texto' => '¿Cuántos seguidores tiene el autor?',
            'opciones' => [
                [
                    'id' => 'seguidores_0_100',
                    'texto' => '0 - 100',
                    'min' => 0,
                    'max' => 100
                ],
                [
                    'id' => 'seguidores_101_500',
                    'texto' => '101 - 500',
                    'min' => 101,
                    'max' => 500
                ],
                [
                    'id' => 'seguidores_501_2500',
                    'texto' => '501 - 2.500',
                    'min' => 501,
                    'max' => 2500
                ],
                [
                    'id' => 'seguidores_2501_mas',
                    'texto' => '2.501+',
                    'min' => 2501,
                    'max' => PHP_INT_MAX
                ]
            ]
        ],

        'siguiendo' => [
            'campo' => 'siguiendo',
            'texto' => '¿A cuántas cuentas sigue el autor?',
            'opciones' => [
                [
                    'id' => 'siguiendo_0_200',
                    'texto' => '0 - 200',
                    'min' => 0,
                    'max' => 200
                ],
                [
                    'id' => 'siguiendo_201_500',
                    'texto' => '201 - 500',
                    'min' => 201,
                    'max' => 500
                ],
                [
                    'id' => 'siguiendo_501_1000',
                    'texto' => '501 - 1.000',
                    'min' => 501,
                    'max' => 1000
                ],
                [
                    'id' => 'siguiendo_1001_mas',
                    'texto' => '1.001+',
                    'min' => 1001,
                    'max' => PHP_INT_MAX
                ]
            ]
        ],

        'tweets_usuario' => [
            'campo' => 'cantidad_tweets',
            'texto' => '¿Cuántos posts ha publicado aproximadamente esta cuenta?',
            'opciones' => [
                [
                    'id' => 'tweets_0_4000',
                    'texto' => '0 - 4.000',
                    'min' => 0,
                    'max' => 4000
                ],
                [
                    'id' => 'tweets_4001_15000',
                    'texto' => '4.001 - 15.000',
                    'min' => 4001,
                    'max' => 15000
                ],
                [
                    'id' => 'tweets_15001_55000',
                    'texto' => '15.001 - 55.000',
                    'min' => 15001,
                    'max' => 55000
                ],
                [
                    'id' => 'tweets_55001_mas',
                    'texto' => '55.001+',
                    'min' => 55001,
                    'max' => PHP_INT_MAX
                ]
            ]
        ],

        'likes_usuario' => [
            'campo' => 'likes_usuario',
            'texto' => '¿Cuántos likes ha dado esta cuenta?',
            'opciones' => [
                [
                    'id' => 'likes_usuario_0_4000',
                    'texto' => '0 - 4.000',
                    'min' => 0,
                    'max' => 4000
                ],
                [
                    'id' => 'likes_usuario_4001_40000',
                    'texto' => '4.001 - 40.000',
                    'min' => 4001,
                    'max' => 40000
                ],
                [
                    'id' => 'likes_usuario_40001_120000',
                    'texto' => '40.001 - 120.000',
                    'min' => 40001,
                    'max' => 120000
                ],
                [
                    'id' => 'likes_usuario_120001_mas',
                    'texto' => '120.001+',
                    'min' => 120001,
                    'max' => PHP_INT_MAX
                ]
            ]
        ]
    ];
}


function obtenerPreguntaAleatoria() {

    $tipos = obtenerTiposPregunta();

    $tipo = array_rand($tipos);

    return [
        'tipo' => $tipo,
        'configuracion' => $tipos[$tipo]
    ];
}


function prepararPreguntaParaFrontend($tipo, $configuracion) {

    return [
        'tipo' => $tipo,
        'texto' => $configuracion['texto'],
        'opciones' => array_map(
            function ($opcion) {

                return [
                    'id' => $opcion['id'],
                    'texto' => $opcion['texto']
                ];
            },
            $configuracion['opciones']
        )
    ];
}

function obtenerConfiguracionPregunta($tipo) {

    $tipos = obtenerTiposPregunta();

    if (!isset($tipos[$tipo])) {
        return null;
    }

    return $tipos[$tipo];
}


function obtenerOpcionPregunta($configuracion, $opcionId) {

    foreach ($configuracion['opciones'] as $opcion) {

        if ($opcion['id'] === $opcionId) {
            return $opcion;
        }
    }

    return null;
}


function comprobarOpcion($valor, $opcion) {

    return $valor >= $opcion['min']
        && $valor <= $opcion['max'];
}