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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'biomedica2024' );

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
define( 'AUTH_KEY',         'Tcn>8y+G6+nAZ9_rzWmr d`SclmP{|X;&[=+)6,^7{1<C 8R2 ^5|G:2@}F(.%_f' );
define( 'SECURE_AUTH_KEY',  'R42%WRXEH,W#.%}wGvtTMXny{?64LfFOtE830xH,^UOZEgC]i(Td|~czCI[p]hh.' );
define( 'LOGGED_IN_KEY',    'F%T=Vz,y*gG)Qf}|KMHK]7n~k3zW[C6kej,astKO,O)Oep:[DiKcO*8#Ach,FvTz' );
define( 'NONCE_KEY',        'SV[?]J9!J$*,s}p{iEH@>fT!JF+=} zqCe|h()gC.ri;HiJM]y:38r05].y5eDM.' );
define( 'AUTH_SALT',        '7Irvnhu7MnPvj!c34>6e@iq;/f|;Xh9MR>i69u6&&Pxe^r,g>*f7naw9]v:;05rJ' );
define( 'SECURE_AUTH_SALT', 'PB>.=R{zj) R]zBqV3BO^OQ4BaW!~Kckw[.M!;-8#t94Pf1*,;~; V2p_vXbOwfD' );
define( 'LOGGED_IN_SALT',   't?g|C}i_vH;V8YJ}5Nong{VBKNi#7G>(bGQ3%0%A5L,3:&L/l#yJ;d/Sf2V9R?li' );
define( 'NONCE_SALT',       'eu@=9ruGR$P_vs^tF-!/Od@J{&_z!$lL{rWNRH2-_S3f>ZYIT;Dx@X|^6q#}7N9[' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'biomedica_';

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
