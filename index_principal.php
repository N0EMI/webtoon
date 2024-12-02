<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Inicializar variables
$mensaje = '';
$tipo_alerta = '';

// Lógica para insertar un nuevo cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    
    // Obtener datos del formulario
    $nombrecliente = $_POST['nombrecliente'];
    $apellido = $_POST['apellidocliente'];
    $correo = $_POST['correocliente'];
    $contrasena = $_POST['contrasenacliente']; 
    $direccion = $_POST['direccioncliente'];
    $telefono = $_POST['telefono'];
    $created_at = $_POST['created_at'];

    // Consulta para insertar un nuevo cliente
    $sql = "INSERT INTO Cliente (nombrecliente, apellidocliente, correocliente, contrasenacliente, direccioncliente, telefono, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conexion->prepare($sql)) {
        // Asegúrate de que los tipos de datos sean correctos
        $stmt->bind_param("sssssii", $nombrecliente, $apellido, $correo, $contrasena, $direccion, $telefono, $created_at);
        if ($stmt->execute()) {
            $mensaje = "🎉 Ud. se ha registrado con éxito.😎";
            $tipo_alerta = "success";
        } else {
            $mensaje = "❌ Algo salió mal, intenta de nuevo por favor... " . $stmt->error;
            $tipo_alerta = "error";
        }
        $stmt->close();
    } else {
        $mensaje = "Error en la preparación de la consulta: " . $conexion->error;
        $tipo_alerta = "error";
    }
}

// Consulta para obtener categorías
$categorias = [];
$sql_categoria = "SELECT * FROM categoria";
$result = $conexion->query($sql_categoria);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
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
    <title>DROPRAX</title>
    <link rel="stylesheet" href="css/principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <div class="top-banner">
            <p>¿Aún no realizas tus compras?&nbsp;&nbsp;&nbsp;<a href="https://wa.me/51935099454" target="_blank"><i class="fab fa-whatsapp"></i>&nbsp;Escríbenos al +51 935 099 454</a></p>
        </div>
        <div class="header-nav">
            <img src="img/logo1.png" alt="logo dropax" class="logo">
            <input type="text" placeholder="Busca una marca o producto"><button type="submit"><i class="fas fa-search"></i></button>
            <ul class="nav-links">
                <li><a href="cliente/inicia_cliente.php" class="button"><i class="fas fa-sign-in-alt icon"></i> INICIAR SESION</a></li>
                <li><a href="#" class="button" id="openModal"><i class="fas fa-user-plus icon"></i> REGISTRARSE</a></li>
            </ul>
        </div>
    </header>

    <div class="scrolling-bar">
        <ul class="category-list">
            <?php foreach ($categorias as $categoria): ?>
                <li><a href="cliente/productos_cliente_no_regitrado.php?idcategoria=<?php echo $categoria['idcategoria']; ?>"><i class="fas fa-capsules icono-color"></i> <?php echo $categoria['nombrecat']; ?></a></li>
            <?php endforeach; ?>
        </ul>

    </div>

    <main>
        <section class="promo-banner">
            <div class="carousel">
                <div class="slides">
                    <img src="img/banner1.jpg" alt="Eucerin Promotion">
                    <img src="img/banner2.jpeg" alt="Vichy Promotion">
                    <img src="img/banner3.jpg" alt="Vichy Promotion">
                    <img src="img/banner4.jpg" alt="Vichy Promotion">
                    <img src="img/banner5.jpeg" alt="Vichy Promotion">
                    <img src="img/banner6.jpg" alt="Vichy Promotion">
                </div>
            </div>
        </section>
    </main>

    <!-- VENTANA EMERGENTE PARA REGISTRARSE -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="#" method="post">
                <h3>INGRESA TUS DATOS</h3>
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombrecliente" placeholder="Nombre" required>
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellidocliente" placeholder="Apellido" required>
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="correocliente" placeholder="Correo Electrónico" required>
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasenacliente" placeholder="Contraseña" required>
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccioncliente" placeholder="Dirección" required>
                <label for="celular">Celular:</label>
                <input type="text" name="telefono" placeholder="Celular" required>
                <label for = "created_at"> Fecha registro: </label>
                <input type="datetime-local" name="created_at" placeholder="Fecha registro" required>
                <input type="submit" value="Agregar Cliente">
            </form>
        </div>
    </div>

    <script>
        //BANNER
        let currentIndex = 0;
        const slides = document.querySelector('.slides');
        const totalSlides = document.querySelectorAll('.slides img').length;

        function showNextSlide() {
            currentIndex = (currentIndex + 1) % totalSlides;
            slides.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        setInterval(showNextSlide, 5000); // Cambia cada 5 segundos banner 

        //VENTANA EMERGENTE
        const modal = document.getElementById("myModal");
        const openModalBtn = document.getElementById("openModal");
        const closeModalBtn = document.querySelector(".close");

        openModalBtn.onclick = function() {
            modal.style.display = "flex";
        }

        closeModalBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }

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
    </script>

    <footer>
        <p>&copy; 2024 Droprax. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

