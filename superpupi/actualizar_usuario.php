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

// Inicializar variables
$id = $nombre = $email = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Obtener el usuario por ID
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch();

    // Asignar valores a las variables
    if ($usuario) {
        $nombre = $usuario['nombre'];
        $email = $usuario['email'];
    }
}

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'id' => $id]);

    header("Location: usuarios.php"); // Redirigir a la página de gestión de usuarios
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Usuario - SuperPupi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="container">
        <h1>Actualizar Usuario</h1>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            
            <button type="submit" class="button">Actualizar</button>
            <a href="usuarios.php" class="button">Cancelar</a> <!-- Botón de cancelar -->
        </form>
    </div>
</body>
</html>
