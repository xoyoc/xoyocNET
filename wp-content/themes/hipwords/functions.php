<?php
/**
 * hipwords functions and definitions
 *
 * @package hipwords
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'hipwords_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function hipwords_setup() {

/*
 * Make theme available for translation.
 * Translations can be filed in the /languages/ directory.
 * If you're building a theme based on hipwords, use a find and replace
 * to change 'hipwords' to the name of your theme in all the template files
 */
load_theme_textdomain( 'hipwords', get_template_directory() . '/languages' );
// Add default posts and comments RSS feed links to head.
add_theme_support( 'automatic-feed-links' );

/*
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
* provide it for us.
*/
add_theme_support( 'title-tag' );

/*
* Enable support for Post Thumbnails on posts and pages.
*
* @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
*/
//add_theme_support( 'post-thumbnails' );

// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
'primary' => __( 'Primary Menu', 'hipwords' ),
) );

/*
* Switch default core markup for search form, comment form, and comments
* to output valid HTML5.
*/
add_theme_support( 'html5', array(
'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
) );

/*
* Enable support for Post Formats.
* See http://codex.wordpress.org/Post_Formats
*/
add_theme_support( 'post-formats', array(
'aside', 'image', 'video', 'quote', 'link',
) );

add_theme_support('post-thumbnails');
set_post_thumbnail_size(800, 400, true);

register_nav_menus( array(
'primary' => __( 'Primary Menu', 'hipwords' ),
) );

function hipwords_add_editor_styles() {
add_editor_style( '/style.css' );
}
add_action( 'init', 'hipwords_add_editor_styles' );

// Set up the WordPress core custom background feature.
add_theme_support( 'custom-background', apply_filters( 'hipwords_custom_background_args', array(
'default-color' => 'ffffff',
'default-image' => '',
) ) );
}
endif; // hipwords_setup
add_action( 'after_setup_theme', 'hipwords_setup' );

/**
* Register widget area.
*
* @link http://codex.wordpress.org/Function_Reference/register_sidebar
*/
function hipwords_widgets_init() {

register_sidebar( array(
'name'          => __( 'Header Right Block', 'hipwords' ),
'id'            => 'header-widget',
'description'   => '',
'before_title' => '<h1 class="widget-title">',
	'after_title' => '</h1>',
'before_widget' => '',
'after_widget' => ''
));

register_sidebar( array(
'name'          => __( 'Sidebar', 'hipwords' ),
'id'            => 'sidebar-1',
'description'   => '',
'before_widget' => '<aside id="%1$s" class="widget %2$s">',
	'after_widget'  => '</aside>',
'before_title'  => '<h1 class="widget-title">',
	'after_title'   => '</h1>',
) );

register_sidebar(array(
'name' => __('Footer Widget 1', 'hipwords'),
'description'   => '',
'id' => 'footer-widget-1',
'before_title' => '<h1 class="widget-title">',
	'after_title' => '</h1>',
'before_widget' => '',
'after_widget' => ''
));

register_sidebar(array(
'name' => __('Footer Widget 2', 'hipwords'),
'description'   => '',
'id' => 'footer-widget-2',
'before_title' => '<h4 class="widget-title">',
	'after_title' => '</h4>',
'before_widget' => '',
'after_widget' => ''
));

register_sidebar(array(
'name' => __('Footer Widget 3', 'hipwords'),
'description'   => '',
'id' => 'footer-widget-3',
'before_title' => '<h4 class="widget-title">',
	'after_title' => '</h4>',
'before_widget' => '',
'after_widget' => ''
));


}
add_action( 'widgets_init', 'hipwords_widgets_init' );

/**
* Enqueue scripts and styles.
*/

function hipwords_scripts() {

wp_enqueue_style('hipwords-bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', false, '3.2.2', 'all');
wp_enqueue_style( 'hipwords-style', get_stylesheet_uri() );

wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '1.0', true);
wp_enqueue_script( 'wordblog-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
wp_enqueue_script( 'hipwords-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
wp_enqueue_script( 'comment-reply' );
}
}
add_action( 'wp_enqueue_scripts', 'hipwords_scripts' );

/**
* Enqueue Google Fonts.
*/
function hipwords_custom_enqueue_google_font() {

$query_args = array(
'family' => 'Open+Sans:400,800,700,300,600'
);

wp_register_style( 'google-fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
wp_enqueue_style( 'google-fonts' );

}
add_action( 'wp_enqueue_scripts', 'hipwords_custom_enqueue_google_font' );

/**
* Implement the Custom Header feature.
*/
require get_template_directory() . '/inc/custom-header.php';

define( 'NO_HEADER_TEXT', true );

/**
* Custom template tags for this theme.
*/
require get_template_directory() . '/inc/template-tags.php';

/**
* Custom functions that act independently of the theme templates.
*/
require get_template_directory() . '/inc/extras.php';

/**
* Customizer additions.
*/
require get_template_directory() . '/inc/customizer.php';

/**
* Load Jetpack compatibility file.
*/
require get_template_directory() . '/inc/jetpack.php';


// Register Custom Navigation Walker
require_once('wp_bootstrap_navwalker.php');
