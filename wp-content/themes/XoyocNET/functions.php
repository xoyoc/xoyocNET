<?php
	// IMAGENES DESTACADAS
	add_theme_support("post-thumbnails");
	// MENU DE MI PAGINA WEB
	function register_my_menus(){
		register_nav_menus(
			array(
				'menu-header'=> __('Menu de encabezado'),
				'menu-footer'=> __('Menu de pie de pagina')
				)
		 );
	}
	add_action('init', 'register_my_menus');


