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
$fecha_inicio = '';
$fecha_fin = '';
$totalVentas = 0;
$ventas = [];

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['fecha_inicio']) && !empty($_POST['fecha_fin'])) {
    // Obtener las fechas del formulario
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Consultar las ventas en el rango de fechas
    $sql = "SELECT * FROM ventas WHERE fecha >= :fecha_inicio AND fecha <= :fecha_fin";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
    $ventas = $stmt->fetchAll();

    // Calcular el total de precios
    foreach ($ventas as $venta) {
        $totalVentas += $venta['precio'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Reporte de Ventas</h1>
        
        <!-- Formulario para ingresar fechas -->
        <form method="POST" action="generar_reporte.php">
            <label for="fecha_inicio">Desde:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
            
            <label for="fecha_fin">Hasta:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
            
            <button type="submit">Generar Reporte</button>
        </form>
        
        <p>Desde: <?php echo htmlspecialchars($fecha_inicio); ?> Hasta: <?php echo htmlspecialchars($fecha_fin); ?></p>
        
        <?php if (!empty($ventas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($venta['id']); ?></td>
                        <td><?php echo htmlspecialchars($venta['producto']); ?></td>
                        <td><?php echo htmlspecialchars($venta['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($venta['precio']); ?></td>
                        <td><?php echo htmlspecialchars($venta['fecha']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total de Ventas:</strong> <?php echo htmlspecialchars($totalVentas); ?></p>
        <?php else: ?>
            <p>No se encontraron ventas en el rango de fechas seleccionado.</p>
        <?php endif; ?>
        
        <a href="perfil_admin.php" class="button">Volver</a>
    </div>
</body>
</html>
