<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# CADMUS TECH - Gym Management API 🏋️‍♂️

Este es el backend oficial para la aplicación de entrenamiento **AppEntrenamiento**, desarrollado como parte del proyecto de **CADMUS TECH**. Provee una API REST sólida para la gestión de alumnos, entrenadores, rutinas de entrenamiento y planes nutricionales.

## 🛠️ Tecnologías Utilizadas

* **Lenguaje:** PHP 8.2+
* **Framework:** Laravel 11.x
* **Base de Datos:** MySQL / MariaDB
* **Autenticación:** Laravel Sanctum (Token-based)
* **Gestión de Archivos:** Laravel Storage (Local disk)

## 📋 Requisitos Previos

Asegúrate de tener instalado lo siguiente en tu máquina local:

1. **PHP 8.2 o superior** con las siguientes extensiones:
   - `mbstring`
   - `xml`
   - `bcmath`
   - `curl`
   - `sodium` ⚠️ **Requerida para Firebase** (ver instrucciones abajo)

2. **Composer** (Gestor de dependencias de PHP)
   - Descarga desde: [getcomposer.org](https://getcomposer.org/)

3. **MySQL / MariaDB** (Servidor de base de datos)
   - XAMPP, WAMP, MAMP o instalación standalone

4. **Node.js & NPM** (Opcional, para compilado de assets)

## 🚀 Instalación y Configuración

### 1. Clonar el repositorio
```bash
git clone https://github.com/Tade17/gym-backend-api.git
cd gym-backend-api
```

### 2. Instalar Composer (si no lo tienes)

**Windows:**
- Descarga el instalador: [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)
- Ejecuta el instalador y sigue las instrucciones
- El instalador detectará automáticamente tu instalación de PHP

**macOS/Linux:**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Habilitar extensión PHP Sodium

La extensión `sodium` es **requerida** para Firebase y autenticación JWT.

**Si usas XAMPP:**
1. Abre `C:\xampp\php\php.ini`
2. Busca la línea `;extension=sodium`
3. Quita el `;` para descomentarla: `extension=sodium`
4. Guarda el archivo

**Verificar que esté habilitada:**
```bash
php -m | grep sodium
```

### 4. Instalar dependencias
```bash
composer install
```

Si obtienes errores relacionados con `ext-sodium`, asegúrate de haber completado el paso 3.

### 5. Configurar el entorno

Copia el archivo de ejemplo:
```bash
# Windows
copy .env.example .env

# macOS/Linux
cp .env.example .env
```

Edita el archivo `.env` con tus credenciales:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_app_entrenamiento
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Configurar Firebase Credentials

Este proyecto usa Firebase para notificaciones push.

1. Ve a [Firebase Console](https://console.firebase.google.com/)
2. Selecciona tu proyecto
3. Ve a **⚙️ Configuración del proyecto** → **Cuentas de servicio**
4. Click en **"Generar nueva clave privada"**
5. Descarga el archivo JSON
6. **Renómbralo** a `firebase-credentials.json`
7. **Guárdalo** en la carpeta `storage/` del proyecto:
   ```
   gym-backend-api/
   └── storage/
       └── firebase-credentials.json
   ```

⚠️ **Importante**: Este archivo contiene credenciales sensibles. **NO lo subas a Git**.

### 7. Generar la App Key
```bash
php artisan key:generate
```

Este comando genera una clave de encriptación única para tu aplicación.

### 8. Ejecutar Migraciones
```bash
php artisan migrate
```

Esto creará todas las tablas necesarias en la base de datos.

### 9. Enlace de Storage (Opcional)
```bash
php artisan storage:link
```

### 10. Iniciar el Servidor
```bash
php artisan serve
```

El backend estará disponible en: **http://127.0.0.1:8000** 🚀



## 🧪 Verificar la Instalación

Ver todas las rutas disponibles:
```bash
php artisan route:list
```

Deberías ver aproximadamente 72 rutas para gestión de:
- Autenticación de usuarios
- Dashboard del entrenador
- Ejercicios y rutinas
- Planes de dieta
- Logs de entrenamiento
- Y más...

## ⚠️ Solución de Problemas

### Error: "composer: command not found"
**Solución:** Composer no está instalado o no está en el PATH. Sigue el paso 2 de instalación.

### Error: "ext-sodium is missing"
**Solución:** La extensión sodium no está habilitada. Sigue el paso 3 para habilitarla.

### Error: "Access denied for user"
**Solución:** Verifica tus credenciales de MySQL en el archivo `.env`. Asegúrate de que el usuario tenga permisos.

### Error al conectar con Firebase
**Solución:** Verifica que el archivo `storage/firebase-credentials.json` exista y tenga el formato correcto de cuenta de servicio.

## 🧹 Tareas de Mantenimiento

### Limpiar fotos antiguas de comidas
Para no saturar el servidor con fotos subidas:
```bash
php artisan app:clean-old-meal-photos
```

### Limpiar caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```