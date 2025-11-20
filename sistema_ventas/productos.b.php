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

// Procesar eliminación de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_producto'])) {
    $id_producto = intval($_POST['id_producto']);
    
    if ($id_producto > 0) {
        // Verificar si el producto existe
        $check_stmt = $conn->prepare("SELECT nombre FROM productos WHERE id_producto = ?");
        $check_stmt->bind_param("i", $id_producto);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            // Obtener el nombre del producto para el mensaje
            $check_stmt->bind_result($nombre_producto);
            $check_stmt->fetch();
            
            // Verificar si el producto tiene ventas relacionadas
            $ventas_stmt = $conn->prepare("SELECT COUNT(*) FROM detalle_venta WHERE id_producto = ?");
            $ventas_stmt->bind_param("i", $id_producto);
            $ventas_stmt->execute();
            $ventas_stmt->bind_result($total_ventas);
            $ventas_stmt->fetch();
            $ventas_stmt->close();
            
            if ($total_ventas > 0) {
                $mensaje = "❌ No se puede eliminar el producto <strong>'$nombre_producto'</strong> porque tiene $total_ventas venta(s) relacionada(s).";
                $tipo_mensaje = "error";
            } else {
                // Eliminar el producto (no tiene ventas relacionadas)
                $delete_stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
                $delete_stmt->bind_param("i", $id_producto);
                
                if ($delete_stmt->execute()) {
                    $mensaje = "✅ Producto <strong>'$nombre_producto'</strong> eliminado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "❌ Error al eliminar producto: " . $delete_stmt->error;
                    $tipo_mensaje = "error";
                }
                $delete_stmt->close();
            }
        } else {
            $mensaje = "❌ El producto no existe";
            $tipo_mensaje = "error";
        }
        $check_stmt->close();
    } else {
        $mensaje = "❌ ID de producto inválido";
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
    <style>
        .eliminar-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .eliminar-btn:hover {
            background-color: #c0392b;
        }

        .eliminar-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
        
        .acciones-col {
            width: 120px;
            text-align: center;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 500px;
            max-width: 90%;
        }
        
        .modal-buttons {
            text-align: right;
            margin-top: 20px;
        }
        
        .cancel-btn {
            background-color: #95a5a6;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        .confirm-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
        }

        .info-ventas {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Gestión de Productos</h1>
            <p>Registro, consulta y eliminación de celulares en inventario</p>
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
                        <!-- ... (el formulario de agregar permanece igual) ... -->
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
                    SELECT p.*, pr.nombre as proveedor_nombre,
                    (SELECT COUNT(*) FROM detalle_venta dv WHERE dv.id_producto = p.id_producto) as total_ventas
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
                            <th>Ventas</th>
                            <th>Fecha Ingreso</th>
                            <th class="acciones-col">Acciones</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        $stock_style = $row['stock'] == 0 ? 'color: #e74c3c; font-weight: bold;' : 
                                     ($row['stock'] <= 5 ? 'color: #e67e22; font-weight: bold;' : '');
                        
                        $puede_eliminar = $row['total_ventas'] == 0;
                        
                        echo '<tr>';
                        echo '<td>' . $row['id_producto'] . '</td>';
                        echo '<td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                        echo '<td style="text-align: right;">$' . number_format($row['precio'], 2) . '</td>';
                        echo '<td style="text-align: center; ' . $stock_style . '">' . $row['stock'] . '</td>';
                        echo '<td>' . ($row['proveedor_nombre'] ? htmlspecialchars($row['proveedor_nombre']) : 'N/A') . '</td>';
                        echo '<td style="text-align: center;">' . $row['total_ventas'] . '</td>';
                        echo '<td>' . $row['fecha_ingreso'] . '</td>';
                        echo '<td class="acciones-col">';
                        if ($puede_eliminar) {
                            echo '<button type="button" class="eliminar-btn" onclick="confirmarEliminacion(' . $row['id_producto'] . ', \'' . htmlspecialchars(addslashes($row['nombre'])) . '\')">
                                    🗑️ Eliminar
                                  </button>';
                        } else {
                            echo '<button type="button" class="eliminar-btn" disabled title="No se puede eliminar - Tiene ' . $row['total_ventas'] . ' venta(s) relacionada(s)">
                                    ❌ Bloqueado
                                  </button>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No hay productos registrados en el sistema.</p>';
                }
                ?>
            </div>

            <!-- Modal de confirmación para eliminar -->
            <div id="modalEliminar" class="modal">
                <div class="modal-content">
                    <h3>⚠️ Confirmar Eliminación</h3>
                    <p id="textoConfirmacion">¿Estás seguro de que deseas eliminar este producto?</p>
                    <div class="info-ventas">
                        <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. El producto se eliminará permanentemente del sistema.
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="cancel-btn" onclick="cerrarModal()">Cancelar</button>
                        <form id="formEliminar" method="POST" style="display: inline;">
                            <input type="hidden" name="id_producto" id="id_producto_eliminar">
                            <button type="submit" name="eliminar_producto" class="confirm-btn">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Resto del código (Vista Stock Bajo y Estadísticas) permanece igual -->
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p><a href="inventario.php" style="color: #1abc9c;">📥 Ir a Gestión de Inventario →</a></p>
            <p><a href="index.php" style="color: #1abc9c;">← Volver al Inicio</a></p>
        </footer>
    </div>

    <script>
        function confirmarEliminacion(id, nombre) {
            document.getElementById('id_producto_eliminar').value = id;
            document.getElementById('textoConfirmacion').innerHTML = 
                '¿Estás seguro de que deseas eliminar el producto: <strong>' + nombre + '</strong>?';
            document.getElementById('modalEliminar').style.display = 'block';
        }
        
        function cerrarModal() {
            document.getElementById('modalEliminar').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            var modal = document.getElementById('modalEliminar');
            if (event.target == modal) {
                cerrarModal();
            }
        }
    </script>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>