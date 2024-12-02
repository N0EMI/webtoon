<?php
/*
$server = "sql201.infinityfree.com";
$user = "if0_37835107";
$pass = "Noemi199 ";
$db = "if0_37835107_farma";*/
$server = "localhost";
$user = "root";
$pass = "";
$db = "farma";
// Crear conexión
$conexion = mysqli_connect($server, $user, $pass, $db);

// Verificar conexión
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
} else {
    //echo "Conexión exitosa";
}

?>