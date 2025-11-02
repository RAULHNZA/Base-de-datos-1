<html lang="es">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>RAUL</title>
     <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet"> 
     <style>
        * {
	padding: 0;
	margin: 0;
	box-sizing: border-box;
}
img {
	width: 100%;
     height: 100%;
     object-fit: contain;
}

body {
     min-height: 100vh;
     font-family: 'Open Sans', sans-serif;
     font-size: 20px;
	font-weight: 400;
     background-image: linear-gradient(-25deg, #f2f2f2 50%,rgb(74, 146, 154) 50%);
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
     color: rgb(7, 47, 246);
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
     color: rgb(0, 0, 0);
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

.contenedor-texto .titulo {
     font-size: 50px;
     font-weight: normal;
     margin-bottom: 40px;
}

.contenedor-texto .mascota {
     font-weight: bold;
     color:rgb(20, 21, 21);
     font-style: normal;
}

.contenedor-texto p {
     margin-bottom: 40px;
     line-height: 36px;
}

.btn-link {
     display: inline-block;
     padding: 10px 30px;
	border-radius: 100px;
     margin-right: 10px;
     text-decoration: none;
     background:rgb(242, 242, 242);
}

.btn-link:hover,
.btn-link.activo {
	color: #fff;
	background:rgb(74, 111, 154);
}

@media screen and (max-width: 915px) {
     main {
          flex-direction: column;
          text-align: center;
     }

     main .contenedor-img {
          margin-bottom: 40px;
     }
} 
     </style>
</head>
<body>
     <div class="contenedor">
          <header>
               <a href="#" class="logo">CITA AGENDADA</a>
               <nav class="menu">
                    <a href="1.1.html">Inicio</a>
                    
                    <!-- <a href="https://www.facebook.com/profile.php?id=100008441493976">Regresar</a> -->
               </nav>
          </header>

          <main>
               <div class="contenedor-img">
                    <!-- <img src="data:image/jpeg;base64,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" alt=""> -->
               </div>
               <div class="contenedor-texto">
					<!-- Forma #1 -->
					<h2 class="titulo"> <br>  <span class="typed"></span></h2>
					
					<!-- Forma #2 -->
						<!-- <h2 class="titulo">
							<span class="typed"></span>
						</h2> -->
					<!-- <div class="titulo" id="cadenas-texto">
						<p>Traé a HomePets <br> a tu <i class="mascota">Perro</i></p>
						<p>Traé a HomePets <br> a tu <i class="mascota">Gato</i></p>
						<p>Traé a HomePets <br> a tu <i class="mascota">Pez</i></p>
					</div> -->

                    
                        <!-- <p>
                            !HOLA¡ ME LLAMO "RAUL"<br>
                            Necesitamos que contestes unas preguntas para saber si eres apto para trabajar en mi <br>
                            en mi empresa. Puedes enviarnos mensaje en el boton "Contacto" para dudas.
                        </p> -->
                   
                    <!-- <a href="combinacion3.html" class="btn-link">CLICK PREGUNTAS</a> -->
               </div>
          </main>
     </div>

	 <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.11"></script>
     <script>
         var typed = new Typed('.typed', {
	strings: [
		'<i class="mascota">GRACIAS POR AGENDAR TU CITA, TE ESPERAMOS</i>',
        '<i class="mascota">GRACIAS POR AGENDAR TU CITA, TE ESPERAMOS</i>',
		'<i class="mascota">GRACIAS POR AGENDAR TU CITA, TE ESPERAMOS</i>',
		'<i class="mascota">GRACIAS POR AGENDAR TU CITA, TE ESPERAMOS</i>',

	],
	stringsElement: '#cadenas-texto', // ID del elemento que contiene cadenas de texto a mostrar.
	typeSpeed: 75, // Velocidad en mlisegundos para poner una letra,
	startDelay: 300, // Tiempo de retraso en iniciar la animacion. Aplica tambien cuando termina y vuelve a iniciar,
	backSpeed: 75, // Velocidad en milisegundos para borrrar una letra,
	smartBackspace: true, // Eliminar solamente las palabras que sean nuevas en una cadena de texto.
	shuffle: false, // Alterar el orden en el que escribe las palabras.
	backDelay: 1500, // Tiempo de espera despues de que termina de escribir una palabra.
	loop: true, // Repetir el array de strings
	loopCount: false, // Cantidad de veces a repetir el array.  false = infinite
	showCursor: true, // Mostrar cursor palpitanto
	cursorChar: '|', // Caracter para el cursor
	contentType: 'html', // 'html' o 'null' para texto sin formato
});
     </script>
</body>
</html>

<?php
//este es el chido
$servidor = "localhost";
$usuario = "root";
$clave = "";
$bd = "ejemplo8";

$conexion = mysqli_connect($servidor, $usuario, $clave, $bd);

mysqli_query($conexion, "INSERT INTO datos (nombre, correo, telefono, terapia, fecha) VALUES ('$_REQUEST[nombre]', '$_REQUEST[correo]', '$_REQUEST[telefono]', '$_REQUEST[terapia]', '$_REQUEST[fecha]')") 
or die("Problemas en el select <br>" . mysqli_error($conexion));
mysqli_close($conexion);

?>

