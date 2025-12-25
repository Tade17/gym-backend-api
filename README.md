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

1.  **PHP 8.2 o superior** (Con extensiones: `mbstring`, `xml`, `bcmath`, `curl`).
2.  **Composer** (Gestor de dependencias de PHP).
3.  **MySQL / MariaDB** (Servidor de base de datos).
4.  **Node.js & NPM** (Opcional, para el compilado de assets si fuera necesario).

## 🚀 Instalación y Configuración

Sigue estos pasos para poner a correr el proyecto:

### 1. Clonar el repositorio
```bash
git clone [https://github.com/Tade17/gym-backend-api.git](https://github.com/Tade17/gym-backend-api.git)
cd gym-backend-api
```
###  2. Instalar dependencias 
```bash 
composer install
 ```

###  3. Configurar el entorno 
Copia el archivo de ejemplo y configura tus credenciales de base de datos

```bash 
cp .env.example .env 
```

Luego, abre el archivo .env y edita estas líneas con tus datos locales:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_app_db_v2
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

### 4 . Generar la App Key y Enlace de Storage 
```bash 
php artisan key:generate
php artisan storage:link
```
### 5. Ejecutar Migraciones 
```bash 
php artisan migrate 
```

###   6. Iniciar Servidor  
```bash 
php artisan serve
```


El backend estará disponible en: http://localhost:8000



###  🧹 Tareas Programadas
 Para no saturar el servidor con fotos subidas 
```bash 
php artisan app:clean-old-meal-photos
```