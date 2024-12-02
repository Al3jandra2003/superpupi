<?php
session_start(); // Iniciar sesión

// Conectar a la base de datos
$host = 'localhost';
$dbname = 'superpupi'; // Cambia por el nombre de tu base de datos
$username = 'root';
$password = ''; // Si tu base de datos tiene contraseña, colócala aquí

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Procesar el inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Guardar información del usuario en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // Guardar el rol del usuario

        // Redirigir según el rol
        if ($user['role'] === 'admin') {
            header("Location: perfil_admin.php"); // Redirigir al perfil de admin
            exit;
        } else {
            header("Location: perfil_usuario.php"); // Redirigir al perfil de usuario
            exit;
        }
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperPupi - Inicio de sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="container">
        <h1>Inicio de sesión</h1>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Iniciar sesión</button>

            <?php if (isset($error) && $error): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <a href="index.php" class="button">Regresar</a> <!-- Botón de regresar -->
        <a href="forgot_password.php" class="button">Olvidaste tu contraseña?</a> <!-- Enlace para recuperar contraseña -->
    </div>
</body>
</html>



