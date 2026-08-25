# AdivinaX

Juego estilo Arcade basado en tweets.

El jugador recibe tweets aleatorios y debe responder preguntas sobre ellos. Las respuestas correctas otorgan puntos y, al finalizar una partida de 10 rondas, puede guardar su puntaje en una leaderboard global.

Los tweets utilizados durante las partidas se almacenan localmente en MySQL. La API de X se utiliza únicamente para importar nuevos tweets y datos públicos de sus autores; el juego no consulta X durante cada partida.

## Tecnologías

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Base de datos:** MySQL
- **Contenedores:** Docker
- **Servidor frontend:** Live Server
- **API:** REST
- **Fuente de datos:** X API

---

## Arquitectura

```text
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
```

---

## Requisitos

Antes de ejecutar el proyecto se necesita:

- Docker
- PHP
- PHP cURL
- Un navegador
- Live Server (opcional, pero recomendado para el frontend)

Crear un archivo `.env` en la raíz del proyecto tomando como referencia `.env.example`.

No publicar tokens o contraseñas reales.

## Ejecución

### 1. Levantar MySQL

Desde la raíz del proyecto:

```bash
docker compose up -d
```

Comprobar que el contenedor está funcionando:

```bash
docker compose ps
```

### 2. Levantar el backend

Desde la carpeta `backend`:

```bash
php -S localhost:8000
```

La API quedará disponible en:

```text
http://localhost:8000
```

### 3. Levantar el frontend

Abrir `frontend/index.html` utilizando Live Server.

Por defecto:

```text
http://localhost:5500
```

---

## Importar tweets desde X

AdivinaTwit no consulta X durante las partidas.

Los tweets deben importarse previamente y quedan almacenados en MySQL.

Para realizar una importación:

1. Configurar las credenciales de X en `.env`.
2. Levantar MySQL.
3. Levantar el backend PHP.
4. Ejecutar:

```bash
curl http://localhost:8000/importar_x.php
```

---

## TODO

- Mejorar diseño visual
- Agregar más tipos de preguntas
- Mejorar tiempos de respuesta
- Mejorar sistema de puntuación
- Mejorar manejo de errores
- Preparar deploy/self-hosting