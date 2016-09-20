<?php
/**
 * Custom header
 */

function hipword_custom_header_setup() {
	$args = array(
		// Text color and image (empty to use none).
		'default-text-color'     => 'fff',
'default-image'          => '%s/img/header-top.jpg',

// Set height and width, with a maximum value for the width.
'height'                 => 500,
'width'                  => 1920,

// Callbacks for styling the header and the admin preview.
'wp-head-callback'       => 'hipword_header_style',
'admin-head-callback'    => 'hipword_admin_header_style',
'admin-preview-callback' => 'hipword_admin_header_image',
);

add_theme_support( 'custom-header', $args );

/*
* Default custom headers packaged with the theme.
* %s is a placeholder for the theme template directory URI.
*/
register_default_headers( array(
'Deafult' => array(
'url'           => '%s/img/header-top.jpg',
'thumbnail_url' => '%s/img/header-top.jpg',
'description'   => _x( 'Deafult', 'header image description', 'hipword' )
),

'Deafult2' => array(
'url'           => '%s/img/header-top2.jpg',
'thumbnail_url' => '%s/img/header-top2.jpg',
'description'   => _x( 'Deafult2', 'header image description', 'hipword' )
),

'Deafult3' => array(
'url'           => '%s/img/header-top3.jpg',
'thumbnail_url' => '%s/img/header-top3.jpg',
'description'   => _x( 'Deafult3', 'header image description', 'hipword' )
),

'Deafult4' => array(
'url'           => '%s/img/header-top4.jpg',
'thumbnail_url' => '%s/img/header-top4.jpg',
'description'   => _x( 'Deafult4', 'header image description', 'hipword' )
),

'Deafult5' => array(
'url'           => '%s/img/header-top5.jpg',
'thumbnail_url' => '%s/img/header-top5.jpg',
'description'   => _x( 'Deafult5', 'header image description', 'hipword' )
),


) );
}
add_action( 'after_setup_theme', 'hipword_custom_header_setup', 11 );
function hipword_header_style() {
$header_image = get_header_image();
$text_color   = get_header_textcolor();
// If no custom options for text are set, let's bail.
if ( empty( $header_image ) && $text_color == get_theme_support( 'custom-header', 'default-text-color' ) )
return;

// If we get this far, we have custom styles.
?>
<style type="text/css" id="hipword-header-css">
	<?php
	if ( ! empty( $header_image ) ) :
	?>
	.header-top {
		background: url(<?php header_image(); ?>);
		background-position: 50% 0;
		background-repeat: no-repeat;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		background-size: cover;
		-o-background-size: cover;
	}?>
	<?php endif; ?>
</style>
<?php
}
/**
 * Style the header image displayed on the Appearance > Header admin panel.
*/
function hipword_admin_header_style() {
$header_image = get_header_image();
?>
<style type="text/css" id="hipword-admin-header-css">
	.appearance_page_custom-header #headimg {
		border: none;
		-webkit-box-sizing: border-box;
		-moz-box-sizing:    border-box;
		box-sizing:         border-box;
	<?php
	  if ( ! empty( $header_image ) ) {
		echo 'background: url(' . esc_url( $header_image ) . ') no-repeat scroll top; background-size: 1600px auto;';
	} ?>
	padding: 0 20px;
	}
	#headimg .home-link {
		-webkit-box-sizing: border-box;
		-moz-box-sizing:    border-box;
		box-sizing:         border-box;
		margin: 0 auto;
		max-width: 1040px;
	<?php
	  if ( ! empty( $header_image ) || display_header_text() ) {
		echo 'min-height: 230px;';
	} ?>
	width: 100%;
	}
	.default-header img {
		max-width: 230px;
		width: auto;
	}
</style>
<?php
}
/**
* Output markup to be displayed on the Appearance > Header admin panel.
* This callback overrides the default markup displayed there.
*/
function hipword_admin_header_image() {
?>
<div id="headimg" style="background: url(<?php header_image(); ?>) no-repeat scroll top; background-size: 1600px auto;">
	<?php $style = ' style="color:#' . get_header_textcolor() . ';"'; ?>
	<div class="home-link">
		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="#" tabindex="-1"><?php bloginfo( 'name' ); ?></a></h1>
		<h2 id="desc" class="displaying-header-text"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></h2>
	</div>
</div>
<?php }