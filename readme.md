AdivinaTwit

Juego estilo Arcade basado en tweets.

El jugador recibe tweets aleatorios y debe responder preguntas sobre ellos. Las respuestas correctas otorgan puntos y, al finalizar una partida de 10 rondas, puede guardar su puntaje en una leaderboard global.

Los tweets utilizados durante las partidas se almacenan localmente en MySQL. La API de X se utiliza únicamente para importar nuevos tweets y datos públicos de sus autores; el juego no consulta X durante cada partida.

Tecnologías
Frontend: HTML, CSS, JavaScript
Backend: PHP
Base de datos: MySQL
Contenedores: Docker
Servidor frontend: Live Server
API: REST
API: REST
Fuente de datos: X API

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
PHP cURL
Un navegador
Live Server (opcional, pero recomendado para el frontend)

Crear un archivo .env en la raíz del proyecto tomando como referencia .env.example.
No publicar tokens o contraseñas reales.

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

Importar tweets desde X

AdivinaTwit no consulta X durante las partidas.

Los tweets deben importarse previamente y quedan almacenados en MySQL.

Para realizar una importación:

Configurar las credenciales de X en .env.
Levantar MySQL.
Levantar el backend PHP.
Ejecutar:
curl http://localhost:8000/importar_x.php

------------------------------------------------------------

TODO:
 Mejorar diseño visual
 Agregar más tipos de preguntas
 Categorizar las preguntas para no traer tantos datos del backend al frontend
 Mejorar sistema de puntuación
 Mejorar manejo de errores
 Ajustar los rangos de respuestas utilizando datos reales
 Preparar deploy/self-hosting