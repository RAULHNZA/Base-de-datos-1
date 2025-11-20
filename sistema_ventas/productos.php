<?php
// Incluir archivo de conexión
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Procesar formulario de nuevo producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = trim($_POST['nombre']);
    $marca = trim($_POST['marca']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $descripcion = trim($_POST['descripcion']);
    $id_proveedor = intval($_POST['id_proveedor']);
    
    if (!empty($nombre) && $precio > 0) {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, marca, precio, stock, descripcion, id_proveedor, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
        $stmt->bind_param("ssdisi", $nombre, $marca, $precio, $stock, $descripcion, $id_proveedor);
        
        if ($stmt->execute()) {
            $mensaje = "✅ Producto agregado exitosamente";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "❌ Error al agregar producto: " . $stmt->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    } else {
        $mensaje = "❌ El nombre y precio son obligatorios";
        $tipo_mensaje = "error";
    }
}

// Obtener lista de proveedores para el select
$proveedores = $conn->query("SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Gestión de Productos</h1>
            <p>Registro y consulta de celulares en inventario</p>
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

            <!-- Formulario para agregar producto -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <div>
                    <h2>Agregar Nuevo Producto</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nombre">Nombre del producto *</label>
                            <input type="text" id="nombre" name="nombre" required 
                                   placeholder="Ej: iPhone 15 Pro Max 256GB">
                        </div>

                        <div class="form-group">
                            <label for="marca">Marca *</label>
                            <input type="text" id="marca" name="marca" required 
                                   placeholder="Ej: Apple, Samsung, Xiaomi">
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio *</label>
                            <input type="number" id="precio" name="precio" step="0.01" min="0" required 
                                   placeholder="Ej: 15999.99">
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock inicial</label>
                            <input type="number" id="stock" name="stock" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="id_proveedor">Proveedor</label>
                            <select id="id_proveedor" name="id_proveedor">
                                <option value="">Seleccionar proveedor</option>
                                <?php while ($proveedor = $proveedores->fetch_assoc()): ?>
                                    <option value="<?php echo $proveedor['id_proveedor']; ?>">
                                        <?php echo htmlspecialchars($proveedor['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Características del celular..."></textarea>
                        </div>

                        <button type="submit" name="agregar_producto" class="btn-success">
                            ➕ Agregar Producto
                        </button>
                    </form>
                </div>

                <div>
                    <h2>Información del Inventario</h2>
                    <?php
                    // Estadísticas de productos
                    $stats = $conn->query("
                        SELECT 
                            COUNT(*) as total_productos,
                            SUM(stock) as total_stock,
                            AVG(precio) as precio_promedio
                        FROM productos
                    ")->fetch_assoc();
                    ?>
                    
                    <div style="background: #e8f4fd; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db; margin-bottom: 20px;">
                        <h3>📊 Resumen General</h3>
                        <p><strong>Total productos:</strong> <?php echo $stats['total_productos']; ?></p>
                        <p><strong>Total en stock:</strong> <?php echo $stats['total_stock']; ?> unidades</p>
                        <p><strong>Precio promedio:</strong> $<?php echo number_format($stats['precio_promedio'], 2); ?></p>
                    </div>

                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
                        <h4>⚠️ Productos Agotados</h4>
                        <?php
                        $agotados = $conn->query("SELECT COUNT(*) as total FROM productos WHERE stock = 0")->fetch_assoc()['total'];
                        ?>
                        <p style="font-size: 1.2em; font-weight: bold; color: #e74c3c;">
                            <?php echo $agotados; ?> productos
                        </p>
                        <a href="#stock-bajo" style="color: #e67e22;">Ver detalles</a>
                    </div>
                </div>
            </div>

            <!-- Lista de todos los productos -->
            <div style="margin-top: 40px;">
                <h2>Inventario de Productos</h2>
                <?php
                $result = $conn->query("
                    SELECT p.*, pr.nombre as proveedor_nombre
                    FROM productos p
                    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                    ORDER BY p.stock ASC, p.nombre
                ");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Proveedor</th>
                            <th>Fecha Ingreso</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        $stock_style = $row['stock'] == 0 ? 'color: #e74c3c; font-weight: bold;' : 
                                     ($row['stock'] <= 5 ? 'color: #e67e22; font-weight: bold;' : '');
                        
                        echo '<tr>';
                        echo '<td>' . $row['id_producto'] . '</td>';
                        echo '<td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                        echo '<td style="text-align: right;">$' . number_format($row['precio'], 2) . '</td>';
                        echo '<td style="text-align: center; ' . $stock_style . '">' . $row['stock'] . '</td>';
                        echo '<td>' . ($row['proveedor_nombre'] ? htmlspecialchars($row['proveedor_nombre']) : 'N/A') . '</td>';
                        echo '<td>' . $row['fecha_ingreso'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No hay productos registrados en el sistema.</p>';
                }
                ?>
            </div>

            <!-- Vista: Stock Bajo -->
            <div id="stock-bajo" style="margin-top: 40px;">
                <h2>🚨 Productos con Stock Bajo (Vista: vw_stockbajo)</h2>
                <p><em>Vista que muestra productos con stock crítico o agotado</em></p>
                
                <?php
                $result = $conn->query("SELECT * FROM vw_stockbajo ORDER BY stock ASC");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        $color_estado = $row['estado_stock'] == 'AGOTADO' ? '#e74c3c' : 
                                      ($row['estado_stock'] == 'CRÍTICO' ? '#e67e22' : '#f39c12');
                        
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                        echo '<td style="text-align: right;">$' . number_format($row['precio'], 2) . '</td>';
                        echo '<td style="text-align: center; font-weight: bold; color: ' . $color_estado . '">' . $row['stock'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['proveedor']) . '</td>';
                        echo '<td style="color: ' . $color_estado . '; font-weight: bold;">' . $row['estado_stock'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>✅ No hay productos con stock bajo.</p>';
                }
                ?>
            </div>

            <!-- Estadísticas por marca -->
            <div style="margin-top: 40px;">
                <h2>📈 Productos por Marca</h2>
                <?php
                $result = $conn->query("
                    SELECT 
                        marca,
                        COUNT(*) as total_productos,
                        SUM(stock) as total_stock,
                        AVG(precio) as precio_promedio
                    FROM productos 
                    GROUP BY marca 
                    ORDER BY total_productos DESC
                ");
                
                if ($result->num_rows > 0) {
                    echo '<table>';
                    echo '<tr>
                            <th>Marca</th>
                            <th>Total Productos</th>
                            <th>Total Stock</th>
                            <th>Precio Promedio</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($row['marca']) . '</strong></td>';
                        echo '<td style="text-align: center;">' . $row['total_productos'] . '</td>';
                        echo '<td style="text-align: center;">' . $row['total_stock'] . '</td>';
                        echo '<td style="text-align: right;">$' . number_format($row['precio_promedio'], 2) . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p><a href="inventario.php" style="color: #1abc9c;">📥 Ir a Gestión de Inventario →</a></p>
            <p><a href="index.php" style="color: #1abc9c;">← Volver al Inicio</a></p>
        </footer>
    </div>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>