<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'meatandgreet');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         ' }:?,aLZgSBl+pE&NqDZ(cpxm.G+XALnDZ0b>qjOmx[fxi?4q:cCk.(~,1Q.>~rw');
define('SECURE_AUTH_KEY',  '.{(QFn#[8IVfzYIC[c&R [fno6:PFo?J%A`wA`^mB5bVR(bZ+<.6< wE84->>8)Y');
define('LOGGED_IN_KEY',    '0#,jRNG0}[u99Q%dx<G_.iGJu6&C))-<5Bn&cHHYb,#Y1$-^X(-ZLMY)91x|$LWL');
define('NONCE_KEY',        '%Umm(D[tk &&;.idYwN]qVY?KT)K ~SGm|7e_.-Oq^:V}+U}noZe8N _!59SvJ,V');
define('AUTH_SALT',        '.pG.JJ|t#*O5|osBM3@2RUTBU{GN0U-*QT_pNY;ZPTuMbw.wBnO-^6Rt#DcuVY2)');
define('SECURE_AUTH_SALT', '8+g%J1a.V(c?Q5o9kd{*n+tX|,37TN+*l$CY`fb7Jrw<e~]hoZIK,AUkig6$wWc<');
define('LOGGED_IN_SALT',   ',;%6Z13sUTWH5Ig)chr>9AF7L0ZC OR_uABz^UHf-*2V!%3b1e|V72a)sacQ2)Ki');
define('NONCE_SALT',       'VUuCN,E$`8m1ibSZGmO?%~T,>z]j;wLdH~679b[<f:9|Ek:Y6&CXI]dH-q5% @2a');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
