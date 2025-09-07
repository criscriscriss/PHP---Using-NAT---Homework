<?php
$servername = "localhost";
$username = "root";  // usuario por defecto en XAMPP
$password = "";      // contraseña vacía por defecto
$database = "redes"; // nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
