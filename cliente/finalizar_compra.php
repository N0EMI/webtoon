<?php
session_start(); // Iniciar sesión para acceder al carrito

// Verificar si hay productos en el carrito
$productos = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Calcular el total
$total = 0; 
foreach ($productos as $producto) {
    $total += $producto['precio']; // Sumar al total
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';

    // Guardar los datos del cliente en la sesión (opcional)
    $_SESSION['cliente'] = [
        'nombre' => $nombre,
        'correo' => $correo,
        'telefono' => $telefono,
    ];

    // Aquí podrías guardar los datos en la base de datos si es necesario
    // Redirigir a la página de la factura
    header("Location: factura.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="../css/finalizar.css">
</head>
<body>
<header>
    <div class="header-nav">
        <img src="../img/logo1.png" alt="logo" class="logo">
    </div>
</header>


    <main>
   
   
    <div class="container">
        <!-- Columna izquierda: Datos del Cliente -->
        <div class="left">
            <h3>Datos del Cliente</h3>
            <form action="finalizar_compra.php" method="post">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" required>
                <label for="correo">Correo:</label>
                <input type="email" name="correo" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" required>
                <input type="submit" value="Finalizar Compra">
            </form>
        </div>
        <!-- Columna derecha: Productos -->
        <div class="right">
            <h3>Productos en tu Carrito</h3>
            <table>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                </tr>
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo $producto['nombre']; ?></td>
                            <td>S/ <?php echo number_format($producto['precio'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>S/ <?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No hay productos en el carrito.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</main>



    <footer>
        <p>&copy; 2024 Droprax. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
