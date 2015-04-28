<?php 
get_header();
?>
<h1><?php bloginfo('name'); ?></h1>
<?php
include (TEMPLATEPATH . '/templates/the_loop.php');
get_footer();
?>