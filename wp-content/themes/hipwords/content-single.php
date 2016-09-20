<?php
/**
 * @package hipwords
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="entry-content">
	<p>
		<?php
			if ( has_post_thumbnail() ) {
			the_post_thumbnail();
		} ?>
	</p>
	<?php the_content(); ?>
	<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'hipwords' ),
	'after'  => '</div>',
	) );
	?>
</div><!-- .entry-content -->
<footer class="entry-footer">
	<?php hipwords_entry_footer(); ?>
</footer><!-- .entry-footer -->
</article><!-- #post-## -->
