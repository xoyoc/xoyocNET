<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'database_name_here');

/** MySQL database username */
define('DB_USER', 'username_here');

/** MySQL database password */
define('DB_PASSWORD', 'password_here');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         'zI{CPQ&sz<C:z:*]B{ubGfT|K6#w6qUP[*biL#6bksmq[AP^RA>IF<{u^e-nN+hB');
define('SECURE_AUTH_KEY',  '#qy+_*Ht+3{abPH)+r|#gT|srp6=D<1Jl,XTs&?/N0m;qhwh>YCY:@@S;PK=Vy)$');
define('LOGGED_IN_KEY',    'Q1>9vpqW#6{Sd7{LPXo?7Q$90fEv53*-Z3rT,6%G6FZPet}Pck]9Uba48u5%f5RJ');
define('NONCE_KEY',        'nM-xV5_W`t:gP,33!q;A=!XwIk ){kcf>cGY:kD;+YJI|{`OR|gRB<c9maIh-()M');
define('AUTH_SALT',        'S+ch?~qEaf+gQ&#lr+--a/Q2s^[yEaY(`k7,r|Q5IqjEkH9KDk3%A.AYP&Pi.&S&');
define('SECURE_AUTH_SALT', '`oUsVnNlGcj<b gv*/A>m|;6&qn|Ue&(|%&$kMUwz?~kyXtC6~yGzFJOfn4*gNIT');
define('LOGGED_IN_SALT',   'u*hXUFh`:J(e!<S,|Y4rq]^/qL2@IOzWKgywLOG>;aX-y+UDwm;sl]:uqU%5EWiU');
define('NONCE_SALT',       'HT![W6Z}JWl>wgg$43yvL_M&$++yfg/R7T578{S|Wl5:1yhEtq$>Sg.{nK<3#oTS');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
