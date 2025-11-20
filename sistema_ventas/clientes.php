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

// Procesar edición de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    
    if (!empty($nombre) && !empty($id_cliente)) {
        $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, telefono = ?, direccion = ? WHERE id_cliente = ?");
        $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $id_cliente);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $mensaje = "✅ Cliente actualizado exitosamente";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "ℹ️ No se realizaron cambios en el cliente";
                $tipo_mensaje = "info";
            }
        } else {
            $mensaje = "❌ Error al actualizar cliente: " . $stmt->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    } else {
        $mensaje = "❌ El nombre del cliente y ID son obligatorios";
        $tipo_mensaje = "error";
    }
}

// Procesar eliminación de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    
    if (!empty($id_cliente)) {
        // Verificar si el cliente tiene ventas relacionadas
        $check_stmt = $conn->prepare("SELECT COUNT(*) as total FROM ventas WHERE id_cliente = ?");
        $check_stmt->bind_param("i", $id_cliente);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        $check_stmt->close();
        
        if ($row['total'] > 0) {
            $mensaje = "❌ No se puede eliminar el cliente porque tiene ventas registradas";
            $tipo_mensaje = "error";
        } else {
            // Proceder con la eliminación
            $stmt = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            $stmt->bind_param("i", $id_cliente);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $mensaje = "✅ Cliente eliminado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "❌ No se encontró el cliente especificado";
                    $tipo_mensaje = "error";
                }
            } else {
                $mensaje = "❌ Error al eliminar cliente: " . $stmt->error;
                $tipo_mensaje = "error";
            }
            $stmt->close();
        }
    } else {
        $mensaje = "❌ ID de cliente no válido";
        $tipo_mensaje = "error";
    }
}

// Obtener datos del cliente para editar (si se solicita)
$cliente_editar = null;
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $stmt = $conn->prepare("SELECT id_cliente, nombre, telefono, direccion FROM clientes WHERE id_cliente = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente_editar = $result->fetch_assoc();
    $stmt->close();
}

// Obtener lista de clientes para los modales
$clientes_result = $conn->query("
    SELECT id_cliente, nombre, telefono, fecha_registro 
    FROM clientes 
    ORDER BY nombre ASC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
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
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-warning {
            background-color: #f39c12;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .cliente-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .acciones-tabla {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        .btn-accion {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.85em;
        }
    </style>
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

            <!-- Formulario para agregar/editar cliente -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <div>
                    <h2><?php echo $cliente_editar ? '✏️ Editar Cliente' : 'Agregar Nuevo Cliente'; ?></h2>
                    <form method="POST" action="">
                        <?php if ($cliente_editar): ?>
                            <input type="hidden" name="id_cliente" value="<?php echo $cliente_editar['id_cliente']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" required 
                                   placeholder="Ej: Juan Pérez García"
                                   value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['nombre']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" 
                                   placeholder="Ej: 5512345678" maxlength="10"
                                   value="<?php echo $cliente_editar ? htmlspecialchars($cliente_editar['telefono']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="3" 
                                      placeholder="Dirección completa del cliente"><?php echo $cliente_editar ? htmlspecialchars($cliente_editar['direccion']) : ''; ?></textarea>
                        </div>

                        <div class="btn-group">
                            <?php if ($cliente_editar): ?>
                                <button type="submit" name="editar_cliente" class="btn-warning">
                                    ✏️ Actualizar Cliente
                                </button>
                                <a href="clientes.php" class="btn-secondary">
                                    ✖️ Cancelar
                                </a>
                            <?php else: ?>
                                <button type="submit" name="agregar_cliente" class="btn-success">
                                    ➕ Agregar Cliente
                                </button>
                                <button type="button" onclick="abrirModalEliminar()" class="btn-danger">
                                    🗑️ Eliminar Cliente
                                </button>
                            <?php endif; ?>
                        </div>
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
                    
                    <?php if ($cliente_editar): ?>
                        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-top: 20px;">
                            <h3>✏️ Editando Cliente</h3>
                            <p><strong>ID:</strong> <?php echo $cliente_editar['id_cliente']; ?></p>
                            <p>Modifica los datos del cliente y haz clic en "Actualizar Cliente".</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Modal para eliminar cliente -->
            <div id="modalEliminar" class="modal">
                <div class="modal-content">
                    <h2>🗑️ Eliminar Cliente</h2>
                    <p>Selecciona el cliente que deseas eliminar:</p>
                    
                    <form method="POST" action="" id="formEliminar">
                        <div class="form-group">
                            <label for="id_cliente">Seleccionar Cliente:</label>
                            <select id="id_cliente" name="id_cliente" required onchange="mostrarInfoCliente(this.value)">
                                <option value="">-- Selecciona un cliente --</option>
                                <?php 
                                $clientes_result->data_seek(0); // Reiniciar puntero
                                while ($cliente = $clientes_result->fetch_assoc()): ?>
                                    <option value="<?php echo $cliente['id_cliente']; ?>">
                                        <?php echo htmlspecialchars($cliente['nombre']); ?> 
                                        (ID: <?php echo $cliente['id_cliente']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div id="infoCliente" class="cliente-info" style="display: none;">
                            <h4>Información del Cliente:</h4>
                            <p><strong>Nombre:</strong> <span id="infoNombre"></span></p>
                            <p><strong>Teléfono:</strong> <span id="infoTelefono"></span></p>
                            <p><strong>Fecha Registro:</strong> <span id="infoFecha"></span></p>
                            <p><strong>Ventas registradas:</strong> <span id="infoVentas"></span></p>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" name="eliminar_cliente" class="btn-danger" 
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente? Esta acción no se puede deshacer.')">
                                🗑️ Confirmar Eliminación
                            </button>
                            <button type="button" onclick="cerrarModalEliminar()" class="btn-secondary">
                                ✖️ Cancelar
                            </button>
                        </div>
                    </form>
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
                            <th>Acciones</th>
                          </tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id_cliente'] . '</td>';
                        echo '<td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>';
                        echo '<td>' . ($row['telefono'] ? htmlspecialchars($row['telefono']) : 'N/A') . '</td>';
                        echo '<td>' . ($row['direccion'] ? htmlspecialchars(substr($row['direccion'], 0, 50)) . '...' : 'N/A') . '</td>';
                        echo '<td>' . $row['fecha_registro'] . '</td>';
                        echo '<td class="acciones-tabla">';
                        echo '<a href="?editar=' . $row['id_cliente'] . '" class="btn-warning btn-accion">✏️ Editar</a>';
                        echo '</td>';
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

    <script>
        // Datos de clientes para el modal (podría mejorarse con AJAX)
        const clientes = {
            <?php 
            $clientes_result->data_seek(0); // Reiniciar el puntero del resultado
            while ($cliente = $clientes_result->fetch_assoc()): 
            ?>
                "<?php echo $cliente['id_cliente']; ?>": {
                    nombre: "<?php echo addslashes($cliente['nombre']); ?>",
                    telefono: "<?php echo addslashes($cliente['telefono'] ?: 'N/A'); ?>",
                    fecha: "<?php echo $cliente['fecha_registro']; ?>",
                    ventas: "<?php 
                        $check = $conn->query("SELECT COUNT(*) as total FROM ventas WHERE id_cliente = " . $cliente['id_cliente']);
                        echo $check->fetch_assoc()['total'];
                    ?>"
                },
            <?php endwhile; ?>
        };

        function abrirModalEliminar() {
            document.getElementById('modalEliminar').style.display = 'block';
        }

        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').style.display = 'none';
            document.getElementById('id_cliente').value = '';
            document.getElementById('infoCliente').style.display = 'none';
        }

        function mostrarInfoCliente(idCliente) {
            const infoDiv = document.getElementById('infoCliente');
            const cliente = clientes[idCliente];
            
            if (cliente) {
                document.getElementById('infoNombre').textContent = cliente.nombre;
                document.getElementById('infoTelefono').textContent = cliente.telefono;
                document.getElementById('infoFecha').textContent = cliente.fecha;
                document.getElementById('infoVentas').textContent = cliente.ventas + ' venta(s) registrada(s)';
                infoDiv.style.display = 'block';
                
                // Mostrar advertencia si tiene ventas
                if (cliente.ventas > 0) {
                    infoDiv.style.borderLeft = '4px solid #e74c3c';
                    // Agregar advertencia si no existe
                    if (!document.getElementById('advertenciaVentas')) {
                        const advertencia = document.createElement('p');
                        advertencia.id = 'advertenciaVentas';
                        advertencia.style.color = '#e74c3c';
                        advertencia.style.fontWeight = 'bold';
                        advertencia.textContent = '⚠️ Este cliente tiene ventas registradas y no puede ser eliminado.';
                        infoDiv.appendChild(advertencia);
                    }
                } else {
                    infoDiv.style.borderLeft = '4px solid #27ae60';
                    // Remover advertencia si existe
                    const advertencia = document.getElementById('advertenciaVentas');
                    if (advertencia) {
                        advertencia.remove();
                    }
                }
            } else {
                infoDiv.style.display = 'none';
            }
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modalEliminar');
            if (event.target === modal) {
                cerrarModalEliminar();
            }
        }

        // Scroll automático al formulario si estamos editando
        <?php if ($cliente_editar): ?>
            window.onload = function() {
                document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
            };
        <?php endif; ?>
    </script>

    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>