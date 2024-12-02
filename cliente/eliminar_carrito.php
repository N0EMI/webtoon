<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idcliente'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

$idcliente = $_SESSION['idcliente'];

// Eliminar productos del carrito
$sql_eliminar = "DELETE FROM carrito WHERE idcliente = ?";
$stmt = $conexion->prepare($sql_eliminar);
$stmt->bind_param("i", $idcliente);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar el carrito']);
}
?>
