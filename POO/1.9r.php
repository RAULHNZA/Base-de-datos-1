<!DOCTYPE html>
<html lang="es">
    <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <meta http-equiv="X-UA-Compatible" content="ie=edge">
         <title>fecha4.bajas</title>
         <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet"> 
         <style>
            * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }
    
    
    body {
         min-height: 100vh;
         font-family: 'Open Sans', sans-serif;
         font-size: 20px;
        font-weight: 400;
         background-image: linear-gradient(-25deg, #f2f2f2 50%,rgb(74, 154, 139) 50%);
         display: flex;
         align-items: center;
    }
    
    .contenedor {
        background-color: #fff;
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
        padding: 40px;
        border-radius: 10px;
    }
    
    header {
        display: flex;
         align-items: center;
         justify-content: space-between;
         flex-wrap: wrap;
         margin-bottom: 80px;
    }
    
    .logo {
        font-size: 25px;
         font-weight: 600;
         color: #000;
        text-decoration: none;
    }
    
    .menu  {
        display: flex;
         justify-content: space-between;
         flex-wrap: wrap;
    }
    
    .menu a {
        font-size: 22px;
         border-bottom: 2px solid transparent;
         margin-left: 40px;
         color: #000;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .menu a:hover {
        border-bottom: 2px solid #f2c968;
    }
    
    main {
         display: flex;
         justify-content: flex-end;
         align-items: center;
    }
    
    main .contenedor-img {
         max-width: 60%;
         margin-right: 40px;
    }
    
   
    .btn-link {
         display: inline-block;
         padding: 10px 30px;
        border-radius: 100px;
         margin-right: 10px;
         text-decoration: none;
         background: #cac7c7;
    }
    
    .btn-link:hover,
    .btn-link.activo {
        color: #fff;
        background:rgb(74, 154, 89);
        
    }
    
   
         </style>
    </head>
    <body>
         <div class="contenedor">
              <header>
                   <a href="#" class="logo">Reporte de registros</a>
                   <nav class="menu">
                        <a href="1_menu.html">Inicio</a>
                        <!-- <a href="fecha.php">Volver</a> -->
                   </nav>
              </header>
        
<?php
// Conexión a la base de datos
$mysql = new mysqli("localhost", "root", "", "ejemplo8");

// Verificar conexión
if ($mysql->connect_error) {
    die("Error al conectar a la base de datos: " . $mysql->connect_error);
}

// Consulta de todos los registros
$resultado = $mysql->query("SELECT * FROM datos") or die($mysql->error);

// Encabezado HTML
// echo "<h1>Reporte de registros</h1>";

if ($resultado->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Terapia</th>
            <th>Fecha</th>
          </tr>";

    // Mostrar cada fila
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>
                <td>{$fila['id']}</td>
                <td>{$fila['nombre']}</td>
                <td>{$fila['correo']}</td>
                <td>{$fila['telefono']}</td>
                <td>{$fila['terapia']}</td>
                <td>{$fila['fecha']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No hay registros en la base de datos.";
}

// Cerrar conexión
$mysql->close();
?>
         </div>
    
    </body>
    </html>