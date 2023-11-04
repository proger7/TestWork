<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'abelo' );

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
define( 'AUTH_KEY',         'kE39k<54wn0.:>Zo=r>Mt@M4L``S2mC/XM8zv`d!1l;jF</Vg=8Vor42AtJ.hea]' );
define( 'SECURE_AUTH_KEY',  'T4m!:yHQ?m[mm13>b_PgrW.Gc pQ#0X&4&5NJSknnXS?S+74+-5iB[HW>?.L%D|g' );
define( 'LOGGED_IN_KEY',    '=G$CQ#*F|J+tou=j&RN2)<n<^A2*GfUMxJ#wGt#@I<MKBoixJ5MwhHX6X~01L_*v' );
define( 'NONCE_KEY',        'Mg,YdV@0=T1z`o~yp;eA*+TO9$uL_.s<J)V[)!5d@!4A{q^>(wV.,I]#~h:;d2h<' );
define( 'AUTH_SALT',        'hmVFORg(&x2hYpeHc{br#ksy)-S|?7o}h>RglWh:M]~2{b8!IQ# TvhZx@PqUiul' );
define( 'SECURE_AUTH_SALT', 'G%j84lAWf&|V 7qoEcY%fW#D6z<NMKm!.U/g=.,9L jK-Gm,9c{$-s?0R`;z;GPq' );
define( 'LOGGED_IN_SALT',   ']ipv.v B2 7MXCsV3-.(ar[r[sJq)4+i~J(e3/TN4Yb~1gPn`I3nKM3T&JgOZ8T]' );
define( 'NONCE_SALT',       'XWI)i>Sv|5Z;2tYRN]$8!b_FJ(&KLW}r,+McPI*P-T>k^Tzqv49QwrdOGw}q9G*i' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ab_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
