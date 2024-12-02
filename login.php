<?php
include 'conexion.php'; 

session_start();

// Obtener datos del formulario
$correo_electronico = $_POST['correo_electronico'];
$contrasena = $_POST['contrasena'];

/*Verificar si es un administrador
$consulta_admin = "SELECT * FROM Administrador WHERE correoAdmin=? AND contraseñaAdmin=?";
$stmt_admin = $conexion->prepare($consulta_admin);
$stmt_admin->bind_param("ss", $correo_electronico, $contrasena);
$stmt_admin->execute();
$resultado_admin = $stmt_admin->get_result();

if ($resultado_admin && $resultado_admin->num_rows > 0) {
    // Es un administrador
    $_SESSION['Correo_electronico'] = $correo_electronico; // Guardar el correo en la sesión
    header("Location: ADMI.php");
    exit();
}*/


// Verificar si es un cliente 
$consulta_cliente = "SELECT * FROM cliente WHERE correocliente=? AND contrasenacliente=?";
$stmt_cliente = $conexion->prepare($consulta_cliente);
$stmt_cliente->bind_param("ss", $correo_electronico, $contrasena);
$stmt_cliente->execute();
$resultado_cliente = $stmt_cliente->get_result();

if ($resultado_cliente && $resultado_cliente->num_rows > 0) {
    // Es un cliente
    $cliente = $resultado_cliente->fetch_assoc();
    $_SESSION['Correo_electronico'] = $correo_electronico; // Guardar el correo en la sesión
    $_SESSION['idcliente'] = $cliente['idcliente']; // Guardar el ID del cliente en la sesión
    header("Location: cliente/index_cliente.php");
    exit();
}

// Si no se encuentra ninguna coincidencia
$stmt_admin->close();
$stmt_cliente->close();
mysqli_close($conexion);

// Redirigir al formulario con un mensaje de error
header("Location: logout.php?error=1"); // Mensaje de error por defecto
exit();
?>
