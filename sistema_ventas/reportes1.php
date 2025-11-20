<?php
// Incluir archivo de conexión
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Variables para reportes
$reporte_data = null;
$mensaje = '';
$tipo_mensaje = '';

// Procesar generación de reporte mensual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_reporte'])) {
    $mes = intval($_POST['mes']);
    $anio = intval($_POST['anio']);
    
    if ($mes > 0 && $anio > 0) {
        try {
            // Llamar al procedimiento almacenado con cursor
            $stmt = $conn->prepare("CALL p_GenerarReporteVentas(?, ?)");
            $stmt->bind_param("ii", $mes, $anio);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $reporte_data = $row;
                    $mensaje = "✅ Reporte generado exitosamente para " . DateTime::createFromFormat('!m', $mes)->format('F') . " de $anio";
                    $tipo_mensaje = "success";
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $mensaje = "❌ Error al generar reporte: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "❌ Debes seleccionar mes y año válidos";
        $tipo_mensaje = "error";
    }
}

// Obtener años disponibles para el select
$anios = $conn->query("
    SELECT DISTINCT YEAR(fecha_venta) as anio 
    FROM ventas 
    WHERE fecha_venta IS NOT NULL 
    ORDER BY anio DESC
");
?>

<!DOCTYPE html>
<html lang="es">




    <!-- links para exportar a excel ........................................................................-->
    <script src="https://unpkg.com/xlsx@0.16.9/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/file-saverjs@latest/FileSaver.min.js"></script>
    <script src="https://unpkg.com/tableexport@latest/dist/js/tableexport.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .reporte-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .estadistica {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .estadistica-item {
            text-align: center;
            padding: 20px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        .numero-grande {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .vista-table {
            font-size: 0.9em;
        }
        .vista-table th {
            background: #34495e;
            color: white;
        }
        .mes-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .highlight {
            background: #fff3cd !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Reportes y Estadísticas</h1>
            <p>Generación de reportes usando procedimientos con cursor y vistas</p>
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
                <!-- Generador de reportes -->
                <div>
                    <div class="reporte-card">
                        <h2>📈 Generar Reporte Mensual</h2>
                        <form method="POST" action="">
                            <div class="mes-selector">
                                <div class="form-group">
                                    <label for="mes">Mes *</label>
                                    <select id="mes" name="mes" required>
                                        <option value="">Seleccionar mes</option>
                                        <option value="1">Enero</option>
                                        <option value="2">Febrero</option>
                                        <option value="3">Marzo</option>
                                        <option value="4">Abril</option>
                                        <option value="5">Mayo</option>
                                        <option value="6">Junio</option>
                                        <option value="7">Julio</option>
                                        <option value="8">Agosto</option>
                                        <option value="9">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="anio">Año *</label>
                                    <select id="anio" name="anio" required>
                                        <option value="">Seleccionar año</option>
                                        <?php while ($anio_row = $anios->fetch_assoc()): ?>
                                            <option value="<?php echo $anio_row['anio']; ?>">
                                                <?php echo $anio_row['anio']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                        <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?> (Actual)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" name="generar_reporte" class="btn-success">
                                🚀 Generar Reporte
                            </button>
                        </form>
                    </div>

                    <!-- Información del procedimiento -->
                    <div class="reporte-card" style="background: #e8f4fd;">
                        <h3>📋 Procedimiento sp_GenerarReporteVentas</h3>
                        <p><strong>Función:</strong> Genera reporte mensual usando CURSOR para análisis de ventas</p>
                        <p><strong>Características:</strong></p>
                        <ul>
                            <li>✅ Usa CURSOR para encontrar producto más vendido</li>
                            <li>✅ Calcula totales y promedios</li>
                            <li>✅ Procesa datos de múltiples tablas</li>
                            <li>✅ Retorna estadísticas consolidadas</li>
                        </ul>
                    </div>

                    <!-- Reporte generado -->
                    <?php if ($reporte_data): ?>
                    <div class="reporte-card" style="background: #d4edda;">
                        <h3>📋 Resultado del Reporte</h3>
                        <div class="estadistica">
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #27ae60;">
                                    $<?php echo number_format($reporte_data['total_ventas_mes'], 2); ?>
                                </div>
                                <div>Total Ventas</div>
                            </div>
                            
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #3498db;">
                                    <?php echo $reporte_data['cantidad_ventas']; ?>
                                </div>
                                <div>Ventas Realizadas</div>
                            </div>
                            
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #e67e22;">
                                    <?php echo $reporte_data['producto_mas_vendido'] ?: 'N/A'; ?>
                                </div>
                                <div>Producto Más Vendido</div>
                            </div>
                            
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #9b59b6;">
                                    <?php echo $reporte_data['cantidad_producto_top']; ?>
                                </div>
                                <div>Cantidad Vendida</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Vistas y estadísticas -->
                <div>
                    <!-- Vista: Ventas por Marca -->
                    <div class="reporte-card">
                        <h2>🏷️ Ventas por Marca (Vista: vw_ventaspormarca)</h2>
                        <p><em>Vista que muestra el desempeño de ventas agrupado por marca</em></p>
                        
                        <?php
                        $ventas_marca = $conn->query("SELECT * FROM vw_ventaspormarca");
                        
                        if ($ventas_marca->num_rows > 0) {
                            echo '<table class="vista-table">';
                            echo '<tr>
                                    <th>Marca</th>
                                    <th>Total Ventas</th>
                                    <th>Productos Vendidos</th>
                                    <th>Ingreso Total</th>
                                    <th>Precio Promedio</th>
                                  </tr>';
                            
                            while ($marca = $ventas_marca->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($marca['marca']) . '</strong></td>';
                                echo '<td style="text-align: center;">' . $marca['total_ventas'] . '</td>';
                                echo '<td style="text-align: center;">' . $marca['total_productos_vendidos'] . '</td>';
                                echo '<td style="text-align: right;">$' . number_format($marca['ingreso_total'], 2) . '</td>';
                                echo '<td style="text-align: right;">$' . number_format($marca['precio_promedio'], 2) . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        } else {
                            echo '<p>No hay datos de ventas por marca disponibles.</p>';
                        }
                        ?>
                    </div>

                    <!-- Estadísticas generales -->
                    <div class="reporte-card">
                        <h2>📈 Estadísticas Generales</h2>
                        <?php
                        $stats_generales = $conn->query("
                            SELECT 
                                COUNT(*) as total_ventas,
                                SUM(total) as ingreso_total,
                                AVG(total) as promedio_venta,
                                MAX(total) as venta_maxima,
                                COUNT(DISTINCT id_cliente) as clientes_activos
                            FROM ventas 
                            WHERE estado = 'completada'
                        ")->fetch_assoc();
                        ?>
                        
                        <div class="estadistica">
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #3498db;">
                                    <?php echo $stats_generales['total_ventas']; ?>
                                </div>
                                <div>Total Ventas</div>
                            </div>
                            
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #27ae60;">
                                    $<?php echo number_format($stats_generales['ingreso_total'], 2); ?>
                                </div>
                                <div>Ingreso Total</div>
                            </div>
                            
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #e67e22;">
                                    $<?php echo number_format($stats_generales['promedio_venta'], 2); ?>
                                </div>
                                <div>Venta Promedio</div>
                            </div>
                            
                            <div class="estadistica-item">
                                <div class="numero-grande" style="color: #9b59b6;">
                                    <?php echo $stats_generales['clientes_activos']; ?>
                                </div>
                                <div>Clientes Activos</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top productos vendidos -->
                    <div class="reporte-card">
                        <h2>🔥 Productos Más Vendidos</h2>
                        <?php
                        $top_productos = $conn->query("
                            SELECT 
                                p.nombre,
                                p.marca,
                                SUM(dv.cantidad) as total_vendido,
                                SUM(dv.subtotal) as ingreso_total
                            FROM detalle_venta dv
                            JOIN productos p ON dv.id_producto = p.id_producto
                            JOIN ventas v ON dv.id_venta = v.id_venta
                            WHERE v.estado = 'completada'
                            GROUP BY p.id_producto, p.nombre, p.marca
                            ORDER BY total_vendido DESC
                            LIMIT 5
                        ");
                        
                        
                        if ($top_productos->num_rows > 0) {
                            // aqui pegue el id y agregue mas en class....................................................................................................................
                            echo '<table id="tabla" class="vista-table table table-border table-hover">';
                            echo '<tr>
                                    <th>Producto</th>
                                    <th>Marca</th>
                                    <th>Unidades Vendidas</th>
                                    <th>Ingreso</th>
                                  </tr>';
                            
                            $contador = 0;
                            while ($producto = $top_productos->fetch_assoc()) {
                                $clase = $contador == 0 ? 'highlight' : '';
                                echo '<tr class="' . $clase . '">';
                                echo '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
                                echo '<td>' . htmlspecialchars($producto['marca']) . '</td>';
                                echo '<td style="text-align: center;">' . $producto['total_vendido'] . '</td>';
                                echo '<td style="text-align: right;">$' . number_format($producto['ingreso_total'], 2) . '</td>';
                                echo '</tr>';
                                $contador++;
                            }
                            echo '</table>';
                        } else {
                            echo '<p>No hay datos de productos vendidos.</p>';
                        }
                        ?>
                        <!-- este es el boton........................................................................................................ -->
                                <div class="card-body">
            <button id="btnExportar" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar datos a Excel
            </button>

                    </div>
                </div>
            </div>

            <!-- Vista: Clientes Activos -->
            <div class="reporte-card" style="margin-top: 20px;">
                <h2>👥 Clientes Activos (Vista: vw_clientesactivos)</h2>
                <p><em>Vista que categoriza clientes según su frecuencia de compras</em></p>
                
                <?php
                $clientes_activos = $conn->query("SELECT * FROM vw_clientesactivos ORDER BY total_gastado DESC LIMIT 10");
                
                if ($clientes_activos->num_rows > 0) {
                    echo '<table class="vista-table">';
                    echo '<tr>
                            <th>Cliente</th>
                            <th>Total Compras</th>
                            <th>Total Gastado</th>
                            <th>Última Compra</th>
                            <th>Categoría</th>
                          </tr>';
                    
                    while ($cliente = $clientes_activos->fetch_assoc()) {
                        $color_categoria = $cliente['categoria_cliente'] == 'PREMIUM' ? '#e74c3c' : 
                                         ($cliente['categoria_cliente'] == 'FRECUENTE' ? '#f39c12' : '#3498db');
                        
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($cliente['nombre']) . '</strong></td>';
                        echo '<td style="text-align: center;">' . $cliente['total_compras'] . '</td>';
                        echo '<td style="text-align: right;">$' . number_format($cliente['total_gastado'], 2) . '</td>';
                        echo '<td>' . ($cliente['ultima_compra'] ? $cliente['ultima_compra'] : 'Sin compras') . '</td>';
                        echo '<td style="color: ' . $color_categoria . '; font-weight: bold;">' . $cliente['categoria_cliente'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No hay datos de clientes activos disponibles.</p>';
                }
                ?>
            </div>
        </main>

        <footer style="margin-top: 40px; padding: 20px; text-align: center; background: #34495e; color: white; border-radius: 5px;">
            <p><a href="index.php" style="color: #1abc9c;">← Volver al Inicio</a></p>
        </footer>
    </div>

    <script>
        // Establecer mes y año actual por defecto
        document.addEventListener('DOMContentLoaded', function() {
            const mesActual = new Date().getMonth() + 1;
            const anioActual = new Date().getFullYear();
            
            document.getElementById('mes').value = mesActual;
            document.getElementById('anio').value = anioActual;
        });
    </script>



<!-- script para exportar a excel .................................................................-->
    <script>
    const $btnExportar = document.querySelector("#btnExportar"),
        $tabla = document.querySelector("#tabla");

    $btnExportar.addEventListener("click", function() {
        let tableExport = new TableExport($tabla, {
            exportButtons: false, // No queremos botones
            filename: "Reporte de prueba", //Nombre del archivo de Excel
            sheetname: "Reporte de prueba", //Título de la hoja
        });
        let datos = tableExport.getExportData();
        let preferenciasDocumento = datos.tabla.xlsx;
        tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
    });
</script>


    <?php
    // Cerrar conexión
    $db->closeConnection();
    ?>
</body>
</html>