# MONUAR

Aplicación web social para registrar países visitados, compartir experiencias de viaje y descubrir recomendaciones. Es el proyecto final del ciclo de Desarrollo de Aplicaciones Web (DAW).

## Antes de empezar

Para ver la aplicación en tu equipo necesitas tener instalado:

- PHP 8.4 o superior.
- Composer.
- MySQL 8 o MariaDB.
- Un navegador web.

Puedes usar **XAMPP** para disponer de PHP y MySQL, o **Docker Desktop** si prefieres levantar los servicios en contenedores. No necesitas Node.js para ejecutar la aplicación.

## Cómo ejecutarlo

### Opción A: XAMPP o MySQL local

1. Clona el repositorio y entra en la aplicación:

   ```bash
   git clone https://github.com/moisessanchezsantos/TFG-Monuar.git
   cd TFG-Monuar/backend
   ```

2. Instala las dependencias:

   ```bash
   composer install
   ```

3. Copia `.env.example` como `.env` y revisa la conexión a MySQL.

4. Crea la base de datos y ejecuta las migraciones:

   ```bash
   php bin/console doctrine:database:create --if-not-exists
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

5. Inicia el servidor local:

   ```bash
   php -S 127.0.0.1:8000 -t public public/index.php
   ```

6. Abre [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Opción B: Docker

Con Docker Desktop abierto y funcionando:

```bash
docker compose -f Proyecto/docker-compose.yml up --build
```

Después abre [http://127.0.0.1:8080](http://127.0.0.1:8080).

## Funcionalidades

- Registro, inicio y cierre de sesión.
- Perfil personal con avatar, biografía y estadísticas.
- Mapa interactivo de países visitados.
- Búsqueda de países y navegación sobre el mapa.
- Publicaciones, fotografías, reseñas y valoraciones.
- Sistema de seguidores entre usuarios.
- Recomendaciones de destinos mediante inteligencia artificial.
- Panel de administración y gestión de usuarios, publicaciones y contenido.
- API para las acciones del mapa, perfiles y publicaciones.
- Protección de contraseñas, sesiones, validación y control de subidas.

## Tecnologías

- PHP 8.4
- Symfony 8
- Doctrine ORM y migraciones
- MySQL
- Twig
- HTML, CSS y JavaScript
- Stimulus y Turbo
- Docker y Docker Compose

## Estructura principal

```text
.
├── backend/
│   ├── assets/          # JavaScript y estilos de Symfony
│   ├── config/          # Configuración y conexión a la base de datos
│   ├── migrations/      # Migraciones de Doctrine
│   ├── public/          # Entrada web, páginas y API
│   ├── src/             # Controladores, entidades y repositorios
│   ├── templates/       # Plantillas Twig
│   ├── composer.json
│   └── .env.example
├── database/             # Datos auxiliares de desarrollo
├── Proyecto/             # Configuración Docker y material del proyecto
├── docs/                 # Documentación técnica
└── arquitectura.png     # Esquema de arquitectura
```

## Despliegue

MONUAR no es una web estática: necesita PHP, una base de datos y un servidor capaz de ejecutar Symfony. Por eso no se puede desplegar directamente en Vercel como el portfolio Angular.

Para una demo pública se recomienda un servicio con soporte para PHP y MySQL, o desplegar el contenedor Docker en un servidor. Para presentarlo en el portfolio, el enlace al repositorio junto con una captura del mapa y otra del perfil es una opción profesional si no hay una demo online estable.

## Estado del proyecto

Proyecto académico completo y en evolución. La configuración incluida está pensada principalmente para desarrollo local. Antes de usarlo en producción habría que configurar secretos del servidor, HTTPS, copias de seguridad, almacenamiento externo para archivos subidos y una base de datos gestionada.

## Autor

Moisés Sánchez Santos  
[GitHub](https://github.com/moisessanchezsantos)