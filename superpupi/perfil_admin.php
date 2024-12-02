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

// Eliminar producto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM productos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
}

// Obtener todos los productos
$sql = "SELECT * FROM productos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Admin - SuperPupi</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function confirmDelete() {
            return confirm("¿Estás seguro de que quieres eliminar este producto?");
        }

        function openUpdateModal(id, name, price, stock) {
            document.getElementById("update-id").value = id;
            document.getElementById("update-name").value = name;
            document.getElementById("update-price").value = price;
            document.getElementById("update-stock").value = stock;
            document.getElementById("update-modal").style.display = "block";
        }

        function closeUpdateModal() {
            document.getElementById("update-modal").style.display = "none";
        }
    </script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .button {
            margin: 5px 0;
            display: block;
        }
    </style>
</head>
<body>
    <div class="stars"></div>
    <div class="container">
        <h1>Perfil del Administrador</h1> 
        <div class="admin-options">
    <a href="reportes.html" class="button">Reportes</a>
    
</div>

        
        <!-- Botón para ver usuarios -->
        <div class="admin-options">
            <a href="usuarios.php" class="button">Ver Usuarios</a>
            <!-- Botón para generar gráfica -->
            <button onclick="location.href='generar_grafica.php'" class="button">Generar Gráfica de Artículo y Stock</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <th>Actualizar</th>
                    <th>Eliminar</th>
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
                        <button onclick="openUpdateModal('<?php echo $producto['id']; ?>', '<?php echo $producto['nombre']; ?>', '<?php echo $producto['precio']; ?>', '<?php echo $producto['stock']; ?>')" class="button">Actualizar</button>
                    </td>
                    <td>
                        <a href="perfil_admin.php?delete=<?php echo $producto['id']; ?>" class="button" onclick="return confirmDelete();">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="index.php" class="button">Regresar</a>
    </div>

    <!-- Modal para actualizar producto -->
    <div id="update-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h2>Actualizar Producto</h2>
            <form action="actualizar_producto.php" method="POST">
                <input type="hidden" id="update-id" name="id">
                <label for="update-name">Nombre:</label>
                <input type="text" id="update-name" name="nombre" required>
                <label for="update-price">Precio:</label>
                <input type="text" id="update-price" name="precio" required>
                <label for="update-stock">Stock:</label>
                <input type="text" id="update-stock" name="stock" required>
                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>
    
    <script>
        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById("update-modal");
            if (event.target == modal) {
                closeUpdateModal();
            }
        }
    </script>
</body>
</html>

