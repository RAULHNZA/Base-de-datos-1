<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Altas</title>
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
            background-image: linear-gradient(-25deg, #f2f2f2 50%, #5b4a9a 50%);
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

        .menu {
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
            background: #5b4a9a;

        }
    </style>
</head>

<body>
    <div class="contenedor">
        <header>
            <a href="#" class="logo"></a>
            <nav class="menu">
                <a href="1.2inicio.php">Inicio</a>
                <a href="1.3altas.php">Registrar otra fecha</a>
            </nav>
        </header>


        <?php


        $db_host = "localhost";
        $db_name = "id16185755_bitacora";
        $db_login = "id16185755_root";
        $db_pswd = "*Zp7HdR0SSgRa&H9";

        $conexion = mysqli_connect($db_host, $db_login, $db_pswd, $db_name);

        mysqli_query($conexion, "insert into empleo (nombre, tel, carrera, ganancia) values ('$_REQUEST[nombre]','$_REQUEST[tel]',
'$_REQUEST[carrera]','$_REQUEST[ganancia]')")

            or die("Problemas en el select <br>" . mysqli_error($conexion));
        echo "La Informacion fue dada de alta";
        mysqli_close($conexion);
        ?>


</body>

</html>