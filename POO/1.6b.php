<html>
<head>
   
    <title>Borrado de registros</title>

</head>
<body>
  

 <?php
  
        $mysql = new mysqli("localhost","root","","ejemplo8");
        
        if($mysql->connect_error)
        
        die("Problemas con la conexion a la base de datos");

        $registro = $mysql->query("select nombre from datos where nombre='$_REQUEST[nombre]'") or die($mysql->error);

        
        if($reg = $registro->fetch_array()){
        
        $mysql->query("delete from datos where nombre='$_REQUEST[nombre]'") or die($mysql->error);
 
        echo"El cliente con registro que se elimino es: ".$reg['nombre'];


        }else{

        echo'No existe un nombre con este registro';  
        }

        $mysql->close();

?>



<br><br>
<a href="1_menu.html">Regresar al menu</a><br><br>

    
</body>
</html>