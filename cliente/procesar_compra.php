<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idcliente'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

$idcliente = $_SESSION['idcliente'];
$direccion_envio = $_POST['direccion_envio'] ?? '';
$metodo_pago = $_POST['metodo_pago'] ?? '';

if (empty($direccion_envio) || empty($metodo_pago)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Obtener productos del carrito
$sql_carrito = "SELECT c.idproducto, c.cantidad, p.precio, (c.cantidad * p.precio) AS total_producto, p.stock
                FROM carrito c
                JOIN producto p ON c.idproducto = p.idproducto
                WHERE c.idcliente = ? AND c.estado = 'activo'";
$stmt_carrito = $conexion->prepare($sql_carrito);
$stmt_carrito->bind_param("i", $idcliente);
$stmt_carrito->execute();
$result_carrito = $stmt_carrito->get_result();

$productos_carrito = [];
$total = 0;

while ($row = $result_carrito->fetch_assoc()) {
    if ($row['cantidad'] > $row['stock']) {
        echo json_encode(['success' => false, 'error' => 'Stock insuficiente para el producto: ' . $row['idproducto']]);
        exit;
    }
    $productos_carrito[] = $row;
    $total += $row['total_producto'];
}

// Calcular el descuento (5% si el total es mayor a 100)
$descuento = $total > 100 ? $total * 0.05 : 0;
$total_final = $total - $descuento;

// Insertar el pedido
$sql_pedido = "INSERT INTO pedido (idcliente, fecha, direccion_envio, metodo_pago, estado_pedido, total_pago, descuento, idadmi) 
               VALUES (?, NOW(), ?, ?, 'Pendiente', ?, ?, 1)";
$stmt_pedido = $conexion->prepare($sql_pedido);
$stmt_pedido->bind_param("issdd", $idcliente, $direccion_envio, $metodo_pago, $total_final, $descuento);

if (!$stmt_pedido->execute()) {
    echo json_encode(['success' => false, 'error' => 'Error al registrar el pedido']);
    exit;
}

// Obtener el ID del pedido recién insertado
$idpedido = $stmt_pedido->insert_id;

// Insertar los productos del pedido
foreach ($productos_carrito as $producto) {
    $subtotal = $producto['cantidad'] * $producto['precio'];
    $sql_detalle = "INSERT INTO pedido_has_producto (idpedido, idproducto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conexion->prepare($sql_detalle);
    $stmt_detalle->bind_param("iiid", $idpedido, $producto['idproducto'], $producto['cantidad'], $subtotal);

    if (!$stmt_detalle->execute()) {
       echo json_encode(['success' => false, 'error' => 'Error al registrar productos del pedido']);
       //echo json_encode(['success' => true, 'factura' => "../cliente/generar_factura.php?idpedido=$idpedido"]);

        exit;

    }

    // Reducir el stock del producto
    $nuevo_stock = $producto['stock'] - $producto['cantidad'];
    $sql_stock = "UPDATE producto SET stock = ? WHERE idproducto = ?";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bind_param("ii", $nuevo_stock, $producto['idproducto']);
    $stmt_stock->execute();
}

// Marcar el carrito como procesado
$sql_actualizar_carrito = "UPDATE carrito SET estado = 'procesado' WHERE idcliente = ?";
$stmt_actualizar_carrito = $conexion->prepare($sql_actualizar_carrito);
$stmt_actualizar_carrito->bind_param("i", $idcliente);
$stmt_actualizar_carrito->execute();

//echo json_encode(['success' => true]);
// Redirigir a la factura
//echo json_encode(['success' => true, 'redirect_url' => "generar_factura.php?idpedido=$idpedido"]);
echo json_encode([
    'success' => true,
    'redirect_url' => "../cliente/generar_factura.php?idpedido=$idpedido",
    'idpedido' => $idpedido // Agrega este parámetro para depuración
]);

?>
