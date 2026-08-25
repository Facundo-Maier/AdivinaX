CREATE TABLE usuarios (
    x_id VARCHAR(30) PRIMARY KEY,

    username VARCHAR(100) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,

    fecha_creacion DATETIME NULL,
    verificado BOOLEAN NULL,

    seguidores INT UNSIGNED NOT NULL DEFAULT 0,
    siguiendo INT UNSIGNED NOT NULL DEFAULT 0,
    likes INT UNSIGNED NOT NULL DEFAULT 0,
    listas INT UNSIGNED NOT NULL DEFAULT 0,
    media INT UNSIGNED NOT NULL DEFAULT 0,
    cantidad_tweets INT UNSIGNED NOT NULL DEFAULT 0,

    raw_json JSON NULL
);


CREATE TABLE tweets (
    x_id VARCHAR(30) PRIMARY KEY,

    usuario_x_id VARCHAR(30) NOT NULL,

    contenido TEXT NOT NULL,
    fecha DATETIME NOT NULL,
    idioma VARCHAR(10) NULL,

    likes INT UNSIGNED NOT NULL DEFAULT 0,
    cant_comentarios INT UNSIGNED NOT NULL DEFAULT 0,
    cant_retweets INT UNSIGNED NOT NULL DEFAULT 0,
    cant_citas INT UNSIGNED NOT NULL DEFAULT 0,
    cant_bookmarks INT UNSIGNED NOT NULL DEFAULT 0,
    impresiones BIGINT UNSIGNED NOT NULL DEFAULT 0,

    raw_json JSON NULL,

    CONSTRAINT fk_tweets_usuario
        FOREIGN KEY (usuario_x_id)
        REFERENCES usuarios(x_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE jugadores (
    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,
    puntaje INT UNSIGNED NOT NULL DEFAULT 0
);