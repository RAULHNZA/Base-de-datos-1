<html>
<head>
    <title>Borrado de registros</title>
</head>
<body>
<body>
   
    <Borrado de Registros>

    <form action="2.2b.php"method="post">

        Ingrese el nombre a eliminar:

        <input type="text" name="nombre" required>

        <br>

        <input type="submit" value="borrar">
     
    </form>

</body>
</html>


<?php
  
  $mysql = new mysqli("localhost","root","","ejemplo8");
  
  if($mysql->connect_error)
  
  die("Problemas con la conexion a la base de datos");

  $registro = $mysql->query("select nombre from datos where nombre='$_REQUEST[nombre]'") or die($mysql->error);

  
  
  //if($reg = $mysql->fetch_array()){
    if($reg = $registro->fetch_array()){

  
  $mysql->query("delete from datos where nombre='$_REQUEST[nombre]'") or die($mysql->error);

  echo"El autor con registro que se elimino es: ".$reg['nombre'];


  }else{

  echo'No existe un autor con este registro';  
  }

  $mysql->close();

?>
