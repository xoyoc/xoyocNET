<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package hipwords
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

</div>

<div class="col-md-4  col-md-offset-1">
<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary -->
</div>

</div>