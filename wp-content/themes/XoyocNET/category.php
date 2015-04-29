<?php 
get_header();
?>
<h2><?php bloginfo('name'); ?></h2>
<?php
include (TEMPLATEPATH . '/templates/the_loop.php');
get_footer();
?>