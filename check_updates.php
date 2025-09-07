<?php
include("conexion.php");

// Obtener el conteo actual de dispositivos
$sql = "SELECT COUNT(*) as total FROM dispositivos";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$currentCount = $row['total'];

// Obtener el conteo anterior del parámetro GET
$previousCount = isset($_GET['conteo_actual']) ? intval($_GET['conteo_actual']) : $currentCount;

// Verificar si hay cambios
$hasChanges = ($currentCount != $previousCount);

// Devolver respuesta JSON
header('Content-Type: application/json');
echo json_encode([
    'hasChanges' => $hasChanges,
    'previousCount' => $previousCount,
    'currentCount' => $currentCount,
    'message' => $hasChanges ? 'Se detectaron cambios en los dispositivos' : 'No hay cambios'
]);

$conn->close();
?>