<?php

function getTareaFilePath($usuario) {
    return "tareas_" . $usuario . ".csv";
}

function guardarTarea($usuario, $texto) {
    $archivo = getTareaFilePath($usuario);
    $tareas = listarTareas($usuario);
    $ultimoId = 0;

    foreach (array_merge($tareas['pendientes'], $tareas['completadas']) as $tarea) {
        if ($tarea['id'] > $ultimoId) {
            $ultimoId = $tarea['id'];
        }
    }

    $nuevoId = $ultimoId + 1;
    $fila = [$nuevoId, $texto, 'Pendiente'];

    $handle = fopen($archivo, 'a');
    if ($handle === false) {
        return false;
    }
    fputcsv($handle, $fila);
    fclose($handle);
    return true;
}

function listarTareas($usuario) {
    $archivo = getTareaFilePath($usuario);
    $tareas = [
        'pendientes' => [],
        'completadas' => []
    ];

    if (!file_exists($archivo)) {
        return $tareas;
    }

    $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        $campos = str_getcsv($linea);
        if (count($campos) < 3) {
            continue;
        }

        $id = (int) $campos[0];
        $texto = $campos[1];
        $estado = trim($campos[2]);
        $tarea = [
            'id' => $id,
            'text' => $texto,
            'estado' => $estado
        ];

        if (strcasecmp($estado, 'Completado') === 0) {
            $tareas['completadas'][] = $tarea;
        } else {
            $tareas['pendientes'][] = $tarea;
        }
    }

    return $tareas;
}

function guardarTodasTareas($usuario, $tareas) {
    $archivo = getTareaFilePath($usuario);
    $handle = fopen($archivo, 'w');
    if ($handle === false) {
        return false;
    }

    foreach ($tareas['pendientes'] as $tarea) {
        fputcsv($handle, [$tarea['id'], $tarea['text'], $tarea['estado']]);
    }
    foreach ($tareas['completadas'] as $tarea) {
        fputcsv($handle, [$tarea['id'], $tarea['text'], $tarea['estado']]);
    }

    fclose($handle);
    return true;
}

function completarTarea($usuario, $id) {
    $tareas = listarTareas($usuario);
    $id = (int)$id;

    foreach ($tareas['pendientes'] as $index => $tarea) {
        if ($tarea['id'] === $id) {
            $tareas['pendientes'][$index]['estado'] = 'Completado';
            $tareas['completadas'][] = $tareas['pendientes'][$index];
            unset($tareas['pendientes'][$index]);
            break;
        }
    }

    $tareas['pendientes'] = array_values($tareas['pendientes']);
    return guardarTodasTareas($usuario, $tareas);
}

function eliminarTarea($usuario, $id) {
    $tareas = listarTareas($usuario);
    $id = (int)$id;

    foreach (['pendientes', 'completadas'] as $estado) {
        foreach ($tareas[$estado] as $index => $tarea) {
            if ($tarea['id'] === $id) {
                unset($tareas[$estado][$index]);
                $tareas[$estado] = array_values($tareas[$estado]);
                return guardarTodasTareas($usuario, $tareas);
            }
        }
    }

    return false;
}
