<<<<<<< HEAD
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
=======
<?php
include("conexion.php");
session_start();

// Obtener datos para la tabla
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

// Obtener dispositivos para visualización
$sql_dispositivos = "SELECT * FROM dispositivos";
$result_dispositivos = $conn->query($sql_dispositivos);
$dispositivos_disponibles = [];
while ($row = $result_dispositivos->fetch_assoc()) {
    $dispositivos_disponibles[] = $row;
}

// Verificar si hay una simulación guardada
$simulacionGuardada = isset($_SESSION['ultima_simulacion']) ? $_SESSION['ultima_simulacion'] : null;

// Obtener conteo actual de dispositivos para detección de cambios
$sql_conteo = "SELECT COUNT(*) as total FROM dispositivos";
$result_conteo = $conn->query($sql_conteo);
$row_conteo = $result_conteo->fetch_assoc();
$conteo_actual = $row_conteo['total'];

// Guardar simulación si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_simulacion'])) {
    $simulacion_data = $_POST['simulacion_data'];
    $_SESSION['ultima_simulacion'] = json_decode($simulacion_data, true);
    $simulacionGuardada = $_SESSION['ultima_simulacion'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulador NAT Completo</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
  <nav class="navbar">
    <div class="logo"><i class="fas fa-network-wired"></i><h1>Simulador NAT Completo</h1></div>
    <div class="nav-links">
      <a href="#tabla-section" class="active"><i class="fas fa-table"></i> Datos</a>
      <a href="#visualizacion-section"><i class="fas fa-project-diagram"></i> Visualización</a>
      <a href="#info-section"><i class="fas fa-info-circle"></i> Información</a>
    </div>
  </nav>

  <div class="container">
    <section id="tabla-section" class="section">
      <div class="header">
        <h2>Gestión de Solicitudes NAT</h2>
        <p>Administra y genera simulaciones de traducción de direcciones de red.</p>
      </div>

      <div class="simulation-card">
        <div class="controls">
          <button class="btn" onclick="mostrar('nat')"><i class="fas fa-check-circle"></i> Con NAT</button>
          <button class="btn btn-secondary" onclick="mostrar('sin_nat')"><i class="fas fa-times-circle"></i> Sin NAT</button>
          <button class="btn btn-secondary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nueva Solicitud</button>
          <button class="btn btn-random" onclick="generarSimulacionAleatoria()"><i class="fas fa-random"></i> Simulación Aleatoria</button>
          <button class="btn btn-success" onclick="actualizarVisualizacion()" id="btnActualizar"><i class="fas fa-sync"></i> Actualizar Visualización</button>
        </div>

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
                <td><?= htmlspecialchars($fila['dispositivo']) ?></td>
                <td><?= htmlspecialchars($fila['ip_privada']) ?></td>
                <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                <td class="ip"><?= htmlspecialchars($fila['ip_publica']) ?></td>
                <td class="puerto"><?= htmlspecialchars($fila['puerto']) ?></td>
                <td class="respuesta"><?= htmlspecialchars($fila['mensaje']) ?></td>
                <td><?= htmlspecialchars($fila['fecha']) ?></td>
                <td class="acciones">
                  <a href="procesar.php?eliminar=<?= $fila['id_solicitud'] ?>" onclick="return confirm('¿Seguro que quieres eliminar esta solicitud?')">🗑️ Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <form id="formSimulacion" method="POST" action="#visualizacion-section" style="display: none;">
          <input type="hidden" name="simulacion_data" id="simulacion_data">
          <input type="hidden" name="guardar_simulacion" value="1">
        </form>
      </div>
    </section>

    <section id="visualizacion-section" class="section">
      <div class="header">
        <h2>Visualización de Red en Tiempo Real</h2>
        <p>Observa cómo funciona el NAT con los dispositivos y solicitudes configurados.</p>
        <div id="statusMessage" class="status-message info">
          <i class="fas fa-info-circle"></i> <?php echo $simulacionGuardada ? 'Visualizando simulación personalizada' : 'Visualizando todos los dispositivos'; ?>
        </div>
      </div>

      <div class="controls">
        <button class="btn" onclick="initNetwork()">
          <i class="fas fa-sync"></i> Reiniciar Visualización
        </button>
        <button class="btn btn-secondary" onclick="toggleAutoRefresh()">
          <i class="fas fa-clock"></i> Auto-Actualizar: <span id="autoRefreshStatus">OFF</span>
        </button>
      </div>

      <div class="visualization">
        <div class="network-box">
          <h3 class="network-title"><i class="fas fa-globe-americas"></i> Red sin NAT</h3>
          <div id="networkWithoutNAT" class="network-container"></div>
        </div>
        <div class="network-box">
          <h3 class="network-title"><i class="fas fa-random"></i> Red con NAT</h3>
          <div id="networkWithNAT" class="network-container"></div>
        </div>
      </div>

      <div class="explanation">
        <h3>Dispositivos en la Red</h3>
        <div class="comparison">
          <div class="comparison-box">
            <h4 class="comparison-title"><i class="fas fa-laptop"></i> Dispositivos <?php echo $simulacionGuardada ? 'de la Simulación' : 'Registrados'; ?></h4>
            <ul id="deviceList">
              <?php 
              $dispositivosParaLista = $simulacionGuardada ? $simulacionGuardada['dispositivos'] : $dispositivos_disponibles;
              foreach ($dispositivosParaLista as $dispositivo): 
                $nombre = is_array($dispositivo) ? $dispositivo['nombre'] : $dispositivo;
                $ip = is_array($dispositivo) ? $dispositivo['ip_privada'] : $dispositivo;
              ?>
                <li><strong><?php echo htmlspecialchars($nombre); ?></strong>: <?php echo htmlspecialchars($ip); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="comparison-box">
            <h4 class="comparison-title"><i class="fas fa-info-circle"></i> Información de la Visualización</h4>
            <ul>
              <li><strong>Modo:</strong> <?php echo $simulacionGuardada ? 'Simulación personalizada' : 'Todos los dispositivos'; ?></li>
              <li><strong>Total dispositivos:</strong> <?php echo count($dispositivosParaLista); ?></li>
              <li><strong>Estado:</strong> <span class="status-text">Visualización activa</span></li>
              <?php if ($simulacionGuardada): ?>
              <li><strong>IP Pública compartida:</strong> <?php echo htmlspecialchars($simulacionGuardada['ipPublica']); ?></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section id="info-section" class="section">
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
    </section>
  </div>

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
        
        <button type="submit" name="agregar" class="btn">Agregar</button>
      </form>
    </div>
  </div>

  <footer>
    <p>Proyecto de Ingeniería de Software - Administración de Sistemas de Información</p>
  </footer>

  <script>
    const dispositivosBD = <?php echo json_encode($dispositivos_disponibles); ?>;
    const simulacionGuardada = <?php echo $simulacionGuardada ? json_encode($simulacionGuardada) : 'null'; ?>;
    const conteoActual = <?php echo $conteo_actual; ?>;
    
    const solicitudesPosibles = [
      "Navegar en Google", "Ver videos en YouTube", "Revisar correo electrónico",
      "Acceder a Facebook", "Usar WhatsApp Web", "Jugar en línea",
      "Ver Netflix", "Escuchar Spotify", "Conectar a Instagram",
      "Usar Twitter", "Acceder a Amazon", "Realizar búsquedas",
      "Conectar a Zoom", "Usar Microsoft Teams", "Actualizar software"
    ];

    const serviciosInternet = [
      { nombre: "Google", ip: "8.8.8.8" },
      { nombre: "YouTube", ip: "142.250.72.206" },
      { nombre: "Facebook", ip: "31.13.64.35" },
      { nombre: "Netflix", ip: "52.11.123.45" },
      { nombre: "WhatsApp", ip: "157.240.20.35" },
      { nombre: "Instagram", ip: "157.240.20.174" },
      { nombre: "Twitter", ip: "104.244.42.193" },
      { nombre: "Amazon", ip: "52.95.123.38" }
    ];

    let networkInstances = [];
    let autoRefreshInterval = null;
    let isAutoRefreshEnabled = false;
    let simulacionActual = <?php echo isset($_SESSION['ultima_simulacion']) ? json_encode($_SESSION['ultima_simulacion']) : 'null'; ?>;

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

    function abrirModal(){ 
      document.getElementById("modal").style.display = "flex"; 
    }

    function cerrarModal(){ 
      document.getElementById("modal").style.display = "none"; 
    }

    function generarSimulacionAleatoria() {
      const tbody = document.querySelector("#tabla tbody");
      tbody.innerHTML = '';
      
      const numDispositivos = Math.floor(Math.random() * 3) + 3;
      const dispositivosSeleccionados = [];
      const dispositivosCopia = [...dispositivosBD];
      
      for (let i = 0; i < numDispositivos; i++) {
        if (dispositivosCopia.length === 0) break;
        const randomIndex = Math.floor(Math.random() * dispositivosCopia.length);
        const dispositivo = dispositivosCopia.splice(randomIndex, 1)[0];
        dispositivosSeleccionados.push(dispositivo);
      }
      
      const ipPublicaComun = `181.50.23.${Math.floor(Math.random() * 100) + 1}`;
      let puertoBase = 5000;
      
      simulacionActual = {
        dispositivos: dispositivosSeleccionados,
        ipPublica: ipPublicaComun,
        solicitudes: []
      };
      
      dispositivosSeleccionados.forEach((dispositivo, index) => {
        const servicio = serviciosInternet[Math.floor(Math.random() * serviciosInternet.length)];
        const solicitud = solicitudesPosibles[Math.floor(Math.random() * solicitudesPosibles.length)];
        const puerto = puertoBase + index + 1;
        
        const fecha = new Date();
        const fechaFormateada = `${fecha.getFullYear()}-${(fecha.getMonth()+1).toString().padStart(2, '0')}-${fecha.getDate().toString().padStart(2, '0')} ${fecha.getHours().toString().padStart(2, '0')}:${fecha.getMinutes().toString().padStart(2, '0')}:${fecha.getSeconds().toString().padStart(2, '0')}`;
        
        simulacionActual.solicitudes.push({
          dispositivo: dispositivo.nombre,
          ip_privada: dispositivo.ip_privada,
          solicitud: solicitud,
          servicio: servicio.nombre,
          ip_publica: ipPublicaComun,
          puerto: puerto
        });
        
        const fila = document.createElement('tr');
        fila.innerHTML = `
          <td>${dispositivo.nombre}</td>
          <td>${dispositivo.ip_privada}</td>
          <td>${solicitud}</td>
          <td class="ip">${ipPublicaComun}</td>
          <td class="puerto">${puerto}</td>
          <td class="respuesta">Respuesta de Internet para ${solicitud} → entregada a ${dispositivo.nombre} (${dispositivo.ip_privada})</td>
          <td>${fechaFormateada}</td>
          <td class="acciones">
            <a href="#" onclick="return confirm('¿Seguro que quieres eliminar esta solicitud?')">🗑️ Eliminar</a>
          </td>
        `;
        
        tbody.appendChild(fila);
      });
      
      document.getElementById('btnActualizar').disabled = false;
      alert(`Se generó una simulación con ${dispositivosSeleccionados.length} dispositivos`);
    }

    function actualizarVisualizacion() {
      if (!simulacionActual) {
        alert('No hay una simulación para visualizar');
        return;
      }
      
      document.getElementById('simulacion_data').value = JSON.stringify(simulacionActual);
      document.getElementById('formSimulacion').submit();
    }

    function initNetwork() {
      const containerWithoutNAT = document.getElementById('networkWithoutNAT');
      const containerWithNAT = document.getElementById('networkWithNAT');
      
      containerWithoutNAT.innerHTML = '';
      containerWithNAT.innerHTML = '';
      
      const options = {
        nodes: {
          shape: 'dot',
          size: 30,
          font: { size: 14, face: 'Tahoma' },
          borderWidth: 2,
          shadow: true
        },
        edges: {
          width: 2,
          shadow: true,
          arrows: { to: { enabled: true, scaleFactor: 1 } }
        },
        physics: {
          stabilization: true,
          barnesHut: {
            gravitationalConstant: -80000,
            springConstant: 0.001,
            springLength: 200
          }
        }
      };
      
      const devices = generateDevicesForVisualization();
      const internetNodes = generateInternetNodes();
      
      const dataWithoutNAT = createNetworkData(devices, internetNodes, false);
      networkInstances[0] = new vis.Network(containerWithoutNAT, dataWithoutNAT, options);
      
      const dataWithNAT = createNetworkData(devices, internetNodes, true);
      networkInstances[1] = new vis.Network(containerWithNAT, dataWithNAT, options);
      
      networkInstances[0].on("stabilizationIterationsDone", function() {
        networkInstances[0].setOptions({ physics: false });
      });
      
      networkInstances[1].on("stabilizationIterationsDone", function() {
        networkInstances[1].setOptions({ physics: false });
      });
    }
    
    function generateDevicesForVisualization() {
      const devices = [];
      const dispositivosParaVisualizar = simulacionGuardada ? simulacionGuardada.dispositivos : dispositivosBD;
      
      dispositivosParaVisualizar.forEach((dispositivo, index) => {
        const nombre = dispositivo.nombre || dispositivo;
        const ipPrivada = dispositivo.ip_privada || dispositivo;
        const ipPublica = simulacionGuardada ? simulacionGuardada.ipPublica : '181.50.23.65';
        
        devices.push({
          id: index + 1,
          label: `${nombre}\n${ipPrivada}`,
          group: getDeviceType(nombre),
          ip_privada: ipPrivada,
          ip_publica: ipPublica,
          nombre: nombre
        });
      });
      
      return devices;
    }
    
    function getDeviceType(nombre) {
      const lowerNombre = nombre.toLowerCase();
      if (lowerNombre.includes('phone')) return 'smartphone';
      if (lowerNombre.includes('laptop')) return 'laptop';
      if (lowerNombre.includes('pc') || lowerNombre.includes('gamer')) return 'desktop';
      if (lowerNombre.includes('tv')) return 'tablet';
      if (lowerNombre.includes('tablet')) return 'tablet';
      return 'server';
    }
    
    function generateInternetNodes() {
      return [
        { id: 100, label: "Google\n8.8.8.8", group: "server" },
        { id: 101, label: "Facebook\n31.13.64.35", group: "server" },
        { id: 102, label: "Netflix\n52.11.123.45", group: "server" },
        { id: 103, label: "Router ISP\n181.50.23.1", group: "router" }
      ];
    }
    
    function createNetworkData(devices, internetNodes, withNAT) {
      const nodes = [];
      const edges = [];
      
      devices.forEach(device => {
        nodes.push({
          id: device.id,
          label: device.label,
          group: device.group,
          color: getColorForDevice(device.group),
          title: `Dispositivo: ${device.nombre}\nIP Privada: ${device.ip_privada}\nIP Pública: ${device.ip_publica}`
        });
      });
      
      internetNodes.forEach(node => {
        nodes.push({
          id: node.id,
          label: node.label,
          group: node.group,
          color: getColorForDevice(node.group)
        });
      });
      
      if (withNAT) {
        nodes.push({
          id: 50,
          label: "Router NAT\n192.168.1.1",
          group: "nat",
          color: getColorForDevice("nat"),
          title: "Router NAT - Traduce IPs privadas a públicas"
        });
        
        devices.forEach(device => {
          edges.push({
            from: device.id,
            to: 50,
            color: { color: '#97C2FC' },
            label: "LAN",
            title: `Conexión desde ${device.nombre} al Router NAT`
          });
        });
        
        internetNodes.forEach(node => {
          edges.push({
            from: 50,
            to: node.id,
            color: { color: '#FF9999' },
            label: "WAN",
            title: `Conexión desde Router NAT a ${node.label.split('\n')[0]}`
          });
        });
      } else {
        devices.forEach(device => {
          internetNodes.forEach(node => {
            if (Math.random() > 0.5) {
              edges.push({
                from: device.id,
                to: node.id,
                color: { color: '#97C2FC' },
                label: "Directo",
                title: `Conexión directa desde ${device.nombre} a ${node.label.split('\n')[0]}`
              });
            }
          });
        });
      }
      
      return { nodes: nodes, edges: edges };
    }
    
    function getColorForDevice(type) {
      const colors = {
        'laptop': '#9b59b6',
        'desktop': '#3498db',
        'smartphone': '#2ecc71',
        'tablet': '#1abc9c',
        'printer': '#e67e22',
        'server': '#e74c3c',
        'router': '#f1c40f',
        'nat': '#f1c40f'
      };
      return colors[type] || '#95a5a6';
    }

    async function checkForUpdates() {
      updateStatus("Verificando actualizaciones...", "info");
      
      try {
        const response = await fetch('check_updates.php?conteo_actual=' + conteoActual);
        const data = await response.json();
        
        if (data.hasChanges) {
          updateStatus("¡Se detectaron cambios! Recargando...", "success");
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          updateStatus("No hay cambios en la base de datos", "info");
        }
      } catch (error) {
        updateStatus("Error al verificar actualizaciones", "error");
      }
    }

    function toggleAutoRefresh() {
      isAutoRefreshEnabled = !isAutoRefreshEnabled;
      
      if (isAutoRefreshEnabled) {
        autoRefreshInterval = setInterval(checkForUpdates, 10000);
        document.getElementById('autoRefreshStatus').textContent = 'ON';
        updateStatus("Auto-actualización activada (cada 10 segundos)", "success");
      } else {
        clearInterval(autoRefreshInterval);
        document.getElementById('autoRefreshStatus').textContent = 'OFF';
        updateStatus("Auto-actualización desactivada", "info");
      }
    }

    function updateStatus(message, type) {
      const statusElement = document.getElementById('statusMessage');
      statusElement.innerHTML = `<i class="fas fa-${getStatusIcon(type)}"></i> ${message}`;
      statusElement.className = `status-message ${type}`;
    }

    function getStatusIcon(type) {
      const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle'
      };
      return icons[type] || 'info-circle';
    }

    window.addEventListener("click",(e)=>{
      const modal=document.getElementById("modal"); 
      if(e.target===modal) cerrarModal();
    });

    document.addEventListener('DOMContentLoaded', function() {
      initNetwork();
      
      if (window.location.hash === '#visualizacion-section') {
        document.getElementById('visualizacion-section').scrollIntoView();
      }
    });
  </script>
</body>
</html>
>>>>>>> 6af9e56 (The end of the project - PHP- Using NAT- Homework)
