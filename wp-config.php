<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'HrrG<P@Cl4q/d-_3_oK1Qwv533n4,~%*)cM.iKdRmR-EvTTQA],L)7uP{q./c6aB' );
define( 'SECURE_AUTH_KEY',  'R,5modo{_|*lPU$?c@5O~bp&wu^9S1+ls.zN;Xbe6&?X+E}9-zPX[DHksbJbsQgm' );
define( 'LOGGED_IN_KEY',    'OlT8*s]aIhaS f:o?Ek,Hp6#EF{_i@qTd 2Gvlj(0Xq^_BBOfq,!T`Ljm3T3QwE%' );
define( 'NONCE_KEY',        'W/vQoH _Y^(+r<?*p{_F5F&8_[bzvWK#;WTH#h=ZZwZ`uo6Cv)6g4~y.!p~DI(ic' );
define( 'AUTH_SALT',        'y#r&!Q9;?w~qh+A9g%OtUohq|TuNDP$*+juh,V*fdo=#}rrpV71?)+Zo]mg.CHO3' );
define( 'SECURE_AUTH_SALT', '[=IuvtU;K@J]W>ANun6}|8F;7?H9?ss3/ta}R>&8$l(%#/]@TsTS4L_~*?dcf}Z&' );
define( 'LOGGED_IN_SALT',   'Hy(>6v_*8aoDI2]AVgu(}/K@1J^0S(Ks%boQ 0A` C<15+!_q7XdCA>q@dRdwjSK' );
define( 'NONCE_SALT',       'SK5X@-X4Qc4==SbvPK<,,Qi vB~(7DEr(UcWZvJO]qgf(Y2ts{L:&@84wQ$l/x2Z' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
