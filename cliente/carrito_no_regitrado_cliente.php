<?php
session_start(); // Iniciar la sesión

// Verificar si se ha enviado un idProducto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $idProducto = intval($data['idproducto']);
    $nombre = $data['nombre'];
    $precio = floatval($data['precio']);

    // Agregar el producto al carrito en la sesión
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Asegurarse de que no se agregue el mismo producto varias veces
    $productoYaEnCarrito = false;
    foreach ($_SESSION['carrito'] as $item) {
        if ($item['id'] == $idProducto) {
            $productoYaEnCarrito = true;
            break;
        }
    }

    if (!$productoYaEnCarrito) {
        $_SESSION['carrito'][] = [
            'id' => $idProducto,
            'nombre' => $nombre,
            'precio' => $precio,
        ];
        echo json_encode(['message' => $nombre . " ha sido añadido al carrito"]);
    } else {
        echo json_encode(['message' => $nombre . " ya está en el carrito"]);
    }
} else {
    echo json_encode(['message' => 'Método no permitido']);
}