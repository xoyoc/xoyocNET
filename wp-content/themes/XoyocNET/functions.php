<?php
	add_theme_support("post-thumbnails");
	$menus = array(
		"menu_principal"=>"Men� Principal",
		"menu_social"=>"Men� Social"
	);
	register_nav_menus($menus);
/*	function extracto_mas()
	{
		$enlace = "<a href='".get_permalink()."'><b> leer m�s...</b></a>";
		return $enlace;
	}
	add_filter("excerpt_more","extracto_mas");*/
?>