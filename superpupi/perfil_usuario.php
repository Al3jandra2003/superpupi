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

// Obtener todos los productos
$sql = "SELECT * FROM productos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll();

// Verificar si se ha agregado un producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $producto_id = $_POST['producto_id'];
    $usuario_id = 1; // Cambia esto al ID del usuario que está logueado
    $cantidad = $_POST['cantidad']; // El usuario selecciona la cantidad

    // Verificar si hay suficiente stock antes de agregar al carrito
    $sql = "SELECT stock FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if ($producto['stock'] >= $cantidad) {
        // Insertar en la tabla carrito
        $sql = "INSERT INTO carrito (producto_id, usuario_id, cantidad) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$producto_id, $usuario_id, $cantidad]);

        // Actualizar el stock del producto
        $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cantidad, $producto_id]);

        header("Location: perfil_usuario.php?mensaje=Producto agregado al carrito");
        exit();
    } else {
        header("Location: perfil_usuario.php?mensaje=No hay suficiente stock");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Usuario - SuperPupi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="container">
        <h1>Perfil del Usuario</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?php echo $producto['id']; ?></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo $producto['precio']; ?></td>
                    <td><?php echo $producto['stock']; ?></td>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>" width="50" height="50"></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" required>
                            <button type="submit" name="agregar" class="button">Agregar al Carrito</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="button">Regresar</a>
    </div>

    <!-- Carrito -->
    <div class="carrito">
        <a href="carrito.php" class="button">Ver Carrito</a>
        <a href="figura3d.php" class="button">Ver Figura 3D</a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <p><?php echo $_GET['mensaje']; ?></p>
    <?php endif; ?>
</body>
</html>
