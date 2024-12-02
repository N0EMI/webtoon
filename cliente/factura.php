<?php
session_start(); // Iniciar sesión para acceder a los datos del cliente y el carrito

// Verificar si hay productos en el carrito
$productos = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total = 0;

if (isset($_SESSION['cliente'])) {
    $cliente = $_SESSION['cliente'];
} else {
    // Redirigir si no hay datos de cliente
    header("Location: finalizar_compra.php");
    exit;
}

// Calcular el total
foreach ($productos as $producto) {
    $total += $producto['precio']; // Sumar al total
}

// Eliminar el carrito al volver
if (isset($_POST['volver'])) {
    unset($_SESSION['carrito']); // Elimina el carrito de la sesión
    header("Location: index_principal.php"); // Redirige a la página principal
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <link rel="stylesheet" href="../css/factura.css">

</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Droprax</h1>
            <p>Dirección de la Empresa</p>
            <p>Teléfono: (123) 456-7890</p>
            <p>Correo: contacto@droprax.com</p>
        </header>

        <main>
            <h2>Factura</h2>
            <h3>Datos del Cliente</h3>
            <p>Nombre: <?php echo $cliente['nombre']; ?></p>
            <p>Correo: <?php echo $cliente['correo']; ?></p>
            <p>Teléfono: <?php echo $cliente['telefono']; ?></p>

            <h3>Productos Comprados</h3>
            <div class="table-container">
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

            <p>Gracias por su compra!</p>
        </main>
        
        <form action="" method="post"> <!-- Cambia la acción a POST -->
            <button type="submit" name="volver">Volver</button> <!-- Añadir el nombre al botón -->
        </form>
        
        <footer>
            <p>&copy; 2024 Droprax. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
