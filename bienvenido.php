<?php
session_start();
require "usuario.php";

$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];

if (validar($cedula)) {
    header("Location: ingreso.php");
    exit;
}

$datos = [
    'cedula'       => $cedula,
    'nombre'       => $nombre,
    'estado_civil' => $_POST['estado_civil'],
    'correo'       => $_POST['correo'],
    'clave_hash'   => password_hash($_POST['clave'], PASSWORD_DEFAULT)
];

guardar($datos);

$_SESSION['usuario'] = $nombre;
$_SESSION['cedula'] = $cedula;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usuario registrado</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h1>USUARIO REGISTRADO</h1>
        <p class="exito">Bienvenido, <?= htmlspecialchars($nombre) ?>.</p>
        <p><a href="index.php">Volver al menú</a></p>
    </div>
</body>
</html>