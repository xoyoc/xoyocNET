<?php
	// IMAGENES DESTACADAS
	add_theme_support("post-thumbnails");
	// MENU DE MI PAGINA WEB
	function register_my_menu(){
		register_nav_menus(
			array(
					'menu-header'=> ___('Menu de encabezado'),
					'menu-footer'=> ___('Menu de pie de pagina')
				)
		 );
	}
	add_action('init', 'register_my_menu');


