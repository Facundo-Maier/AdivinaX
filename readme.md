AdivinaTwit

Juego estilo Arcade basado en tweets.

El jugador recibe tweets aleatorios y debe responder preguntas sobre ellos. Las respuestas correctas otorgan puntos y, al finalizar una partida de 10 rondas, puede guardar su puntaje en una leaderboard global.

Actualmente los tweets utilizados son datos de prueba almacenados en MySQL. En una versión futura se planea incorporar la API de X (Twitter).

Tecnologías
Frontend: HTML, CSS, JavaScript
Backend: PHP
Base de datos: MySQL
Contenedores: Docker
Servidor frontend: Live Server
API: REST

------------------------------------------------------------

Arquitectura

┌─────────────────┐
│    Frontend     │
│ HTML/CSS/JS     │
│ localhost:5500  │
└────────┬────────┘
         │
         │ HTTP / JSON
         ▼
┌─────────────────┐
│     Backend     │
│      PHP        │
│ localhost:8000  │
└────────┬────────┘
         │
         │ PDO / SQL
         ▼
┌─────────────────┐
│      MySQL      │
│    Docker       │
└─────────────────┘

------------------------------------------------------------

Requisitos

Antes de ejecutar el proyecto se necesita:

Docker
PHP
Un navegador
Live Server (opcional, pero recomendado para el frontend)
Ejecución
1. Levantar MySQL

Desde la raíz del proyecto:

docker compose up -d

Comprobar que el contenedor está funcionando:

docker compose ps

2. Levantar el backend

Desde la carpeta backend:

php -S localhost:8000

La API quedará disponible en:

http://localhost:8000

3. Levantar el frontend

Abrir frontend/index.html utilizando Live Server.

Por defecto:

http://localhost:5500

------------------------------------------------------------

TODO:
 Mejorar diseño visual
 Agregar más tipos de preguntas
 Categorizar las preguntas para no traer tanto del backend al front
 Mejorar sistema de puntuación
 Integración con API de X
 Obtener tweets reales automáticamente
 Mejorar manejo de errores