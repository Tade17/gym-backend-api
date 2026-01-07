<!DOCTYPE html>
<html>
<head>
    <style>
        .header { background-color: #C2185B; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; border: 1px solid #eee; border-radius: 0 0 10px 10px; font-family: sans-serif; }
        .credentials { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .button { background-color: #C2185B; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div style="max-width: 600px; margin: auto;">
        <div class="header">
            <h1>¡Bienvenido a la Familia, {{ $user->first_name }}!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            <p>Tu cuenta ha sido creada con éxito por tu entrenador. Ya puedes acceder a tu panel para ver tu ficha técnica y plan de entrenamiento.</p>
            
            <div class="credentials">
                <p><strong>Tus credenciales de acceso:</strong></p>
                <p>📧 Correo: <code>{{ $user->email }}</code></p>
                <p>🔑 Contraseña: <code>{{ $password }}</code></p>
            </div>

            <p>Te recomendamos cambiar tu contraseña una vez que ingreses por primera vez.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ config('app.url') }}" class="button">Ir al Panel de Alumno</a>
            </div>
        </div>
    </div>
</body>
</html>