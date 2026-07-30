<?php
session_start();
require "usuario.php";
require "registros.php";
 
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $clave  = $_POST['clave'];
 
    if (autenticar($cedula, $clave)) {
        $_SESSION['cedula'] = $cedula;
        $nombreUsuario = obtenerNombreUsuario($cedula);
        $_SESSION['usuario'] = $nombreUsuario !== false ? $nombreUsuario : $cedula;
        header("Location: tareas.php");
        exit;
    } else {
        registrarFallo($cedula);
        $error = "Cédula o contraseña incorrecta.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ingreso</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h1>Ingreso</h1>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <form method="POST" action="ingreso.php">
            <label>Cédula:</label>
            <input type="text" name="cedula" required><br>
            <label>Contraseña:</label>
            <input type="password" name="clave" required><br>
            <input type="submit" value="Ingresar">
        </form>
    </div>
</body>
</html>