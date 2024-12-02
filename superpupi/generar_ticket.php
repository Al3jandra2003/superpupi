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
$sql = "SELECT c.cantidad, p.id AS producto_id, p.nombre, p.precio, p.stock, p.imagen FROM carrito c 
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

// Mostrar el contenido del carrito
echo "<h2>Tu Carrito de Compras</h2>";
echo "<table border='1'>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Imagen</th>
        </tr>";

$total = 0;
foreach ($carrito as $item) {
    $nombre = $item['nombre'];
    $precio = $item['precio'];
    $cantidad = $item['cantidad'];
    $subtotal = $precio * $cantidad;

    echo "<tr>
            <td>$nombre</td>
            <td>$precio</td>
            <td>$cantidad</td>
            <td>$subtotal</td>
            <td>";
    if (!empty($item['imagen'])) {
        // Mostrar la imagen del producto
        $imagen_data = $item['imagen'];
        $imagen_path = 'data:image/jpeg;base64,' . base64_encode($imagen_data); // Convertir a base64 para mostrar
        echo "<img src='$imagen_path' width='50' height='50' alt='$nombre'>";
    }
    echo "</td>
        </tr>";

    // Calcular el total
    $total += $subtotal;
}
echo "</table>";
echo "<p>Total: $total</p>";

// Si se hace clic en "Generar Ticket" (compra)
if (isset($_POST['comprar'])) {
    // Incluir FPDF para generar el ticket
    require('fpdf/fpdf.php');

    // Crear nuevo PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Configurar el encabezado del ticket
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Ticket de Compra - SuperPupi');
    $pdf->Ln();

    // Agregar la fecha y hora actual
    $fecha_hora = date("Y-m-d H:i:s"); // Formato de fecha y hora
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 10, 'Fecha y Hora: ' . $fecha_hora);
    $pdf->Ln();

    // Agregar los productos comprados al ticket
    foreach ($carrito as $item) {
        $nombre = $item['nombre'];
        $precio = $item['precio'];
        $cantidad = $item['cantidad'];
        $subtotal = $precio * $cantidad;

        // Mostrar datos del producto
        $pdf->Cell(40, 10, "Producto: $nombre");
        $pdf->Ln();
        $pdf->Cell(40, 10, "Precio: $precio x $cantidad");
        $pdf->Ln();
        $pdf->Cell(40, 10, "Subtotal: $subtotal");
        $pdf->Ln();

        // Reducir el stock del producto
        $nuevo_stock = $item['stock'] - $cantidad;
        $sql_actualizar_stock = "UPDATE productos SET stock = ? WHERE id = ?";
        $stmt_actualizar_stock = $pdo->prepare($sql_actualizar_stock);
        $stmt_actualizar_stock->execute([$nuevo_stock, $item['producto_id']]);

        // Mostrar imagen del producto en el ticket
        if (!empty($item['imagen'])) {
            // Guardar temporalmente la imagen como archivo
            $imagen_data = $item['imagen'];
            $imagen_path = 'temp_image.jpg'; // Ruta temporal para la imagen
            file_put_contents($imagen_path, $imagen_data);

            // Agregar la imagen al PDF
            $pdf->Image($imagen_path, $pdf->GetX(), $pdf->GetY(), 40, 40); // Ajusta el tamaño de la imagen
            $pdf->Ln(45); // Espacio después de la imagen
        }
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
    echo "<form method='POST' action='enviar_ticket.php'>
            <button type='submit' name='enviar_correo' class='button'>Enviar Ticket por Correo</button>
          </form>";

    // Vaciar el carrito después de la compra
    $sql_vaciar_carrito = "DELETE FROM carrito WHERE usuario_id = ?";
    $stmt_vaciar = $pdo->prepare($sql_vaciar_carrito);
    $stmt_vaciar->execute([$usuario_id]);

    // Eliminar la imagen temporal
    if (file_exists($imagen_path)) {
        unlink($imagen_path);
    }

    exit();
}

// Botón para comprar
echo "<form method='POST'>
        <button type='submit' name='comprar' class='button'>Generar Ticket</button>
      </form>";
?>
