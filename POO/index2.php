<?php

    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $bd = "ejemplo1";

    $coneccion = mysqli_connect ($servidor, $usuario, $clave, $bd );

    if(isset($_POST['enviar'])){
    
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        
        $insertar = "INSERT INTO datos Values ('$nombre','$correo','$telefono','')";
        
        $coneccion = mysqli_query($coneccion,$insertar);
    }
?>


<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$bd = "ejemplo1";

$conexion = mysqli_connect($servidor, $usuario, $clave, $bd );

mysqli_query($conexion, "insert into datos (nombre, correo, telefono); 
values ('$_REQUEST[nombre]','$_REQUEST[correo]','$_REQUEST[telefono]'')")
 
   or die("Problemas en el select <br>" . mysqli_error($conexion));
   echo "La Informacion fue dada de alta";
mysqli_close($conexion);
?>


<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$bd = "ejemplo1";

$conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

mysqli_query($conexion, "insert into datos (nombre, correo, telefono) values ('$_REQUEST[nombre]','$_REQUEST[correo]','$_REQUEST[telefono]')")
 
   or die("Problemas en el select <br>" . mysqli_error($conexion));
   echo "El libro fue dado de alta";
mysqli_close($conexion);
?>

bajas2
<?php
  
  $mysql = new mysqli("localhost","root","","ejemplo");
  
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

bajas 3
<?php
  
  $mysql = new mysqli("localhost","root","","ejemplo");
  
  if($mysql->connect_error)
  
  die("Problemas con la conexion a la base de datos");

  $registro = $mysql->query("select id from datos where id='$_REQUEST[id]'") or die($mysql->error);

  
  
  //if($reg = $mysql->fetch_array()){
    if($reg = $registro->fetch_array()){

  
  $mysql->query("delete from datos where id='$_REQUEST[id]'") or die($mysql->error);

  echo"El autor con registro que se elimino es: ".$reg['id'];


  }else{

  echo'No existe un autor con este registro';  
  }

  $mysql->close();

?>