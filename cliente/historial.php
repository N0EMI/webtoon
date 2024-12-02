<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idcliente'])) {
    echo "<p>Por favor, inicia sesión para ver tu historial de pedidos.</p>";
    exit;
}

$idcliente = $_SESSION['idcliente'];

// Obtener los pedidos del cliente
$sql_pedidos = "SELECT p.idpedido, p.fecha, p.total_pago, p.descuento, p.estado_pedido
                FROM pedido p
                WHERE p.idcliente = ?
                ORDER BY p.fecha DESC";
$stmt_pedidos = $conexion->prepare($sql_pedidos);
$stmt_pedidos->bind_param("i", $idcliente);
$stmt_pedidos->execute();
$result_pedidos = $stmt_pedidos->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos</title>
    <link rel="stylesheet" href="../css/principal.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h2>Historial de Pedidos</h2>

    <?php if ($result_pedidos->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Descuento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pedido = $result_pedidos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $pedido['idpedido']; ?></td>
                        <td><?php echo $pedido['fecha']; ?></td>
                        <td>S/ <?php echo number_format($pedido['total_pago'], 2); ?></td>
                        <td>S/ <?php echo number_format($pedido['descuento'], 2); ?></td>
                        <td><?php echo $pedido['estado_pedido']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes pedidos registrados.</p>
    <?php endif; ?>
</body>
</html>
<form action="../cliente/index_cliente.php" method="get">
    <button type="submit" class="styled-button">Volver</button>
</form>
