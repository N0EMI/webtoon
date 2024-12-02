<?php
session_start();
include '../conexion.php'; 

if (isset($_POST['idproducto']) && isset($_POST['cantidad'])) {
    // Verificar si el cliente está registrado
    if (isset($_SESSION['idcliente'])) {
        $idcliente = $_SESSION['idcliente'];
        $idproducto = $_POST['idproducto'];
        $cantidad = $_POST['cantidad'];

        // Verificar si el producto ya está en el carrito
        $sql_verificar = "SELECT * FROM carrito WHERE idcliente = ? AND idproducto = ? AND estado = 'activo'";
        $stmt = $conexion->prepare($sql_verificar);
        $stmt->bind_param("ii", $idcliente, $idproducto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Si el producto ya está en el carrito, actualizamos la cantidad
            $row = $resultado->fetch_assoc();
            $nuevaCantidad = $row['cantidad'] + $cantidad;
            $sql_actualizar = "UPDATE carrito SET cantidad = ? WHERE idcarrito = ?";
            $stmt = $conexion->prepare($sql_actualizar);
            $stmt->bind_param("ii", $nuevaCantidad, $row['idcarrito']);
            $stmt->execute();
        } else {
            // Si no está en el carrito, lo añadimos
            $sql_insertar = "INSERT INTO carrito (idcliente, idproducto, cantidad, estado) VALUES (?, ?, ?, 'activo')";
            $stmt = $conexion->prepare($sql_insertar);
            $stmt->bind_param("iii", $idcliente, $idproducto, $cantidad);
            $stmt->execute();
        }

    } else {
        // Para clientes no registrados, utilizamos una sesión temporal
        $idproducto = $_POST['idproducto'];
        $cantidad = $_POST['cantidad'];

        // Si no existe un carrito en la sesión, lo creamos
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Verificar si el producto ya está en el carrito (para clientes no registrados)
        $producto_en_carrito = false;
        foreach ($_SESSION['carrito'] as &$producto) {
            if ($producto['idproducto'] == $idproducto) {
                $producto['cantidad'] += $cantidad; // Si ya existe, sumamos la cantidad
                $producto_en_carrito = true;
                break;
            }
        }

        if (!$producto_en_carrito) {
            // Si el producto no está en el carrito, lo añadimos
            $_SESSION['carrito'][] = ['idproducto' => $idproducto, 'cantidad' => $cantidad];
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
}
?>

