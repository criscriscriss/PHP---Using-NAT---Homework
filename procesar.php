<?php
include("conexion.php");

// --- AGREGAR SOLICITUD ---
if (isset($_POST['agregar'])) {
    $nombre_dispositivo = $_POST['nombre_dispositivo'];
    $ip_privada = $_POST['ip_privada'];
    $descripcion = $_POST['descripcion'];
    $ip_publica = $_POST['ip_publica'];
    $puerto = $_POST['puerto'];

    // Verificar si el dispositivo ya existe por nombre + IP privada
    $sql_check = "SELECT id_dispositivo FROM dispositivos 
                  WHERE nombre='$nombre_dispositivo' AND ip_privada='$ip_privada'";
    $res_check = $conn->query($sql_check);

    if ($res_check->num_rows > 0) {
        // Si ya existe, usar ese id
        $row = $res_check->fetch_assoc();
        $id_dispositivo = $row['id_dispositivo'];
    } else {
        // Si no existe, lo insertamos
        $sql_insert_dis = "INSERT INTO dispositivos (nombre, ip_privada) 
                           VALUES ('$nombre_dispositivo', '$ip_privada')";
        $conn->query($sql_insert_dis);
        $id_dispositivo = $conn->insert_id;
    }

    // Insertar en solicitudes
    $sql = "INSERT INTO solicitudes (id_dispositivo, descripcion, ip_publica, puerto)
            VALUES ('$id_dispositivo', '$descripcion', '$ip_publica', '$puerto')";
    if ($conn->query($sql) === TRUE) {
        $id_solicitud = $conn->insert_id;

        // Crear la respuesta automática
        $mensaje = "Respuesta de Internet para $descripcion → entregada a $nombre_dispositivo ($ip_privada)";

        $sql_resp = "INSERT INTO respuestas (id_solicitud, mensaje) VALUES ('$id_solicitud', '$mensaje')";
        $conn->query($sql_resp);
    }

    header("Location: index.php");
    exit();
}

// --- ELIMINAR SOLICITUD ---
if (isset($_GET['eliminar'])) {
    $id_solicitud = $_GET['eliminar'];

    // Primero eliminar respuesta
    $conn->query("DELETE FROM respuestas WHERE id_solicitud='$id_solicitud'");
    // Luego eliminar solicitud
    $conn->query("DELETE FROM solicitudes WHERE id_solicitud='$id_solicitud'");

    header("Location: index.php");
    exit();
}
?>
