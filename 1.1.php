<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Tienda de Celulares</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --refurbished-color: #9b59b6;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
        }
        
        .logo {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .nav-links {
            list-style: none;
        }
        
        .nav-links li {
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .nav-links li:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-links li.active {
            background-color: var(--secondary-color);
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .header h2 {
            color: var(--primary-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 0.9rem;
            color: #777;
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .card-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .card-footer {
            margin-top: 10px;
            font-size: 0.8rem;
            color: #777;
        }
        
        /* Tables */
        .table-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-shipped {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-refurbished {
            background-color: #e8d4f7;
            color: #5e3370;
        }
        
        .status-new {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-refurbished {
            background-color: var(--refurbished-color);
            color: white;
        }
        
        .btn-refurbished:hover {
            background-color: #8e44ad;
        }
        
        /* Forms */
        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            border-bottom: 3px solid var(--secondary-color);
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Product Cards */
        .product-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 180px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 3rem;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .product-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .product-price {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }
        
        .badge-new {
            background-color: var(--secondary-color);
        }
        
        .badge-refurbished {
            background-color: var(--refurbished-color);
        }
        
        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .chart-placeholder {
            height: 300px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            border-radius: 4px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .stats-section {
                grid-template-columns: 1fr;
            }
            
            .product-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h1><i class="fas fa-mobile-alt"></i> Admin Celulares</h1>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-mobile"></i> Productos</a></li>
                <li><a href="#"><i class="fas fa-shopping-cart"></i> Pedidos</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Reportes</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configuración</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>Panel de Administración - Celulares</h2>
                <div class="user-info">
                    <div class="user-avatar">AD</div>
                    <span>Administrador</span>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <!-- es donde aparecen todos los datos -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Ventas Totales</div>
                        <div class="card-icon" style="background-color: var(--secondary-color);"><i class="fas fa-dollar-sign"></i></div>
                    </div>
                    <div class="card-value">$24,850</div>
                    <div class="card-footer">+18% respecto al mes anterior</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pedidos</div>
                        <div class="card-icon" style="background-color: var(--success-color);"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                    <div class="card-value">126</div>
                    <div class="card-footer">18 pendientes</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Celulares en Stock</div>
                        <div class="card-icon" style="background-color: var(--warning-color);"><i class="fas fa-mobile"></i></div>
                    </div>
                    <div class="card-value">78</div>
                    <div class="card-footer">12 reacondicionados</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Clientes</div>
                        <div class="card-icon" style="background-color: var(--accent-color);"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="card-value">342</div>
                    <div class="card-footer">+32 este mes</div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <!-- aqui es donde deben de aparecer las graficas -->
            <div class="stats-section">
                <div class="chart-container">
                    <h3>Ventas por Tipo de Producto</h3>
                    <div class="chart-placeholder">
                        <p>Gráfico de ventas: Nuevos vs Reacondicionados</p>
                    </div>
                </div>
                
                <div class="chart-container">
                    <h3>Estado de Inventario</h3>
                    <div class="chart-placeholder">
                        <p>Gráfico de disponibilidad</p>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="changeTab('pedidos')">Pedidos Recientes</div>
                <div class="tab" onclick="changeTab('productos')">Gestión de Productos</div>
                <div class="tab" onclick="changeTab('agregar')">Agregar Producto</div>
            </div>
            
            <!-- Tab Content: Pedidos recientes-->
            <div id="pedidos" class="tab-content active">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Pedidos Recientes</h3>
                        <button class="btn btn-primary">Ver Todos</button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-00256</td>
                                <td>Juan Pérez</td>
                                <td>iPhone 13</td>
                                <td><span class="status status-new">Nuevo</span></td>
                                <td>15/05/2023</td>
                                <td>$899.99</td>
                                <td><span class="status status-pending">Pendiente</span></td>
                                <td>
                                    <button class="btn btn-primary">Ver</button>
                                    <button class="btn btn-success">Procesar</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-00255</td>
                                <td>María García</td>
                                <td>Samsung Galaxy S21</td>
                                <td><span class="status status-refurbished">Reacondicionado</span></td>
                                <td>14/05/2023</td>
                                <td>$524.50</td>
                                <td><span class="status status-shipped">Enviado</span></td>
                                <td>
                                    <button class="btn btn-primary">Ver</button>
                                    <button class="btn btn-success">Seguimiento</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-00254</td>
                                <td>Carlos López</td>
                                <td>Google Pixel 6</td>
                                <td><span class="status status-refurbished">Reacondicionado</span></td>
                                <td>13/05/2023</td>
                                <td>$367.80</td>
                                <td><span class="status status-completed">Completado</span></td>
                                <td>
                                    <button class="btn btn-primary">Ver</button>
                                    <button class="btn btn-success">Factura</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-00253</td>
                                <td>Ana Rodríguez</td>
                                <td>iPhone 12 Pro</td>
                                <td><span class="status status-new">Nuevo</span></td>
                                <td>12/05/2023</td>
                                <td>$956.30</td>
                                <td><span class="status status-pending">Pendiente</span></td>
                                <td>
                                    <button class="btn btn-primary">Ver</button>
                                    <button class="btn btn-success">Procesar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab Content: Gestion de Productos -->
            <div id="productos" class="tab-content">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Gestión de Productos</h3>
                        <button class="btn btn-primary" onclick="changeTab('agregar')">Agregar Producto</button>
                    </div>
                    
                    <div class="product-cards">
                        <div class="product-card">
                            <div class="product-badge badge-new">Nuevo</div>
                            <div class="product-image">
                                <i class="fas fa-mobile"></i>
                            </div>
                            <div class="product-info">
                                <div class="product-title">iPhone 14 Pro Max</div>
                                <div class="product-details">
                                    <span>Apple</span>
                                    <span>256GB</span>
                                </div>
                                <div class="product-price">$1,199.99</div>
                                <div class="product-details">
                                    <span>Stock: 15</span>
                                    <span>Vendidos: 42</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-primary">Editar</button>
                                    <button class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-badge badge-refurbished">Reacondicionado</div>
                            <div class="product-image">
                                <i class="fas fa-mobile"></i>
                            </div>
                            <div class="product-info">
                                <div class="product-title">Samsung Galaxy S22 Ultra</div>
                                <div class="product-details">
                                    <span>Samsung</span>
                                    <span>128GB</span>
                                </div>
                                <div class="product-price">$749.99</div>
                                <div class="product-details">
                                    <span>Stock: 8</span>
                                    <span>Vendidos: 28</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-primary">Editar</button>
                                    <button class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-badge badge-new">Nuevo</div>
                            <div class="product-image">
                                <i class="fas fa-mobile"></i>
                            </div>
                            <div class="product-info">
                                <div class="product-title">Google Pixel 7 Pro</div>
                                <div class="product-details">
                                    <span>Google</span>
                                    <span>256GB</span>
                                </div>
                                <div class="product-price">$899.99</div>
                                <div class="product-details">
                                    <span>Stock: 12</span>
                                    <span>Vendidos: 18</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-primary">Editar</button>
                                    <button class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-badge badge-refurbished">Reacondicionado</div>
                            <div class="product-image">
                                <i class="fas fa-mobile"></i>
                            </div>
                            <div class="product-info">
                                <div class="product-title">OnePlus 10 Pro</div>
                                <div class="product-details">
                                    <span>OnePlus</span>
                                    <span>256GB</span>
                                </div>
                                <div class="product-price">$599.99</div>
                                <div class="product-details">
                                    <span>Stock: 5</span>
                                    <span>Vendidos: 22</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-primary">Editar</button>
                                    <button class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Agregar Producto -->
            <div id="agregar" class="tab-content">
                <div class="form-container">
                    <h3>Agregar Nuevo Producto</h3>
                    <form id="product-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-name">Modelo del Celular</label>
                                <input type="text" id="product-name" placeholder="Ej: iPhone 14 Pro Max">
                            </div>
                            <div class="form-group">
                                <label for="product-brand">Marca</label>
                                <select id="product-brand">
                                    <option value="">Seleccione una marca</option>
                                    <option value="apple">Apple</option>
                                    <option value="samsung">Samsung</option>
                                    <option value="google">Google</option>
                                    <option value="oneplus">OnePlus</option>
                                    <option value="xiaomi">Xiaomi</option>
                                    <option value="huawei">Huawei</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-type">Tipo de Producto</label>
                                <select id="product-type">
                                    <option value="">Seleccione tipo</option>
                                    <option value="new">Nuevo</option>
                                    <option value="refurbished">Reacondicionado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product-condition">Condición (solo reacondicionados)</label>
                                <select id="product-condition">
                                    <option value="">Seleccione condición</option>
                                    <option value="excellent">Excelente</option>
                                    <option value="good">Buena</option>
                                    <option value="fair">Regular</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-price">Precio</label>
                                <input type="number" id="product-price" placeholder="0.00" step="0.01">
                            </div>
                            <div class="form-group">
                                <label for="product-stock">Stock</label>
                                <input type="number" id="product-stock" placeholder="0">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product-storage">Almacenamiento</label>
                                <select id="product-storage">
                                    <option value="">Seleccione capacidad</option>
                                    <option value="64">64GB</option>
                                    <option value="128">128GB</option>
                                    <option value="256">256GB</option>
                                    <option value="512">512GB</option>
                                    <option value="1024">1TB</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product-color">Color</label>
                                <input type="text" id="product-color" placeholder="Ej: Negro, Plata, Azul">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-description">Descripción</label>
                            <textarea id="product-description" rows="4" placeholder="Descripción del producto..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-features">Características principales</label>
                            <textarea id="product-features" rows="3" placeholder="Características separadas por coma..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-image">Imagen del Producto</label>
                            <input type="file" id="product-image">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Agregar Producto</button>
                        <button type="button" class="btn btn-refurbished">Agregar como Reacondicionado</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cambiar entre pestañas
        function changeTab(tabName) {
            // Ocultar todos los contenidos de pestañas
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Mostrar la pestaña seleccionada
            document.getElementById(tabName).classList.add('active');
            
            // Actualizar pestañas activas
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            event.currentTarget.classList.add('active');
        }
        
        // Manejar envío del formulario
        document.getElementById('product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Producto agregado correctamente');
            this.reset();
        });
        
        // Mostrar/ocultar campo de condición según tipo de producto
        document.getElementById('product-type').addEventListener('change', function() {
            const conditionField = document.getElementById('product-condition');
            if (this.value === 'refurbished') {
                conditionField.disabled = false;
            } else {
                conditionField.disabled = true;
                conditionField.value = '';
            }
        });
    </script>
</body>
</html>