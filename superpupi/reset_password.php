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

// Procesar el restablecimiento de la contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Aquí puedes agregar la lógica para verificar el token si lo has almacenado
    // y actualizar la contraseña en la base de datos.
    // Por simplicidad, asumo que solo actualizas la contraseña directamente.

    // Actualizar la contraseña en la base de datos
    // Si decides verificar el token, asegúrate de que corresponde al usuario correcto.
    $sql = "UPDATE usuarios SET password = :password WHERE reset_token = :token";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['password' => $new_password, 'token' => $token]);

    echo "La contraseña ha sido actualizada exitosamente.";
}

// Generar un token único para restablecer la contraseña (esto se puede usar en un enlace)
$token = bin2hex(random_bytes(16));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - SuperPupi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="container">
        <h1>Restablecer Contraseña</h1>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <label for="new_password">Nueva Contraseña:</label>
            <input type="password" name="new_password" required>

            <button type="submit">Actualizar Contraseña</button>
        </form>
        <a href="index.php" class="button">Regresar</a>
    </div>
</body>
</html>
