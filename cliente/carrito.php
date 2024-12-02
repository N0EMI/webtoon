<?php
session_start();
include '../conexion.php'; 
// Comprobar si el cliente está registrado o no
$idcliente = isset($_SESSION['idcliente']) ? $_SESSION['idcliente'] : null;
/*
if (!isset($_SESSION['idcliente'])) {
    echo "<p>Por favor, inicia sesión para ver tu carrito.</p>";
    exit;
}*/

// Si el cliente está registrado, obtener los datos del cliente
if ($idcliente) {
    $sql_cliente = "SELECT nombrecliente, apellidocliente, correocliente, direccioncliente, telefono FROM cliente WHERE idcliente = ?";
    $stmt_cliente = $conexion->prepare($sql_cliente);
    $stmt_cliente->bind_param("i", $idcliente);
    $stmt_cliente->execute();
    $result_cliente = $stmt_cliente->get_result();
    $cliente = $result_cliente->fetch_assoc();
} else {
    $cliente = null;
}

/*$idcliente = $_SESSION['idcliente'];

// Obtener los datos del cliente
$sql_cliente = "SELECT nombrecliente, apellidocliente, correocliente, direccioncliente, telefono FROM cliente WHERE idcliente = ?";
$stmt_cliente = $conexion->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $idcliente);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$cliente = $result_cliente->fetch_assoc();*/

// Obtener los productos en el carrito
$sql_carrito = "SELECT p.idproducto, p.nombre, p.foto, c.cantidad, p.precio, (c.cantidad * p.precio) AS total_producto
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
    $productos_carrito[] = $row;
    $total += $row['total_producto'];
}

// Calcular el descuento (5% si el total supera 100)
$descuento = $total > 100 ? $total * 0.05 : 0;
$total_final = $total - $descuento;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../css/principal.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin: 20px 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .left, .right {
            width: 48%;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
        }

        .right table {
            width: 100%;
            border-collapse: collapse;
        }

        .right table th, .right table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .summary {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="../img/logo1.png" alt="Logo de la Farmacia" width="200">
</div>

<div class="container">
    <!-- Columna Izquierda: Datos del cliente -->
    <div class="left">
        <h3>Datos del Cliente</h3>
        <form id="finalizarCompraForm">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" value="<?php echo $cliente['nombrecliente']; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" value="<?php echo $cliente['apellidocliente']; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input type="text" value="<?php echo $cliente['correocliente']; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" value="<?php echo $cliente['telefono']; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Dirección de Envío</label>
                <input type="text" name="direccion_envio" value="<?php echo $cliente['direccioncliente']; ?>" required>
            </div>
            <div class="form-group">
                <label>Método de Pago</label>
                <select name="metodo_pago" required>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Yape">Yape</option>
                </select>
            </div>
        </form> 
    </div>

    <!-- Columna Derecha: Productos del carrito -->
    <div class="right">
        <h3>Productos en el Carrito</h3>
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_carrito as $producto): ?>
                <tr>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($producto['foto']); ?>" alt="Producto" width="50"></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo $producto['cantidad']; ?></td>
                    <td>S/ <?php echo number_format($producto['precio'], 2); ?></td>
                    <td>S/ <?php echo number_format($producto['total_producto'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary">
            <p>Subtotal: S/ <?php echo number_format($total, 2); ?></p>
            <p>Descuento: S/ <?php echo number_format($descuento, 2); ?></p>
            <h3>Total: S/ <?php echo number_format($total_final, 2); ?></h3>
        </div>
        <button class="btn" onclick="finalizarCompra()">Finalizar Compra</button>
        <button class="btn" onclick="abrirModalVolver()">Volver</button>

        <!--ventana de volver en carrito -->
        <div id="modalVolver" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
            padding: 20px; background: white; border: 1px solid #ccc; border-radius: 8px; z-index: 1000; width: 300px;">
            <h3>¿Qué desea hacer?</h3>
            <button class="btn" onclick="volverSinEliminar()">Volver sin eliminar carrito</button>
            <button class="btn" onclick="volverYEliminar()">Volver y eliminar carrito</button>
            <button class="btn" onclick="cerrarModalVolver()">Cancelar</button>
        </div>
        <!-- Fondo oscuro para el modal -->
        <div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0, 0, 0, 0.5); z-index: 999;" onclick="cerrarModalVolver()"></div>
    </div>
</div>

<script>
function finalizarCompra() {
    const form = document.getElementById('finalizarCompraForm');
    const formData = new FormData(form);

    fetch('procesar_compra.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Compra realizada con éxito');
           // window.location.href = 'historial.php';
           
           // Redirige a la factura
           window.location.href = data.redirect_url;
           //window.open(data.factura, '_blank');
         // window.location.href = '../cliente/generar_factura.php';
        } else {
            alert('Error al procesar la compra: ' + data.error);
        }
    });
}
function abrirModalVolver() {
    document.getElementById('modalVolver').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function cerrarModalVolver() {
    document.getElementById('modalVolver').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}

function volverSinEliminar() {
    // Redirigir al usuario a la página de inicio o donde desees
    window.location.href = '../cliente/index_cliente.php'; // Cambia esto según la URL de inicio
}

function volverYEliminar() {
    fetch('eliminar_carrito.php', {
        method: 'POST',
        body: JSON.stringify({ idcliente: <?php echo $idcliente; ?> }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('El carrito ha sido eliminado.');
            window.location.href = '../cliente/index_cliente.php'; // Cambia esto según la URL de inicio
        } else {
            alert('Error al eliminar el carrito: ' + data.error);
        }
    });
}

</script>

</body>
</html>
