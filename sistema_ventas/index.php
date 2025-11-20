<?php
// Incluir archivo de conexión
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas de Celulares</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📱 Sistema de Gestión de Ventas de Celulares</h1>
            <p>Control de inventario, ventas y reportes</p>
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
            <h2>Dashboard Principal</h2>
            
            <!-- Estadísticas rápidas -->
            <div class="stats">
                <?php
                // Obtener estadísticas básicas
                $stats = [];
                
                // Total de clientes
                $result = $conn->query("SELECT COUNT(*) as total FROM clientes");
                $stats['clientes'] = $result->fetch_assoc()['total'];
                
                // Total de productos
                $result = $conn->query("SELECT COUNT(*) as total FROM productos");
                $stats['productos'] = $result->fetch_assoc()['total'];
                
                // Total de ventas
                $result = $conn->query("SELECT COUNT(*) as total FROM ventas WHERE estado = 'completada'");
                $stats['ventas'] = $result->fetch_assoc()['total'];
                
                // Productos con stock bajo (usando vista)
                $result = $conn->query("SELECT COUNT(*) as total FROM vw_stockbajo");
                $stats['stock_bajo'] = $result->fetch_assoc()['total'];
                ?>
                
                <div class="stat-card">
                    <h3>Total Clientes</h3>
                    <div class="stat-number"><?php echo $stats['clientes']; ?></div>
                    <a href="clientes.php">Ver clientes</a>
                </div>
                
                <div class="stat-card">
                    <h3>Total Productos</h3>
                    <div class="stat-number"><?php echo $stats['productos']; ?></div>
                    <a href="productos.php">Ver productos</a>
                </div>
                
                <div class="stat-card">
                    <h3>Ventas Realizadas</h3>
                    <div class="stat-number"><?php echo $stats['ventas']; ?></div>
                    <a href="ventas.php">Nueva venta</a>
                </div>
                
                <div class="stat-card">
                    <h3>Stock Bajo</h3>
                    <div class="stat-number" style="color: #e74c3c;"><?php echo $stats['stock_bajo']; ?></div>
                    <a href="productos.php">Revisar</a>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db;">
                    <h3>🚀 Acciones Rápidas</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0;">
                            <a href="ventas.php" style="text-decoration: none; color: #3498db; font-weight: bold;">
                                ➕ Realizar nueva venta
                            </a>
                        </li>
                        <li style="margin: 10px 0;">
                            <a href="productos.php" style="text-decoration: none; color: #3498db; font-weight: bold;">
                                📦 Agregar nuevo producto
                            </a>
                        </li>
                        <li style="margin: 10px 0;">
                            <a href="clientes.php" style="text-decoration: none; color: #3498db; font-weight: bold;">
                                👥 Registrar nuevo cliente
                            </a>
                        </li>
                    </ul>
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #27ae60;">
                    <h3>📊 Reportes y Vistas</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0;">
                            <strong>Vista:</strong> 
                            <a href="productos.php#stock-bajo" style="text-decoration: none; color: #27ae60;">
                                Productos con stock bajo
                            </a>
                        </li>
                        <li style="margin: 10px 0;">
                            <strong>Vista:</strong> 
                            <a href="reportes.php" style="text-decoration: none; color: #27ae60;">
                                Ventas por marca
                            </a>
                        </li>
                        <li style="margin: 10px 0;">
                            <strong>Vista:</strong> 
                            <a href="clientes.php#activos" style="text-decoration: none; color: #27ae60;">
                                Clientes activos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Últimas ventas -->
            <div style="margin-top: 30px;">
                <h3>Últimas Ventas Realizadas</h3>
                <?php
                $result = $conn->query("
                    SELECT v.id_venta, c.nombre as cliente, v.fecha_venta, v.total 
                    FROM ventas v 
                    JOIN clientes c ON v.id_cliente = c.id_cliente 
                    WHERE v.estado = 'completada'
                    ORDER BY v.fecha_venta DESC 
                    LIMIT 5
                ");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr><th>ID Venta</th><th>Cliente</th><th>Fecha</th><th>Total</th></tr>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id_venta'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['cliente']) . '</td>';
                        echo '<td>' . $row['fecha_venta'] . '</td>';
                        echo '<td>$' . number_format($row['total'], 2) . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No hay ventas registradas aún.</p>';
                }
                ?>
            </div>

            <!-- Productos con stock bajo -->
            <!-- <div style="margin-top: 30px;">
                <h3>⚠️ Productos con Stock Bajo (Vista: vw_stockbajo)</h3>
                <?php
                $result = $conn->query("SELECT * FROM vw_stockbajo LIMIT 5");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr><th>Producto</th><th>Marca</th><th>Stock</th><th>Estado</th></tr>';
                    while ($row = $result->fetch_assoc()) {
                        $color = $row['estado_stock'] == 'AGOTADO' ? '#e74c3c' : 
                                ($row['estado_stock'] == 'CRÍTICO' ? '#e67e22' : '#f39c12');
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                        echo '<td>' . $row['stock'] . '</td>';
                        echo '<td style="color: ' . $color . '; font-weight: bold;">' . $row['estado_stock'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '<p><a href="productos.php">Ver todos los productos</a></p>';
                } else {
                    echo '<p>✅ Todo el stock está en niveles normales.</p>';
                }
                ?>
            </div> -->
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p>Sistema de Gestión de Ventas de Celulares - Proyecto Base de Datos</p>
        </footer>
    </div>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>