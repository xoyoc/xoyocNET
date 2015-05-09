<!DOCTYPE html>
<html lang="es-mx" />
<head>
	<meta charset="UTF-8" />
	<title><?php bloginfo("name"); ?></title>
	<meta name="description" content="<?php bloginfo("description"); ?>" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
	<!-- Descripcion de la web y el diseño resposivo -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo("template_url"); ?>/xoyoc.ico" />
	<link rel="apple-touch-icon" type="apple-touch-icon" href="<?php bloginfo("template_url"); ?>/apple-touch-xoyoc.png" />
	<link rel="author" type="text/plain" href="<?php bloginfo("template_url"); ?>/humans.txt" />
	<link rel="stylesheet" href="<?php bloginfo("template_url"); ?>/css/style.css" />
</head>
<body class="container">
	<header class="encabezado twelve.columns">
		<div class="menu-movil--boton">
			<a href="#" class="boton__barra"><span class="icon-three-bars"></span>Menu</a>
		</div>
		<div class="menu-movil centrado-flex">
			<a class="brillo" href="<?php bloginfo("home"); ?>"><img class="encabezado__logo--imagen" src="<?php bloginfo("template_url"); ?>/img/logotipo.png" alt="LOGOTIPO"><span></span></a>
			<?php 
				wp_nav_menu(
					array(
						'theme_location' => 'menu-header',
						'container' => 'nav',
						'container_class' => 'menu-movil__principal',
						'menu_class' => 'nav__list',
						'link_before' => '<span class="icon-code"> </span>'
						)
				 );	
			 ?>
		</div>
	</header>
	<main class="contenido twelve.columns">