<?php
session_start();

if (empty($_SESSION['cedula'])) {
    header('Location: ingreso.php');
    exit;
}

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $taskText = trim($_POST['task'] ?? '');
        if ($taskText !== '') {
            $_SESSION['tasks'][] = [
                'text' => htmlspecialchars($taskText, ENT_QUOTES, 'UTF-8'),
                'completed' => false,
            ];
        }
    }

    if (isset($_POST['action']) && isset($_POST['task_id'])) {
        $taskId = (int) $_POST['task_id'];
        if (array_key_exists($taskId, $_SESSION['tasks'])) {
            if ($_POST['action'] === 'complete') {
                $_SESSION['tasks'][$taskId]['completed'] = true;
            }
            if ($_POST['action'] === 'delete') {
                array_splice($_SESSION['tasks'], $taskId, 1);
            }
        }
    }

    header('Location: tareas.php');
    exit;
}
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
        <p>Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?></p>
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
            <?php foreach ($_SESSION['tasks'] as $id => $task): ?>
                <?php if (!$task['completed']): ?>
                    <?php $hasPending = true; ?>
                    <li>
                        <?= $task['text'] ?>
                        <form method="POST" action="tareas.php" style="display:inline;">
                            <input type="hidden" name="action" value="complete">
                            <input type="hidden" name="task_id" value="<?= $id ?>">
                            <button type="submit">Completar</button>
                        </form>
                        <form method="POST" action="tareas.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?= $id ?>">
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
            <?php foreach ($_SESSION['tasks'] as $id => $task): ?>
                <?php if ($task['completed']): ?>
                    <?php $hasCompleted = true; ?>
                    <li>
                        <?= $task['text'] ?>
                        <form method="POST" action="tareas.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?= $id ?>">
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