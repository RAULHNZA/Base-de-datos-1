<?php
// Incluir archivo de conexión
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar actualización de inventario usando el procedimiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_inventario'])) {
    $id_producto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);
    $operacion = $_POST['operacion'];
    
    if ($id_producto > 0 && $cantidad > 0) {
        try {
            // Llamar al procedimiento almacenado
            $stmt = $conn->prepare("CALL p_ActualizarInventario(?, ?, ?)");
            $stmt->bind_param("iis", $id_producto, $cantidad, $operacion);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $mensaje = "✅ Inventario actualizado exitosamente<br>";
                    $mensaje .= "Producto ID: {$row['id_producto']}<br>";
                    $mensaje .= "Operación: " . ($operacion == 'agregar' ? '➕ Agregar' : '➖ Quitar') . "<br>";
                    $mensaje .= "Stock anterior: {$row['stock_anterior']}<br>";
                    $mensaje .= "Stock nuevo: {$row['stock_nuevo']}";
                    $tipo_mensaje = "success";
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $mensaje = "❌ Error al actualizar inventario: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "❌ Debes seleccionar un producto y especificar una cantidad válida";
        $tipo_mensaje = "error";
    }
}

// Obtener productos para el select
$productos = $conn->query("SELECT id_producto, nombre, marca, stock FROM productos ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .inventario-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .operacion-buttons {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        .btn-agregar {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            flex: 1;
        }
        .btn-quitar {
            background: #e67e22;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            flex: 1;
        }
        .stock-info {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .stock-normal { background: #d4edda; color: #155724; }
        .stock-bajo { background: #fff3cd; color: #856404; }
        .stock-critico { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📥 Gestión de Inventario</h1>
            <p>Actualización de stock usando procedimiento almacenado</p>
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

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <!-- Formulario de actualización -->
                <div>
                    <div class="inventario-card">
                        <h2>Actualizar Stock</h2>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="id_producto">Producto *</label>
                                <select id="id_producto" name="id_producto" required onchange="actualizarInfoStock()">
                                    <option value="">Seleccionar producto</option>
                                    <?php while ($producto = $productos->fetch_assoc()): ?>
                                        <option value="<?php echo $producto['id_producto']; ?>" 
                                                data-stock="<?php echo $producto['stock']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                                data-marca="<?php echo htmlspecialchars($producto['marca']); ?>">
                                            <?php echo htmlspecialchars($producto['nombre']); ?> - 
                                            <?php echo htmlspecialchars($producto['marca']); ?> 
                                            (Stock: <?php echo $producto['stock']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Información del stock actual -->
                            <div id="info_stock" style="display: none;">
                                <div class="form-group">
                                    <label>Información Actual</label>
                                    <div id="stock_display" class="stock-info stock-normal">
                                        <!-- Se actualiza con JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cantidad">Cantidad *</label>
                                <input type="number" id="cantidad" name="cantidad" min="1" required 
                                       placeholder="Cantidad a agregar o quitar">
                            </div>

                            <div class="operacion-buttons">
                                <button type="submit" name="actualizar_inventario" value="agregar" class="btn-agregar">
                                    ➕ Agregar al Inventario
                                </button>
                                <button type="submit" name="actualizar_inventario" value="quitar" class="btn-quitar">
                                    ➖ Quitar del Inventario
                                </button>
                            </div>

                            <input type="hidden" name="operacion" id="operacion" value="">
                        </form>
                    </div>

                    <!-- Información del procedimiento -->
                    <div class="inventario-card" style="background: #e8f4fd;">
                        <h3>📋 Procedimiento sp_ActualizarInventario</h3>
                        <p><strong>Función:</strong> Actualiza el stock de productos con validaciones</p>
                        <p><strong>Características:</strong></p>
                        <ul>
                            <li>✅ Valida existencia del producto</li>
                            <li>✅ Verifica stock suficiente para quitar</li>
                            <li>✅ Maneja errores con SIGNAL</li>
                            <li>✅ Retorna información del cambio</li>
                        </ul>
                    </div>
                </div>

                <!-- Información y estadísticas -->
                <div>
                    <!-- Resumen de inventario -->
                    <div class="inventario-card">
                        <h2>📊 Resumen de Inventario</h2>
                        <?php
                        $stats = $conn->query("
                            SELECT 
                                COUNT(*) as total_productos,
                                SUM(stock) as total_unidades,
                                AVG(stock) as promedio_stock,
                                COUNT(CASE WHEN stock = 0 THEN 1 END) as agotados,
                                COUNT(CASE WHEN stock <= 5 THEN 1 END) as stock_bajo
                            FROM productos
                        ")->fetch_assoc();
                        ?>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                            <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 5px;">
                                <div style="font-size: 1.5em; font-weight: bold; color: #3498db;">
                                    <?php echo $stats['total_productos']; ?>
                                </div>
                                <small>Total Productos</small>
                            </div>
                            
                            <div style="text-align: center; padding: 15px; background: #e8f4fd; border-radius: 5px;">
                                <div style="font-size: 1.5em; font-weight: bold; color: #27ae60;">
                                    <?php echo $stats['total_unidades']; ?>
                                </div>
                                <small>Total Unidades</small>
                            </div>
                            
                            <div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 5px;">
                                <div style="font-size: 1.5em; font-weight: bold; color: #e67e22;">
                                    <?php echo $stats['stock_bajo']; ?>
                                </div>
                                <small>Stock Bajo (≤5)</small>
                            </div>
                            
                            <div style="text-align: center; padding: 15px; background: #f8d7da; border-radius: 5px;">
                                <div style="font-size: 1.5em; font-weight: bold; color: #e74c3c;">
                                    <?php echo $stats['agotados']; ?>
                                </div>
                                <small>Productos Agotados</small>
                            </div>
                        </div>
                    </div>

                    <!-- Productos con stock bajo -->
                    <div class="inventario-card">
                        <h3>🚨 Productos que Necesitan Atención</h3>
                        <?php
                        $productos_bajo = $conn->query("
                            SELECT nombre, marca, stock 
                            FROM productos 
                            WHERE stock <= 5 
                            ORDER BY stock ASC 
                            LIMIT 5
                        ");
                        
                        if ($productos_bajo->num_rows > 0) {
                            echo '<table style="font-size: 0.9em; width: 100%;">';
                            while ($producto = $productos_bajo->fetch_assoc()) {
                                $clase_stock = $producto['stock'] == 0 ? 'stock-critico' : 
                                             ($producto['stock'] <= 2 ? 'stock-critico' : 'stock-bajo');
                                
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
                                echo '<td>' . htmlspecialchars($producto['marca']) . '</td>';
                                echo '<td><span class="' . $clase_stock . '" style="padding: 2px 8px; border-radius: 3px;">' . $producto['stock'] . '</span></td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                            echo '<p style="margin-top: 10px;"><a href="productos.php#stock-bajo">Ver todos los productos con stock bajo →</a></p>';
                        } else {
                            echo '<p>✅ No hay productos con stock crítico.</p>';
                        }
                        ?>
                    </div>

                    <!-- Movimientos recientes -->
                    <div class="inventario-card">
                        <h3>📝 Movimientos Recientes</h3>
                        <?php
                        $movimientos = $conn->query("
                            SELECT 
                                p.nombre as producto,
                                v.fecha_venta as fecha,
                                dv.cantidad,
                                'VENTA' as tipo
                            FROM detalle_venta dv
                            JOIN productos p ON dv.id_producto = p.id_producto
                            JOIN ventas v ON dv.id_venta = v.id_venta
                            WHERE v.estado = 'completada'
                            ORDER BY v.fecha_venta DESC
                            LIMIT 5
                        ");
                        
                        if ($movimientos->num_rows > 0) {
                            echo '<table style="font-size: 0.9em; width: 100%;">';
                            while ($mov = $movimientos->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . substr($mov['producto'], 0, 20) . '...</td>';
                                echo '<td style="color: #e74c3c;">-' . $mov['cantidad'] . '</td>';
                                echo '<td>' . $mov['tipo'] . '</td>';
                                echo '<td>' . substr($mov['fecha'], 0, 10) . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        } else {
                            echo '<p>No hay movimientos recientes.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p><a href="productos.php" style="color: #1abc9c;">📦 Ver Todos los Productos →</a></p>
            <p><a href="index.php" style="color: #1abc9c;">← Volver al Inicio</a></p>
        </footer>
    </div>

    <script>
        function actualizarInfoStock() {
            const select = document.getElementById('id_producto');
            const selectedOption = select.options[select.selectedIndex];
            const infoStock = document.getElementById('info_stock');
            const stockDisplay = document.getElementById('stock_display');
            
            if (selectedOption.value) {
                const stock = parseInt(selectedOption.getAttribute('data-stock'));
                const nombre = selectedOption.getAttribute('data-nombre');
                const marca = selectedOption.getAttribute('data-marca');
                
                // Determinar clase CSS según el stock
                let claseStock = 'stock-normal';
                if (stock === 0) {
                    claseStock = 'stock-critico';
                } else if (stock <= 5) {
                    claseStock = 'stock-bajo';
                }
                
                stockDisplay.className = 'stock-info ' + claseStock;
                stockDisplay.innerHTML = `
                    <strong>${nombre}</strong><br>
                    <small>${marca}</small><br>
                    Stock Actual: <strong>${stock} unidades</strong>
                `;
                
                infoStock.style.display = 'block';
            } else {
                infoStock.style.display = 'none';
            }
        }
        
        // Configurar los botones de operación
        document.querySelectorAll('button[name="actualizar_inventario"]').forEach(button => {
            button.addEventListener('click', function(e) {
                document.getElementById('operacion').value = this.value;
            });
        });
    </script>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>