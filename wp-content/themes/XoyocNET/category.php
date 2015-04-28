<?php 
get_header();
?>
<h1><?php bloginfo('name'); ?></h1>
<h2><?php bloginfo('description'); ?></h2>
<?php
include (TEMPLATEPATH . '/templates/the_loop.php');
get_footer();
?>