<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="post">

<input type="text" name="nombre" placeholder="nombre">
<input type="text" name="correo" placeholder="correo">
<input type="text" name="telefono" placeholder="telefono">
    
<input type= "submit" name="enviar">

</form>

    
</body>
</html>

<?php
//este es el chido
$servidor = "localhost";
$usuario = "root";
$clave = "";
$bd = "ejemplo1";

$conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

mysqli_query($conexion, "INSERT INTO datos (nombre, correo, telefono) values ('$_REQUEST[nombre]','$_REQUEST[correo]','$_REQUEST[telefono]')")
 
   or die("Problemas en el select <br>" . mysqli_error($conexion));
mysqli_close($conexion);
?>



