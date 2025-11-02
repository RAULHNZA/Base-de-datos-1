<?php
// Conexión a la base de datos
$mysql = new mysqli("localhost", "root", "", "ejemplo8");

// Verificar conexión
if ($mysql->connect_error) {
    die("Problemas con la conexión a la base de datos: " . $mysql->connect_error);
}

// Verificar que todos los campos requeridos estén presentes
if (
    isset($_POST['nombre_original']) &&
    isset($_POST['nombre']) &&
    isset($_POST['correo']) &&
    isset($_POST['telefono']) &&
    isset($_POST['terapia']) &&
    isset($_POST['fecha'])
) {
    // Escapar datos para evitar inyección SQL
    $nombre_original = $mysql->real_escape_string($_POST['nombre_original']);
    $nombre = $mysql->real_escape_string($_POST['nombre']);
    $correo = $mysql->real_escape_string($_POST['correo']);
    $telefono = $mysql->real_escape_string($_POST['telefono']);
    $terapia = $mysql->real_escape_string($_POST['terapia']);
    $fecha = $mysql->real_escape_string($_POST['fecha']);

    // Verificar que el registro exista
    $resultado = $mysql->query("SELECT * FROM datos WHERE nombre='$nombre_original'") or die($mysql->error);

    if ($resultado->num_rows > 0) {
        // Actualizar datos
        $mysql->query("UPDATE datos SET 
            nombre='$nombre',
            correo='$correo',
            telefono='$telefono',
            terapia='$terapia',
            fecha='$fecha'
            WHERE nombre='$nombre_original'") or die($mysql->error);

        echo "Registro actualizado correctamente.";
    } else {
        echo "No se encontró ningún registro con el nombre '$nombre_original'.";
    }
} else {
    echo "Faltan datos del formulario.";
}

// Cerrar conexión
$mysql->close();
?>
