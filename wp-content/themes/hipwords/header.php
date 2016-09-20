<?php
/**
* Header
*
* @package hipwords
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-target=".navbar-fixed-top">
<div class="header-top">
	<div class="overlay">
		<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'hipwords' ); ?></a>
		<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php echo esc_url( home_url('/') ); ?>">
						<?php bloginfo('name'); ?>
					</a>
				</div>
				<?php
            wp_nav_menu( array(
                'menu'              => 'primary',
				'theme_location'    => 'primary',
				'depth'             => 2,
				'container'         => 'div',
				'container_class'   => 'collapse navbar-collapse navbar-right navbar-ex1-collapse',
				'container_id'      => 'bs-example-navbar-collapse-1',
				'menu_class'        => 'nav navbar-nav',
				'fallback_cb'       => 'hipwords_wp_bootstrap_navwalker::fallback',
				'walker'            => new hipwords_wp_bootstrap_navwalker())
				);
				?>
			</div>
		</nav>
		<!-- #site-navigation -->
		<header id="masthead" class="site-header container" role="banner">
			<div class="site-branding">
				<div class="col-md-7">
					<?php if (is_front_page()) { ?>
					<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
					<?php } ?>

					<?php if ((!is_home()) and !(is_archive())) { ?>

					<?php the_title( sprintf( '<h1 class="entry-title site-title">', esc_url( get_permalink() ) ), '</h1>' ); ?>
					<?php if ( 'post' == get_post_type() ) : ?>
					<div class="entry-meta">
						<?php the_time('l, F jS, Y') ?>
						<?php the_category(', ') ?>
					</div><!-- .entry-meta -->
					<?php endif; ?>
					<?php } ?>

					<?php if (is_archive()) { ?>
					<?php
		the_archive_title( '<h1 class="site-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' )
					;?>
					<?php } ?>
				</div>
				<div class="col-md-4 col-md-offset-1">
					<?php if (!dynamic_sidebar('header-widget')) : ?>
					<h1 class="widget-title"><?php _e('Header Widget', 'hipwords'); ?></h1>
					<div class="textwidget"><p><?php _e('This is header widget box. To edit go to Appearance > Widgets > Header Widget','hipword'); ?></p></div>
					<?php endif; //end of footer-widget-3 ?>
				</div>
			</div><!-- .site-branding -->
		</header><!-- #masthead -->
	</div>
</div>
<div id="page" class="container">
	<div class="col-md-7">