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

// Obtener datos de productos
$sql = "SELECT nombre, stock FROM productos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para la gráfica
$nombres = [];
$stocks = [];

foreach ($productos as $producto) {
    $nombres[] = $producto['nombre'];
    $stocks[] = $producto['stock'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica de Artículo y Stock</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f0e68c; /* Fondo amarillo clarito */
            color: black; /* Texto negro */
            font-family: Arial, sans-serif; /* Tipo de letra */
            text-align: center; /* Centramos el contenido */
        }
        canvas {
            max-width: 600px; /* Tamaño de la gráfica */
            margin: 0 auto; /* Centrar la gráfica */
        }
    </style>
</head>
<body>
    <h1>Gráfica de Artículo y Stock</h1>
    <canvas id="myChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar', // Cambia a 'line' si prefieres una gráfica de líneas
            data: {
                labels: <?php echo json_encode($nombres); ?>,
                datasets: [{
                    label: 'Stock',
                    data: <?php echo json_encode($stocks); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)', // Color de relleno de la gráfica
                    borderColor: 'rgba(0, 0, 0, 1)', // Color del borde negro
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre de Producto', // Título del eje Y
                            color: 'black', // Color del título
                        },
                        ticks: {
                            color: 'black' // Color de las etiquetas del eje Y
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Productos', // Título del eje X (puedes cambiarlo si lo deseas)
                            color: 'black', // Color del título
                        },
                        ticks: {
                            color: 'black' // Color de las etiquetas del eje X
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'black' // Color de la leyenda
                        }
                    }
                }
            }
        });
    </script>
    <a href="perfil_admin.php">Regresar</a>
</body>
</html>
