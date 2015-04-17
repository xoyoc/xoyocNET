<!DOCTYPE html>
<html lang="es-mx" />
<head>
	<meta charset="UTF-8" />
	<title>Xoyoc.Net</title>
	<meta name="description" content="Front-end developer es mi protección realizo proyectos a empresas ubicadas en el puerto de Lázaro Cárdenas, Michoacán, trato de ser FreeLancer en mis tiempos libres ya que trabajo para la asociación de agentes aduanales en donde estoy a cargo del departamento de IT." />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
	<!-- Descripcion de la web y el diseño resposivo -->
	<link rel="shortcut icon" type="image/x-icon" href="xoyoc.ico" />
	<link rel="apple-touch-icon" type="apple-touch-icon" href="apple-touch-xoyoc.png" />
	<link rel="author" type="text/plain" href="humans.txt" />
	<link rel="stylesheet" href="css/normalize.css" />
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>
	<input type="checkbox" id="panel-boton" />
	<label id="panel-etiqueta" for="panel-boton" class="animacion"></label>
	<header id="panel" class="animacion">
		<figure>
			<img src="img/logotipo.png" alt="LOGOTIPO">
		</figure>
		<nav>
			<ul>
				<li><a class="animacion" href="#diseño_web">Front-End</a></li>
				<li><a class="animacion" href="#portafolio">Portafolio</a></li>
				<li><a class="animacion" href="#blog">Blog</a></li>
				<li><a class="animacion" href="#contacto">Contacto</a></li>
			</ul>
		</nav>
	</header>
	<main id="contenido">
		<section >
			<h2>Mi lugar</h2>
			<figure>
				<img src="img/Life-Front-End-Developer-Feature_1290x688_KL.jpg" alt="responsive_design">
			</figure>
			<p>
				Hola, mi nombre es <a href="http://xoyoc.net/wp-content/themes/XoyocNET/yo.html">Antonio Xoyoc Becerra Farias</a>, soy un developer Front-end mexicano nací en Lázaro Cárdenas, Michoacán, el puerto más importante de México, es la puerta así el medio oriente, mi más grande sueño es tener mi empresa de diseño y así poder dejar más huella de mi existencia en esta aventura que es la vida.
			</p>
			<p>
				Los proyectos siempre los emprendo con mucho entusiasmo y dedicación, me gusta mucho lo que hago pongo todo el corazón en ello. 
			</p>
		</nav>
	</main>
	<footer id="contacto">
		<form action="">
			<label>
            	<input name="nombre" id="c_name" type="text" class="c_input" placeholder="Nombre..."/>
            	<input name="email"  id="c_mail" type="email" class="c_input" placeholder="Email..."/>
            	<textarea name="mensaje"  id="c_msg" placeholder="Mensaje..."></textarea>
            	<input name="send" onclick="cargaSendMail()" type="button" value="Enviar" class="btn-b" id="c_enviar"></input>
			</label>
		</form>
	</footer>
</body>
</html>