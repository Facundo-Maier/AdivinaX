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
│    Navegador    │
└────────┬────────┘
         │
         │ HTTP:8080
         ▼
┌─────────────────┐
│     Nginx       │
│    Frontend     │
│   HTML/CSS/JS   │
└────────┬────────┘
         │
         │ API
         ▼
┌─────────────────┐
│   Apache + PHP  │
│    Backend      │
└────────┬────────┘
         │
         │ SQL
         ▼
┌─────────────────┐
│      MySQL      │
└─────────────────┘
```
Nginx, PHP y MySQL se ejecutan en contenedores Docker.
---

## Requisitos

Antes de ejecutar el proyecto se necesita:

- Docker
- Docker Compose
- Un navegador
- Una cuenta y credenciales de la API de X para importar tweets

Crear un archivo `.env` en la raíz del proyecto tomando como referencia `.env.example`.

No publicar tokens o contraseñas reales.

## Ejecución

### 1. Configurar variables de entorno

Crear .env a partir de .env.example y configurar las credenciales necesarias.

### 2. Levantar AdivinaTwit

Desde raíz del proyecto:

```bash
docker compose up -d --build
```

Comprobar que los contenedores están funcionando:

```bash
docker compose ps
```

La aplicación estará disponible en:

```bash
http://localhost:8080
```
---

## Importar tweets desde X

AdivinaTwit no consulta X durante las partidas.

Los tweets deben importarse previamente y quedan almacenados en MySQL.
La importación está restringida a ejecución administrativa desde CLI y no se encuentra disponible a través de la aplicación web.

Para realizar una importación:

1. Configurar las credenciales de X en `.env`.
2. Levantar los contenedores.
3. Ejecutar:

```bash
curl http://localhost:8000/importar_x.php
```

---

## Self-hosting

AdivinaTwit puede ejecutarse localmente utilizando Docker y ser utilizado desde otros dispositivos.
Se podra acceder en:

```bash
http://IP-DEL-SERVIDOR:8080
```
---

## TODO

- Mejorar diseño visual
- Agregar más tipos de preguntas
- Mejorar sistema de puntuación
- Mejorar manejo de errores
- Preparar deploy público