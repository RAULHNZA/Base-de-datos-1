<?php

    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $bd = "ejemplo1";

    $coneccion = mysqli_connect ($servidor, $usuario, $clave, $bd )

?>
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

  if(isset($_POST['enviar'])){
      
      $nombre = $_POST['nombre'];
      $correo = $_POST['correo'];
      $telefono = $_POST['telefono'];
      
      $insertar = "INSERT INTO datos Values ('$nombre','$correo','$telefono','')";
      
      $coneccion = mysqli_query($coneccion,$insertar);
  }
?>
