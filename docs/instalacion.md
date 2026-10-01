# Instalación y operación

Guía complementaria del [README](../README.md). Aquí están las formas de instalación sin Docker, la configuración de tareas programadas y correo, y la solución de problemas frecuentes.

## Requisitos sin Docker

- PHP 8.4 o superior.
- Composer 2.
- Node.js 20 o superior y npm.
- MySQL 8 o SQLite.
- Git.

## Instalación con XAMPP

1. Instala PHP 8.4, Composer y Node.js. XAMPP debe tener MySQL activo.
2. Copia el proyecto dentro de `htdocs`.
3. Ejecuta `composer install` y `npm install`.
4. Copia `.env.example` a `.env`.
5. Crea una base de datos, por ejemplo `clinica_veterinaria`.
6. Configura en `.env`:

```env
APP_URL=http://localhost/clinica-veterinaria/public
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinica_veterinaria
DB_USERNAME=root
DB_PASSWORD=
```

Continúa con:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

Accede a `http://localhost/clinica-veterinaria/public`. Para desarrollo, puedes usar `php artisan serve` y abrir `http://127.0.0.1:8000`.

## Instalación con Laragon

1. Copia el proyecto en `C:\laragon\www\clinica-veterinaria`.
2. Inicia Apache y MySQL desde Laragon.
3. Crea la base de datos `clinica_veterinaria`.
4. Configura `.env` con MySQL, normalmente con usuario `root` y contraseña vacía.
5. Ejecuta desde la terminal de Laragon:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
```

Laragon permite abrir el proyecto con `http://clinica-veterinaria.test` si el directorio está dentro de `www`.

## Desarrollo con Docker

Para recargar estilos en caliente mientras desarrollas:

```bash
./vendor/bin/sail npm run dev
```

## Datos de demostración sin Docker

```bash
php artisan db:seed
php artisan db:seed --class=ExampleScenarioSeeder
```

## Tareas programadas

En `routes/console.php` se programan cada minuto:

```bash
php artisan surgeries:mark-no-show
php artisan surgeries:send-reminders
```

En producción se debe mantener activo el scheduler de Laravel. En desarrollo puede ejecutarse:

```bash
php artisan schedule:work
```

Si se configura el envío de correos por cola, se procesan con:

```bash
php artisan queue:work
```

## Correo

Con Docker, el correo local usa Mailpit en `http://localhost:8025`. Para un proveedor SMTP real, configura `MAIL_MAILER=smtp` y las credenciales correspondientes en `.env`. No publiques contraseñas SMTP en el control de versiones.

## Pruebas sin Docker

```bash
php artisan test
```

## Solución rápida de problemas

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan storage:link
```

Si aparece un error de permisos en Docker, ejecuta los comandos Artisan dentro del contenedor mediante Sail y verifica que la carpeta del proyecto tenga permisos de escritura para `storage` y `bootstrap/cache`.

## Seguridad

Las credenciales de los seeders son únicamente para demostración. En una instalación real cambia las contraseñas, configura `APP_DEBUG=false`, usa HTTPS y define un correo SMTP seguro.
