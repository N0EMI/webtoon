<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php';

// Verificar si hay productos en el carrito
if (isset($_POST['cart'])) {
    // Obtener los productos del carrito desde el formulario o la petición
    $cart = json_decode($_POST['cart'], true); // Decodificar el carrito (suponiendo que es un JSON)

    if (!empty($cart)) {
        // Procesar cada producto en el carrito
        foreach ($cart as $item) {
            // Verificar que los valores están definidos
            if (isset($item['id']) && isset($item['name']) && isset($item['price']) && isset($item['quantity'])) {
                $productId = $item['id'];
                $productName = $item['name'];
                $productPrice = $item['price'];
                $productQuantity = $item['quantity'];

                // Realizar la consulta de inserción o actualización en la base de datos
                // Ejemplo: agregar una entrada en la tabla de pedidos o de carrito en la base de datos
                $sql = "INSERT INTO carrito (idproducto, nombre, precio, cantidad) VALUES (?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("isdi", $productId, $productName, $productPrice, $productQuantity);
                    if ($stmt->execute()) {
                        echo "Producto añadido al carrito correctamente.";
                    } else {
                        echo "Error al agregar el producto al carrito: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Error en la preparación de la consulta: " . $conexion->error;
                }
            } else {
                // Si alguna clave no está definida, mostrar error
                echo "Error: Producto con datos incompletos.";
            }
        }
    } else {
        echo "El carrito está vacío.";
    }
} else {
    echo "No se ha enviado el carrito.";
}

// Cerrar la conexión
$conexion->close();
?>
