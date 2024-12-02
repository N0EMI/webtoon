<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php';
session_start(); // Asegúrate de que la sesión esté iniciada

// Obtener la categoría seleccionada
$idcategoria = isset($_GET['idcategoria']) ? $_GET['idcategoria'] : null;
$productos = [];

// Consultar todas las categorías (para el menú)
$categorias = [];
$sql_categoria = "SELECT * FROM categoria";
$result_categoria = $conexion->query($sql_categoria);
if ($result_categoria) {
    while ($row = $result_categoria->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// Validar si la categoría está definida
if ($idcategoria) {
    // Consulta para obtener los productos de la categoría seleccionada
    $sql_productos = "SELECT * FROM producto WHERE idcategoria = ?";
    $stmt = $conexion->prepare($sql_productos);
    $stmt->bind_param("i", $idcategoria);
    $stmt->execute();
    $result_productos = $stmt->get_result();

    if ($result_productos->num_rows > 0) {
        while ($row = $result_productos->fetch_assoc()) {
            $productos[] = $row; // Guardar productos
        }
    } else {
        $mensaje = "No hay productos disponibles en esta categoría.";
        $tipo_alerta = "error";
    }
} else {
    $mensaje = "Categoría no seleccionada.";
    $tipo_alerta = "error";
}
// Consultar los datos del cliente que ha iniciado sesión
if (isset($_SESSION['idcliente'])) {
    $idcliente = $_SESSION['idcliente'];
    $sql_cliente = "SELECT * FROM cliente WHERE idcliente = ?";
    $stmt = $conexion->prepare($sql_cliente);
    $stmt->bind_param("i", $idcliente);
    $stmt->execute();
    $result_cliente = $stmt->get_result();

    if ($result_cliente->num_rows > 0) {
        $cliente = $result_cliente->fetch_assoc(); // Obtener datos del cliente
    } else {
        $mensaje = "No se encontraron datos del cliente.";
        $tipo_alerta = "error";
    }
}
// Cerrar la conexión
$conexion->close();
?>

<?php if (!empty($mensaje)): ?>
    <div class="alert <?php echo $tipo_alerta; ?>" id="alerta">
        <span class="closebtn" onclick="cerrarAlerta()">&times;</span> 
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos de la Categoría</title>
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/boton.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos para el modal y alertas */
        .modal {
            display: none;
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            margin-bottom: 15px;
            border-radius: 5px;
            display: none;
        }

        .alert.success {
            background-color: #4CAF50;
        }

        .alert.error {
            background-color: #f44336;
        }

        .alert .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            cursor: pointer;
        }

        .productos-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .producto-item {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .producto-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .producto-item h3 {
            margin: 10px 0;
            font-size: 1.2em;
            color: #333;
        }

        .producto-item p {
            font-size: 1.1em;
            color: #333;
        }

        .producto-item .button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .producto-item .button:hover {
            background-color: #0056b3;
        }

        .scrolling-bar {
            background-color: #f1f1f1;
            padding: 10px;
        }

        .category-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-x: auto;
        }

        .category-list li {
            margin-right: 20px;
            font-size: 1.1em;
        }

        .category-list a {
            color: #333;
            text-decoration: none;
        }

        .category-list a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <header>
        <div class="top-banner">
            <p>¿Aún no realizas tus compras?&nbsp;&nbsp;&nbsp;<a href="https://wa.me/51935099454" target="_blank"><i class="fab fa-whatsapp"></i>&nbsp;Escríbenos al +51 935 099 454</a></p>
        </div>
        <div class="header-nav">
            <img src="../img/logo1.png" alt="logo" class="logo">
            <input type="text" placeholder="Busca una marca o producto">
            <button type="submit"><i class="fas fa-search"></i></button>
            <a href="#" class="button" id="btnPerfil"><i class="fas fa-user icon"></i> Perfil de Usuario</a>
            <form action="../cliente/carrito.php" method="post">
                <button type="submit" class="styled-button">Mi carrito</button>
            </form>
        </div>
    </header>

    <div class="scrolling-bar">
        <ul class="category-list">
            <?php foreach ($categorias as $categoria): ?>
                <li><a href="../cliente/produtos_cliente_registrado.php?idcategoria=<?php echo $categoria['idcategoria']; ?>"><i class="fas fa-capsules icono-color"></i> <?php echo $categoria['nombrecat']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <main>
        <section class="productos">
            <div class="productos-list">
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto-item">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['foto']); ?>" alt="<?php echo $producto['nombre']; ?>" /> <h3><?php echo $producto['nombre']; ?></h3>
                            <p><?php echo $producto['descripcion']; ?></p>
                            <p>Precio: S/ <?php echo $producto['precio']; ?></p>
                            <p>Stock: <?php echo $producto['stock']; ?></p>
                            <br><br>
                            <!-- Botón para añadir al carrito -->
                            <button class="button" onclick="abrirModal(<?php echo $producto['idproducto']; ?>, '<?php echo $producto['nombre']; ?>', <?php echo $producto['precio']; ?>)">Añadir al carrito</button>

                            <!-- Modal para ingresar la cantidad -->
                            <div id="modalCantidad" class="modal">
                                <div class="modal-content">
                                    <span class="close" onclick="cerrarModalCantidad()">&times;</span>
                                    <h3>Seleccionar cantidad</h3>
                                    <input type="number" id="cantidad" min="1" value="1" />
                                    <button onclick="agregarCarrito()">Añadir al carrito</button>
                                </div>
                            </div>

                            <script>
                            // Aquí va tu código JavaScript

                            let idproductoSeleccionado = null;
                            let precioSeleccionado = null;
                            let nombreProductoSeleccionado = null;

                            function abrirModal(idproducto, nombre, precio) {
                                idproductoSeleccionado = idproducto;
                                precioSeleccionado = precio;
                                nombreProductoSeleccionado = nombre;
                                document.getElementById('modalCantidad').style.display = 'block';
                            }

                            function cerrarModalCantidad() {
                                document.getElementById('modalCantidad').style.display = 'none';
                            }

                            function agregarCarrito() {
                                const cantidad = document.getElementById('cantidad').value;
                                
                                if (cantidad > 0) {
                                    // Enviar los datos al servidor para agregar el producto al carrito
                                    fetch('../cliente/agregar_carrito.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: `idproducto=${idproductoSeleccionado}&cantidad=${cantidad}`
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert('Producto añadido al carrito');
                                            cerrarModalCantidad();
                                        } else {
                                            alert('Hubo un problema al agregar el producto al carrito');
                                        }
                                    });
                                } else {
                                    alert('La cantidad debe ser mayor que 0');
                                }
                            }
                            </script>


                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos disponibles en esta categoría.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <!-- Modal para mostrar datos del usuario -->
    <div id="modalUsuario" class="modal">
        <div class="modal-content">
            <span class="close" id="cerrarModal">&times;</span>
            <h3>Datos del Usuario</h3>
            <?php if ($cliente): ?>
                <p><strong>Nombre:</strong> <?php echo $cliente['nombrecliente']; ?></p>
                <p><strong>Apellido:</strong> <?php echo $cliente['apellidocliente']; ?></p>
                <p><strong>Correo:</strong> <?php echo $cliente['correocliente']; ?></p>
                <p><strong>Dirección:</strong> <?php echo $cliente['direccioncliente']; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $cliente['telefono']; ?></p>
                <div class="login-container">
                    <form action="../logout.php" method="post">
                        <button type="submit" class="styled-button">Salir</button>
                    </form>
                    <form action="../cliente/historial.php" method="get">
                        <button type="submit" class="styled-button">Historial de Compras</button>
                    </form>
                    <form action="Rastreo.php" method="get">
                        <button type="submit" class="styled-button">Rastrear Pedido</button>
                    </form>
                </div>
            <?php else: ?>
                <p>No se encontraron datos del usuario.</p>
            <?php endif; ?>
        </div>
    </div>


    <footer>
        <p>&copy; 2024 Droprax. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Función para cerrar la alerta
        function cerrarAlerta() {
            document.getElementById('alerta').style.display = 'none';
        }

        // Mostrar la alerta si hay un mensaje
        window.onload = function() {
            var alerta = document.getElementById('alerta');
            if (alerta) {
                alerta.style.display = 'block';
            }
        }
        // Mostrar el modal al hacer clic en el botón de perfil
        document.getElementById('btnPerfil').onclick = function() {
            document.getElementById('modalUsuario').style.display = 'block';
        }

        // Cerrar el modal cuando se hace clic en la 'x'
        document.getElementById('cerrarModal').onclick = function() {
            document.getElementById('modalUsuario').style.display = 'none';
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target == document.getElementById('modalUsuario')) {
                document.getElementById('modalUsuario').style.display = 'none';
            }
        }
    </script>

</body>
</html>
