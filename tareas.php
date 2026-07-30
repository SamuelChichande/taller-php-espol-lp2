<?php
session_start();
require_once 'tarea.php';

if (empty($_SESSION['cedula'])) {
    header('Location: ingreso.php');
    exit;
}

function convertirATareasSesion($tareasArchivo) {
    $resultado = [];

    foreach ($tareasArchivo['pendientes'] as $tarea) {
        $resultado[] = [
            'id' => $tarea['id'],
            'text' => $tarea['text'],
            'completed' => false,
        ];
    }

    foreach ($tareasArchivo['completadas'] as $tarea) {
        $resultado[] = [
            'id' => $tarea['id'],
            'text' => $tarea['text'],
            'completed' => true,
        ];
    }

    return $resultado;
}

function cargarTareasDesdeArchivo() {
    $usuario = $_SESSION['usuario'] ?? $_SESSION['cedula'] ?? 'usuario';
    $_SESSION['tasks'] = convertirATareasSesion(listarTareas($usuario));
    return $_SESSION['tasks'];
}

$usuario = $_SESSION['usuario'] ?? $_SESSION['cedula'] ?? 'usuario';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $taskText = trim($_POST['task'] ?? '');
        if ($taskText !== '') {
            guardarTarea($usuario, $taskText);
        }
    }

    if (isset($_POST['action']) && isset($_POST['task_id'])) {
        $taskId = (int) $_POST['task_id'];
        if ($taskId > 0) {
            if ($_POST['action'] === 'complete') {
                completarTarea($usuario, $taskId);
            }
            if ($_POST['action'] === 'delete') {
                eliminarTarea($usuario, $taskId);
            }
        }
    }

    cargarTareasDesdeArchivo();
    header('Location: tareas.php');
    exit;
}

$_SESSION['tasks'] = cargarTareasDesdeArchivo();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Mis Tareas</h1>
        <?php $nombreUsuario = $_SESSION['usuario'] ?? $_SESSION['cedula'] ?? 'Usuario'; ?>
        <p>Usuario: <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></p>
    </header>

    <section>
        <form method="POST" action="tareas.php">
            <input type="hidden" name="action" value="add">
            <label for="task">Nueva tarea:</label>
            <input type="text" id="task" name="task" required>
            <button type="submit">Agregar</button>
        </form>
    </section>

    <section>
        <h2>Pendientes</h2>
        <?php $hasPending = false; ?>
        <ul>
            <?php foreach ($_SESSION['tasks'] as $task): ?>
                <?php if (!$task['completed']): ?>
                    <?php $hasPending = true; ?>
                    <li>
                        <?= htmlspecialchars($task['text'], ENT_QUOTES, 'UTF-8') ?>
                        <form method="POST" action="tareas.php" style="display:inline;">
                            <input type="hidden" name="action" value="complete">
                            <input type="hidden" name="task_id" value="<?= (int) ($task['id'] ?? 0) ?>">
                            <button type="submit">Completar</button>
                        </form>
                        <form method="POST" action="tareas.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?= (int) ($task['id'] ?? 0) ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?php if (!$hasPending): ?>
            <p>No hay tareas pendientes.</p>
        <?php endif; ?>
    </section>

    <section>
        <h2>Completadas</h2>
        <?php $hasCompleted = false; ?>
        <ul>
            <?php foreach ($_SESSION['tasks'] as $task): ?>
                <?php if ($task['completed']): ?>
                    <?php $hasCompleted = true; ?>
                    <li>
                        <?= htmlspecialchars($task['text'], ENT_QUOTES, 'UTF-8') ?>
                        <form method="POST" action="tareas.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?= (int) ($task['id'] ?? 0) ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?php if (!$hasCompleted): ?>
            <p>No hay tareas completadas.</p>
        <?php endif; ?>
    </section>
    <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>