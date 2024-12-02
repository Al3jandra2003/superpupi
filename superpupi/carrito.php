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

// Obtener el carrito del usuario
$usuario_id = 1; // Cambia esto al ID del usuario que está logueado
$sql = "SELECT c.id AS carrito_id, c.cantidad, p.id AS producto_id, p.nombre, p.precio, p.stock, p.imagen FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$carrito = $stmt->fetchAll();

// Si el carrito está vacío, mostrar un mensaje
if (empty($carrito)) {
    echo "<p>No tienes productos en el carrito.</p>";
    exit();
}

// Si se hace clic en "Eliminar" un producto del carrito
if (isset($_POST['eliminar'])) {
    $carrito_id = $_POST['carrito_id'];
    
    // Eliminar el producto del carrito
    $sql_eliminar = "DELETE FROM carrito WHERE id = ?";
    $stmt_eliminar = $pdo->prepare($sql_eliminar);
    $stmt_eliminar->execute([$carrito_id]);

    // Redirigir al carrito para reflejar los cambios
    header("Location: carrito.php");
    exit();
}

// Si se hace clic en "Generar Ticket" (compra)
if (isset($_POST['comprar'])) {
    // Incluir FPDF para generar el ticket
    require('fpdf/fpdf.php');
    
    $pdf = new FPDF();
    $pdf->AddPage();

    // Configurar el encabezado del ticket
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Ticket de Compra - SuperPupi');
    $pdf->Ln();
    
    // Agregar la fecha y hora de generación del ticket
    $fecha_hora = date('d/m/Y H:i:s'); // Formato de fecha y hora
    $pdf->Cell(40, 10, "Fecha: $fecha_hora");
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);

    // Agregar los productos comprados al ticket
    $total = 0;
    foreach ($carrito as $item) {
        $nombre = $item['nombre'];
        $precio = $item['precio'];
        $cantidad = $item['cantidad'];
        $subtotal = $precio * $cantidad;

        $pdf->Cell(40, 10, "Producto: $nombre");
        $pdf->Ln();
        $pdf->Cell(40, 10, "Precio: $precio x $cantidad");
        $pdf->Ln();
        $pdf->Cell(40, 10, "Subtotal: $subtotal");
        $pdf->Ln();

        // Mostrar imagen del producto en el ticket
        if (!empty($item['imagen'])) {
            // Guardar temporalmente la imagen como archivo
            $imagen_data = $item['imagen'];
            $imagen_path = 'temp_image.jpg'; // Ruta temporal para la imagen
            file_put_contents($imagen_path, $imagen_data);

            // Agregar la imagen al PDF
            $pdf->Image($imagen_path, $pdf->GetX(), $pdf->GetY(), 30, 30); // Ajusta el tamaño de la imagen
            $pdf->Ln(35); // Espacio después de la imagen
        }

        $total += $subtotal;

        // Reducir el stock del producto
        $nuevo_stock = $item['stock'] - $cantidad;
        $sql_actualizar_stock = "UPDATE productos SET stock = ? WHERE id = ?";
        $stmt_actualizar_stock = $pdo->prepare($sql_actualizar_stock);
        $stmt_actualizar_stock->execute([$nuevo_stock, $item['producto_id']]);
    }

    // Total final
    $pdf->Cell(40, 10, "Total: $total");
    $pdf->Ln();

    // Guardar el PDF
    $pdf_output = 'ticket_compra.pdf';
    $pdf->Output('F', $pdf_output);

    // Mostrar opciones de descarga o envío por correo
    echo "<p>Compra realizada con éxito. El ticket ha sido generado.</p>";
    echo "<a href='$pdf_output' target='_blank'>Descargar Ticket en PDF</a>";

    // Formulario para enviar el ticket por correo
    echo "<form method='POST' action='enviar_ticket.php'>
            <input type='hidden' name='ticket_pdf' value='$pdf_output'>
            <input type='email' name='correo' placeholder='Tu correo electrónico' required>
            <button type='submit' name='enviar_correo' class='button'>Enviar Ticket por Correo</button>
          </form>";

    // Vaciar el carrito después de la compra
    $sql_vaciar_carrito = "DELETE FROM carrito WHERE usuario_id = ?";
    $stmt_vaciar = $pdo->prepare($sql_vaciar_carrito);
    $stmt_vaciar->execute([$usuario_id]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - SuperPupi</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este producto del carrito?");
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Carrito de Compras</h1>
        <table>
            <thead>
                <tr>
                    <th>Imagen</th> <!-- Nueva columna para la imagen -->
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $item): ?>
                <tr>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($item['imagen']); ?>" width="50" height="50"></td> <!-- Mostrar imagen -->
                    <td><?php echo $item['nombre']; ?></td>
                    <td><?php echo $item['precio']; ?></td>
                    <td><?php echo $item['cantidad']; ?></td>
                    <td>
                        <form method="POST" action="" onsubmit="return confirmarEliminacion();">
                            <input type="hidden" name="carrito_id" value="<?php echo $item['carrito_id']; ?>">
                            <button type="submit" name="eliminar" class="button">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="POST" action="">
            <button type="submit" name="comprar" class="button">Generar Ticket (Comprar)</button>
        </form>
        <a href="index.php" class="button">Regresar</a>
    </div>
</body>
</html>

