<?php
include("conexion.php");

// Traer datos de la base
$sql = "SELECT d.nombre AS dispositivo, d.ip_privada, 
               s.id_solicitud, s.descripcion, s.ip_publica, s.puerto, 
               r.mensaje, r.fecha
        FROM dispositivos d
        INNER JOIN solicitudes s ON d.id_dispositivo = s.id_dispositivo
        INNER JOIN respuestas r ON s.id_solicitud = r.id_solicitud
        ORDER BY r.fecha DESC";

$result = $conn->query($sql);
$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulación NAT Mejorada</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background:linear-gradient(135deg,#eef3f8 0%,#e0e7f1 100%); color:#2c3e50; line-height:1.6; min-height:100vh; }

    /* Navbar */
    .navbar { background:linear-gradient(90deg,#2c3e50 0%,#4a6491 100%); color:white; padding:0.8rem 2rem; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,0.1); position:sticky; top:0; z-index:100; }
    .logo { display:flex; align-items:center; gap:0.8rem; }
    .logo i { font-size:1.8rem; color:#27ae60; }
    .logo h1 { font-size:1.5rem; font-weight:600; }
    .nav-links { display:flex; gap:1.5rem; }
    .nav-links a { color:white; text-decoration:none; padding:0.5rem 1rem; border-radius:4px; transition:all 0.3s ease; display:flex; align-items:center; gap:0.5rem; }
    .nav-links a:hover { background:rgba(255,255,255,0.1); }
    .nav-links a.active { background:#27ae60; }

    /* Container */
    .container { max-width:1200px; margin:2rem auto; padding:0 1.5rem; }

    /* Header */
    .header { text-align:center; margin-bottom:2.5rem; padding:1.5rem; background:white; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.05); }
    .header h2 { font-size:2.2rem; margin-bottom:1rem; color:#2c3e50; }
    .header p { font-size:1.1rem; max-width:800px; margin:0 auto; color:#546e7a; }

    /* Card */
    .simulation-card { background:white; border-radius:12px; box-shadow:0 5px 20px rgba(0,0,0,0.08); padding:2rem; margin-bottom:2rem; }

    /* Botones */
    .btn { padding:12px 24px; background:#27ae60; color:white; border:none; border-radius:6px; cursor:pointer; font-size:1rem; font-weight:500; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:0.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.1); }
    .btn:hover { background:#219653; transform:translateY(-2px); box-shadow:0 6px 8px rgba(0,0,0,0.15); }
    .btn-secondary { background:#4a6491; }
    .btn-secondary:hover { background:#3c5174; }

    /* Tabla */
    table { border-collapse:collapse; width:100%; margin-top:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.05); border-radius:8px; overflow:hidden; background:white; }
    th, td { padding:1rem; text-align:left; border-bottom:1px solid #e0e0e0; }
    th { background:#4a6491; color:white; font-weight:500; }
    tr:nth-child(even){ background-color:#f8f9fa; }
    tr:hover{ background-color:#f1f5f9; }
    .acciones a { color:#dc3545; font-weight:bold; text-decoration:none; }
    .error { color:#dc3545; font-weight:bold; }

    /* Modal */
    .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; }
    .modal-contenido { background:#fff; padding:20px; border-radius:8px; width:90%; max-width:500px; box-shadow:0 4px 12px rgba(0,0,0,0.3); position:relative; animation:aparecer 0.3s ease; }
    .modal-contenido h2 { margin-top:0; }
    .modal-contenido label { display:block; margin-top:10px; font-weight:bold; }
    .modal-contenido input { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:6px; }
    .modal-contenido button { margin-top:15px; background:#007bff; color:white; padding:10px; border:none; border-radius:6px; cursor:pointer; }
    .cerrar { position:absolute; right:15px; top:10px; font-size:18px; font-weight:bold; cursor:pointer; color:#666; }
    @keyframes aparecer { from{transform:scale(0.8);opacity:0;} to{transform:scale(1);opacity:1;} }

    /* Explicación NAT */
    .explanation { background:white; border-radius:12px; box-shadow:0 5px 20px rgba(0,0,0,0.08); padding:2rem; margin-top:2.5rem; }
    .explanation h3 { color:#2c3e50; margin-bottom:1.2rem; font-size:1.5rem; }
    .explanation-content { display:flex; gap:2rem; flex-wrap:wrap; }
    .explanation-text { flex:1; min-width:300px; }
    .explanation-visual { flex:1; min-width:300px; background:#f8f9fa; border-radius:8px; padding:1.5rem; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .ip-box { padding:0.8rem 1.5rem; background:#e3f2fd; border-radius:6px; margin:0.5rem 0; font-family:monospace; display:inline-block; }

    /* Footer */
    footer { text-align:center; padding:1.5rem; margin-top:3rem; background:#2c3e50; color:white; }

    /* Responsive */
    @media (max-width:768px){ .navbar{flex-direction:column; padding:1rem;} .nav-links{margin-top:1rem; flex-wrap:wrap; justify-content:center;} .explanation-content{flex-direction:column;} }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo"><i class="fas fa-network-wired"></i><h1>Simulador NAT</h1></div>
    <div class="nav-links">
      <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
      <a href="animation.html" class="active"><i class="fas fa-play-circle"></i> Ver Animación</a>
      <a href="#"><i class="fas fa-info-circle"></i> Acerca de</a>
    </div>
  </nav>

  <div class="container">
    <!-- Header -->
    <div class="header">
      <h2>Simulación de NAT (Network Address Translation)</h2>
      <p>Este simulador muestra cómo varias <b>IP privadas</b> de una casa u oficina son traducidas por un <b>router NAT</b> a una sola <b>IP pública</b> con diferentes puertos.</p>
    </div>

    <!-- Tarjeta -->
    <div class="simulation-card">
      <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
        <button class="btn" onclick="mostrar('nat')"><i class="fas fa-check-circle"></i> Con NAT</button>
        <button class="btn btn-secondary" onclick="mostrar('sin_nat')"><i class="fas fa-times-circle"></i> Sin NAT</button>
        <button class="btn btn-secondary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nueva Solicitud</button>
      </div>

      <!-- Tabla -->
      <table id="tabla">
        <thead>
          <tr>
            <th>Dispositivo</th>
            <th>IP Privada</th>
            <th>Solicitud</th>
            <th>IP Pública</th>
            <th>Puerto</th>
            <th>Respuesta</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($datos as $fila): ?>
            <tr>
              <td><?= $fila['dispositivo'] ?></td>
              <td><?= $fila['ip_privada'] ?></td>
              <td><?= $fila['descripcion'] ?></td>
              <td class="ip"><?= $fila['ip_publica'] ?></td>
              <td class="puerto"><?= $fila['puerto'] ?></td>
              <td class="respuesta"><?= $fila['mensaje'] ?></td>
              <td><?= $fila['fecha'] ?></td>
              <td class="acciones">
                <a href="procesar.php?eliminar=<?= $fila['id_solicitud'] ?>" onclick="return confirm('¿Seguro que quieres eliminar esta solicitud?')">🗑️ Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Modal (fuera de la tarjeta para que flote) -->
    <div id="modal" class="modal">
      <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModal()">✖</span>
        <h2>➕ Agregar nueva solicitud</h2>
        <form method="POST" action="procesar.php">
          <label>Nombre del dispositivo:</label>
          <input type="text" name="nombre_dispositivo" required>
          <label>IP Privada:</label>
          <input type="text" name="ip_privada" required>
          <label>Descripción:</label>
          <input type="text" name="descripcion" required>
          <label>IP Pública:</label>
          <input type="text" name="ip_publica" required>
          <label>Puerto:</label>
          <input type="number" name="puerto" required>
          <button type="submit" name="agregar">Agregar</button>
        </form>
      </div>
    </div>

    <!-- Explicación NAT -->
    <div class="explanation">
      <h3>¿Cómo funciona NAT?</h3>
      <div class="explanation-content">
        <div class="explanation-text">
          <p>El <strong>Network Address Translation (NAT)</strong> es un mecanismo utilizado por los routers para permitir que múltiples dispositivos en una red privada compartan una única dirección IP pública para acceder a Internet.</p>
          <p>Cuando un dispositivo de la red local quiere comunicarse con Internet:</p>
          <ol>
            <li>El dispositivo envía un paquete con su <span class="ip-box">IP privada</span> como origen</li>
            <li>El router intercepta el paquete y reemplaza la IP privada por la <span class="ip-box">IP pública</span></li>
            <li>El router guarda información de la traducción en su tabla NAT</li>
            <li>Cuando llega la respuesta, el router la redirige al dispositivo correcto</li>
          </ol>
        </div>
        <div class="explanation-visual">
          <i class="fas fa-random" style="font-size: 3rem; color: #4a6491; margin-bottom: 1rem;"></i>
          <p>NAT actúa como un intermediario que traduce entre direcciones privadas y públicas</p>
        </div>
      </div>
    </div>
  </div>

  <footer><p>Proyecto de Ingeniería de Software - Administración de Sistemas de Información</p></footer>

  <script>
    function mostrar(modo) {
      const filas = document.querySelectorAll("#tabla tbody tr");
      filas.forEach((fila) => {
        const ip = fila.querySelector(".ip");
        const puerto = fila.querySelector(".puerto");
        const respuesta = fila.querySelector(".respuesta");
        if (!ip || !puerto || !respuesta) return;
        if (modo === "nat") {
          if (ip.dataset.real) ip.textContent = ip.dataset.real;
          if (puerto.dataset.real) puerto.textContent = puerto.dataset.real;
          if (respuesta.dataset.real) respuesta.textContent = respuesta.dataset.real;
          respuesta.classList.remove("error");
        } else {
          if (!ip.dataset.real) ip.dataset.real = ip.textContent;
          if (!puerto.dataset.real) puerto.dataset.real = puerto.textContent;
          if (!respuesta.dataset.real) respuesta.dataset.real = respuesta.textContent;
          ip.textContent = "❌ No disponible";
          puerto.textContent = "❌";
          respuesta.textContent = "❌ Error de conexión (sin NAT)";
          respuesta.classList.add("error");
        }
      });
    }
    function abrirModal(){ document.getElementById("modal").style.display = "flex"; }
    function cerrarModal(){ document.getElementById("modal").style.display = "none"; }
    window.addEventListener("click",(e)=>{const modal=document.getElementById("modal"); if(e.target===modal) cerrarModal();});
  </script>
</body>
</html>
