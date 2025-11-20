<?php
// Incluir archivo de conexión
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Procesar formulario de nuevo cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_cliente'])) {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    
    if (!empty($nombre)) {
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, telefono, direccion, fecha_registro) VALUES (?, ?, ?, CURDATE())");
        $stmt->bind_param("sss", $nombre, $telefono, $direccion);
        
        if ($stmt->execute()) {
            $mensaje = "✅ Cliente agregado exitosamente";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "❌ Error al agregar cliente: " . $stmt->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    } else {
        $mensaje = "❌ El nombre del cliente es obligatorio";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>👥 Gestión de Clientes</h1>
            <p>Registro y consulta de clientes del sistema</p>
        </header>

        <nav>
            <a href="index.php">Inicio</a>
            <a href="clientes.php">Clientes</a>
            <a href="productos.php">Productos</a>
            <a href="ventas.php">Realizar Venta</a>
            <a href="inventario.php">Inventario</a>
            <a href="reportes.php">Reportes</a>
        </nav>

        <main>
            <!-- Mostrar mensajes -->
            <?php if (isset($mensaje)): ?>
                <div class="<?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario para agregar cliente -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <div>
                    <h2>Agregar Nuevo Cliente</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" required 
                                   placeholder="Ej: Juan Pérez García">
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" 
                                   placeholder="Ej: 5512345678" maxlength="10">
                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="3" 
                                      placeholder="Dirección completa del cliente"></textarea>
                        </div>

                        <button type="submit" name="agregar_cliente" class="btn-success">
                            ➕ Agregar Cliente
                        </button>
                    </form>
                </div>

                <div>
                    <h2>Información</h2>
                    <div style="background: #e8f4fd; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db;">
                        <h3>📋 Total de clientes registrados:</h3>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM clientes");
                        $total_clientes = $result->fetch_assoc()['total'];
                        ?>
                        <div style="font-size: 2em; font-weight: bold; color: #3498db; text-align: center;">
                            <?php echo $total_clientes; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de todos los clientes -->
            <div style="margin-top: 40px;">
                <h2>Lista de Clientes Registrados</h2>
                <?php
                $result = $conn->query("
                    SELECT id_cliente, nombre, telefono, direccion, fecha_registro 
                    FROM clientes 
                    ORDER BY fecha_registro DESC
                ");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Fecha Registro</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id_cliente'] . '</td>';
                        echo '<td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>';
                        echo '<td>' . ($row['telefono'] ? htmlspecialchars($row['telefono']) : 'N/A') . '</td>';
                        echo '<td>' . ($row['direccion'] ? htmlspecialchars(substr($row['direccion'], 0, 50)) . '...' : 'N/A') . '</td>';
                        echo '<td>' . $row['fecha_registro'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No hay clientes registrados en el sistema.</p>';
                }
                ?>
            </div>

            <!-- Vista: Clientes Activos -->
            <div id="activos" style="margin-top: 40px;">
                <h2>🏆 Clientes Activos (Vista: vw_clientesactivos)</h2>
                <p><em>Vista que muestra los clientes con mayor actividad de compras</em></p>
                
                <?php
                $result = $conn->query("SELECT * FROM vw_clientesactivos");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr>
                            <th>Cliente</th>
                            <th>Total Compras</th>
                            <th>Total Gastado</th>
                            <th>Última Compra</th>
                            <th>Categoría</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        $color_categoria = $row['categoria_cliente'] == 'PREMIUM' ? '#e74c3c' : 
                                         ($row['categoria_cliente'] == 'FRECUENTE' ? '#f39c12' : '#3498db');
                        
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>';
                        echo '<td style="text-align: center;">' . $row['total_compras'] . '</td>';
                        echo '<td style="text-align: right;">$' . number_format($row['total_gastado'], 2) . '</td>';
                        echo '<td>' . ($row['ultima_compra'] ? $row['ultima_compra'] : 'Sin compras') . '</td>';
                        echo '<td style="color: ' . $color_categoria . '; font-weight: bold;">' . $row['categoria_cliente'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No hay datos de clientes activos disponibles.</p>';
                }
                ?>
            </div>

            <!-- Estadísticas adicionales -->
            <div style="margin-top: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center;">
                    <h4>Clientes Premium</h4>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM vw_clientesactivos WHERE categoria_cliente = 'PREMIUM'");
                    $premium = $result->fetch_assoc()['total'];
                    ?>
                    <div style="font-size: 1.5em; font-weight: bold; color: #e74c3c;"><?php echo $premium; ?></div>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center;">
                    <h4>Clientes Frecuentes</h4>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM vw_clientesactivos WHERE categoria_cliente = 'FRECUENTE'");
                    $frecuentes = $result->fetch_assoc()['total'];
                    ?>
                    <div style="font-size: 1.5em; font-weight: bold; color: #f39c12;"><?php echo $frecuentes; ?></div>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center;">
                    <h4>Clientes Ocasionales</h4>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM vw_clientesactivos WHERE categoria_cliente = 'OCASIONAL'");
                    $ocasionales = $result->fetch_assoc()['total'];
                    ?>
                    <div style="font-size: 1.5em; font-weight: bold; color: #3498db;"><?php echo $ocasionales; ?></div>
                </div>
            </div>
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p><a href="index.php" style="color: #1abc9c;">← Volver al Inicio</a></p>
        </footer>
    </div>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>