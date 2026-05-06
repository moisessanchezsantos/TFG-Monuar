# Sistema de Login y Mapas Personalizados - MONAR

## Descripción

Se ha implementado un sistema completo de autenticación de usuarios donde cada usuario tiene su propio mapa interactivo 3D con países visitados personalizados.

## Funcionalidades implementadas

### 1. Sistema de autenticación
- **Login** (`backend/public/login.php`): Verifica credenciales contra la base de datos
- **Registro** (`backend/public/register_form.php`, `backend/public/register.php`): Permite crear nuevas cuentas de usuario
- **Logout** (`backend/public/logout.php`): Cierra la sesión del usuario
- **Protección de rutas**: `backend/public/map.php` requiere que el usuario esté logueado

### 2. Mapas personalizados por usuario
Cada usuario tiene:
- Su propio mapa 3D interactivo
- Lista personalizada de países visitados
- Capacidad de agregar/eliminar países de su lista
- Fecha de visita guardada para cada país

### 3. API REST
Se crearon los siguientes endpoints:

- **GET** `backend/public/api/get_visited_countries.php`: Obtiene los países visitados del usuario logueado
- **GET** `backend/public/api/get_countries.php`: Obtiene todos los países disponibles
- **POST** `backend/public/api/add_visited_country.php`: Añade un país a la lista de visitados
- **POST** `backend/public/api/remove_visited_country.php`: Elimina un país de la lista de visitados

### 4. Interfaz de usuario
- Visualización del nombre de usuario en el header
- Botón de cerrar sesión
- Lista de países visitados en el sidebar
- Panel de información del país seleccionado
- Botones para marcar/desmarcar países como visitados

## Estructura de archivos

```
backend/
├── config/
│   └── monar_database.php         # Configuración de base de datos
├── public/
│   ├── index.php                  # Página de login
│   ├── login.php                  # Procesamiento del login
│   ├── register_form.php          # Formulario de registro
│   ├── register.php               # Procesamiento del registro
│   ├── logout.php                 # Cerrar sesión
│   ├── map.php                    # Mapa interactivo (protegido)
│   ├── api/
│   │   ├── get_visited_countries.php
│   │   ├── get_countries.php
│   │   ├── add_visited_country.php
│   │   ├── remove_visited_country.php
│   │   ├── search_users.php
│   │   └── get_user_profile.php
│   └── assets/
│       ├── css/
│       │   ├── styles.css
│       │   └── map.css
│       └── js/
│           ├── app.js
│           ├── map.js
│           └── data.js
├── scripts/
│   ├── maintenance/               # Scripts de mantenimiento y carga de datos
│   └── manual-tests/              # Pruebas manuales auxiliares
└── public/symfony_front_controller.php  # Front controller Symfony original preservado

database/
└── init_test_data.sql             # Datos de prueba
```

## Configuración de la base de datos

### 1. Credenciales
Las credenciales están en `backend/config/monar_database.php`:
- Host: 127.0.0.1
- Base de datos: monuar_db
- Usuario: root
- Contraseña: (vacía)

### 2. Tablas utilizadas
- `usuario`: Información de usuarios
- `pais`: Catálogo de países
- `visita_pais`: Relación entre usuarios y países visitados

### 3. Inicializar datos de prueba
Ejecuta el script SQL en [database/init_test_data.sql](../database/init_test_data.sql):

```sql
-- Crea usuarios de prueba:
-- Email: admin@demo.com
-- Contraseña: 1234

-- Email: user1@example.com
-- Contraseña: 1234
```

## Cómo usar el sistema

### Paso 1: Preparar la base de datos
1. Asegúrate de que MySQL/MariaDB esté ejecutándose
2. Ejecuta las migraciones de Symfony (backend):
   ```bash
   cd backend
   php bin/console doctrine:migrations:migrate
   ```
3. (Opcional) Carga datos de prueba:
   ```bash
   mysql -u root monuar_db < database/init_test_data.sql
   ```

### Paso 2: Iniciar el servidor PHP
```bash
cd backend/public
php -S localhost:8080
```

### Paso 3: Acceder a la aplicación
1. Abre tu navegador en `http://localhost:8080`
2. Opciones:
   - **Login con usuario de prueba**: 
     - Email: `admin@demo.com`
     - Contraseña: `1234`
   - **Crear nueva cuenta**: Click en "Crear cuenta"

### Paso 4: Usar el mapa
1. Después de iniciar sesión, serás redirigido a `map.php`
2. Haz clic en cualquier país del mapa para ver su información
3. Usa el botón "Marcar como visitado" para añadir países
4. Los países visitados aparecerán:
   - Resaltados en color morado en el mapa
   - Listados en el sidebar izquierdo
5. Click en "Eliminar de visitados" para quitar países

## Seguridad implementada

1. **Contraseñas hasheadas**: Se usa `password_hash()` con bcrypt
2. **Sesiones PHP**: Control de acceso basado en sesiones
3. **Prepared statements**: Todas las consultas SQL usan prepared statements
4. **Validación de entrada**: Validación en servidor y cliente
5. **Protección CSRF**: Headers adecuados en las APIs

## Notas importantes

### Códigos ISO de países
Los países en la base de datos deben tener códigos ISO numéricos que coincidan con el dataset de world-atlas usado en el mapa. Ejemplos:
- España: 724
- Francia: 250
- Japón: 392
- Estados Unidos: 840

### Sesiones
- Las sesiones se mantienen mientras el navegador esté abierto
- Click en "Cerrar sesión" para terminar la sesión
- Si intentas acceder a `map.php` sin estar logueado, serás redirigido al login

## Próximas mejoras sugeridas

1. Añadir recuperación de contraseña
2. Perfil de usuario editable
3. Subir foto de perfil
4. Compartir mapas entre usuarios
5. Estadísticas de viajes (países visitados, continentes, etc.)
6. Búsqueda de países en el mapa
7. Filtrado por continente
8. Añadir reseñas y fotos a los países visitados
9. Sistema de amigos/seguidores
10. Mapa de calor por frecuencia de visitas
