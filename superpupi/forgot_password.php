<?php
// Conectar a la base de datos
$host = 'localhost';
$dbname = 'superpupi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Procesar la solicitud de recuperación de contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generar un token único
        $token = bin2hex(random_bytes(16));
        
        // Guardar el token en la base de datos (opcional, si decides hacer esto)
        // $sql = "UPDATE usuarios SET reset_token = :token WHERE email = :email";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute(['token' => $token, 'email' => $email]);

        // Redirigir al usuario a la página de restablecimiento de contraseña
        header("Location: reset_password.php?token=$token");
        exit;
    } else {
        echo "No se encontró ningún usuario con ese correo electrónico.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - SuperPupi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="container">
        <h1>Recuperar Contraseña</h1>
        <form action="forgot_password.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <button type="submit">Enviar enlace</button>
        </form>
        <a href="index.php" class="button">Regresar</a>
    </div>
</body>
</html>
