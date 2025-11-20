<?php
// Incluir archivo de conexión
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Variables para el proceso de venta
$mensaje = '';
$tipo_mensaje = '';
$id_venta_generada = null;
$total_venta = 0;

// Procesar la venta usando el procedimiento almacenado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_venta'])) {
    $id_cliente = intval($_POST['id_cliente']);
    
    // Recoger los productos del carrito
    $detalles_venta = [];
    $productos_seleccionados = $_POST['productos'] ?? [];
    
    foreach ($productos_seleccionados as $index => $id_producto) {
        if (!empty($id_producto) && isset($_POST['cantidades'][$index])) {
            $cantidad = intval($_POST['cantidades'][$index]);
            if ($cantidad > 0) {
                $detalles_venta[] = [
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad
                ];
            }
        }
    }
    
    if (empty($id_cliente)) {
        $mensaje = "❌ Debes seleccionar un cliente";
        $tipo_mensaje = "error";
    } elseif (empty($detalles_venta)) {
        $mensaje = "❌ Debes agregar al menos un producto a la venta";
        $tipo_mensaje = "error";
    } else {
        // Convertir detalles a JSON para el procedimiento
        $json_detalles = json_encode($detalles_venta);
        
        try {
            // Llamar al procedimiento almacenado con cursor
            $stmt = $conn->prepare("CALL p_RealizarVenta(?, ?)");
            $stmt->bind_param("is", $id_cliente, $json_detalles);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $id_venta_generada = $row['id_venta_generada'];
                    $total_venta = $row['total_venta'];
                    $mensaje = "✅ Venta procesada exitosamente - ID: $id_venta_generada - Total: $" . number_format($total_venta, 2);
                    $tipo_mensaje = "success";
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $mensaje = "❌ Error al procesar venta: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    }
}

// Obtener datos para los selects
$clientes = $conn->query("SELECT id_cliente, nombre FROM clientes ORDER BY nombre");
$productos = $conn->query("SELECT id_producto, nombre, marca, precio, stock FROM productos WHERE stock > 0 ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Venta - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .producto-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .precio-info {
            color: #27ae60;
            font-weight: bold;
        }
        .stock-info {
            color: #e67e22;
            font-size: 0.9em;
        }
        .btn-agregar {
            background: #27ae60;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-eliminar {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🛒 Realizar Venta</h1>
            <p>Procesar ventas usando procedimiento almacenado con cursor</p>
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
                
                <?php if ($id_venta_generada): ?>
                    <div style="background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;">
                        <h4>📋 Detalles de la Venta Generada:</h4>
                        <p><strong>ID Venta:</strong> <?php echo $id_venta_generada; ?></p>
                        <p><strong>Total:</strong> $<?php echo number_format($total_venta, 2); ?></p>
                        <p><strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Formulario de venta -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <div>
                    <h2>Procesar Nueva Venta</h2>
                    <form method="POST" action="">
                        <!-- Selección de cliente -->
                        <div class="form-group">
                            <label for="id_cliente">Cliente *</label>
                            <select id="id_cliente" name="id_cliente" required>
                                <option value="">Seleccionar cliente</option>
                                <?php while ($cliente = $clientes->fetch_assoc()): ?>
                                    <option value="<?php echo $cliente['id_cliente']; ?>">
                                        <?php echo htmlspecialchars($cliente['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Productos disponibles -->
                        <div class="form-group">
                            <label>Productos Disponibles</label>
                            <select id="select_producto" style="width: 100%; margin-bottom: 10px;">
                                <option value="">Seleccionar producto</option>
                                <?php while ($producto = $productos->fetch_assoc()): ?>
                                    <option value="<?php echo $producto['id_producto']; ?>" 
                                            data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                            data-marca="<?php echo htmlspecialchars($producto['marca']); ?>"
                                            data-precio="<?php echo $producto['precio']; ?>"
                                            data-stock="<?php echo $producto['stock']; ?>">
                                        <?php echo htmlspecialchars($producto['nombre']); ?> - 
                                        <?php echo htmlspecialchars($producto['marca']); ?> - 
                                        $<?php echo number_format($producto['precio'], 2); ?> - 
                                        Stock: <?php echo $producto['stock']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="button" class="btn-agregar" onclick="agregarProducto()">➕ Agregar Producto</button>
                        </div>

                        <!-- Lista de productos seleccionados -->
                        <div class="form-group">
                            <label>Productos en la Venta</label>
                            <div id="lista_productos">
                                <!-- Los productos se agregarán aquí dinámicamente -->
                            </div>
                        </div>

                        <button type="submit" name="procesar_venta" class="btn-success">
                            🛒 Procesar Venta
                        </button>
                    </form>
                </div>

                <div>
                    <h2>Información de Venta</h2>
                    
                    <div style="background: #e8f4fd; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db; margin-bottom: 20px;">
                        <h3>📋 Procedimiento sp_RealizarVenta</h3>
                        <p><strong>Función:</strong> Procesa una venta completa con múltiples productos usando CURSOR</p>
                        <p><strong>Características:</strong></p>
                        <ul>
                            <li>✅ Usa CURSOR para iterar productos</li>
                            <li>✅ Valida stock disponible</li>
                            <li>✅ Actualiza inventario automáticamente</li>
                            <li>✅ Maneja transacciones (ROLLBACK/COMMIT)</li>
                            <li>✅ Calcula totales automáticamente</li>
                        </ul>
                    </div>

                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
                        <h4>⚠️ Triggers Activados</h4>
                        <p>Al procesar la venta se activan:</p>
                        <ul>
                            <li><strong>tg_ActualizarStock:</strong> Reduce stock automáticamente</li>
                            <li><strong>tg_CalcularSubtotal:</strong> Calcula subtotal por producto</li>
                        </ul>
                    </div>

                    <!-- Últimas ventas realizadas -->
                    <div style="margin-top: 20px;">
                        <h4>📦 Últimas Ventas</h4>
                        <?php
                        $ultimas_ventas = $conn->query("
                            SELECT v.id_venta, c.nombre as cliente, v.fecha_venta, v.total 
                            FROM ventas v 
                            JOIN clientes c ON v.id_cliente = c.id_cliente 
                            WHERE v.estado = 'completada'
                            ORDER BY v.fecha_venta DESC 
                            LIMIT 5
                        ");
                        
                        if ($ultimas_ventas->num_rows > 0) {
                            echo '<table style="font-size: 0.9em;">';
                            while ($venta = $ultimas_ventas->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>#' . $venta['id_venta'] . '</td>';
                                echo '<td>' . substr($venta['cliente'], 0, 15) . '...</td>';
                                echo '<td>$' . number_format($venta['total'], 2) . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p><a href="reportes.php" style="color: #1abc9c;">📊 Ver Reportes de Ventas →</a></p>
            <p><a href="index.php" style="color: #1abc9c;">← Volver al Inicio</a></p>
        </footer>
    </div>

    <script>
        let contadorProductos = 0;
        
        function agregarProducto() {
            const select = document.getElementById('select_producto');
            const selectedOption = select.options[select.selectedIndex];
            
            if (!selectedOption.value) {
                alert('Por favor selecciona un producto');
                return;
            }
            
            const id_producto = selectedOption.value;
            const nombre = selectedOption.getAttribute('data-nombre');
            const marca = selectedOption.getAttribute('data-marca');
            const precio = selectedOption.getAttribute('data-precio');
            const stock = selectedOption.getAttribute('data-stock');
            
            const listaProductos = document.getElementById('lista_productos');
            
            const productoDiv = document.createElement('div');
            productoDiv.className = 'producto-item';
            productoDiv.innerHTML = `
                <input type="hidden" name="productos[${contadorProductos}]" value="${id_producto}">
                <div>
                    <strong>${nombre}</strong><br>
                    <small>${marca} - Stock: ${stock}</small>
                </div>
                <div class="precio-info">$${parseFloat(precio).toFixed(2)}</div>
                <div>
                    <input type="number" name="cantidades[${contadorProductos}]" value="1" min="1" max="${stock}" style="width: 60px;">
                </div>
                <div class="precio-info" id="subtotal-${contadorProductos}">$${parseFloat(precio).toFixed(2)}</div>
                <div>
                    <button type="button" class="btn-eliminar" onclick="this.parentElement.parentElement.remove()">❌</button>
                </div>
            `;
            
            listaProductos.appendChild(productoDiv);
            contadorProductos++;
            
            // Reset select
            select.selectedIndex = 0;
        }
    </script>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>