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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_MEMORY_LIMIT', '256M' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'yMn2PqGWDOhd21Rxaq6lCZmNBbqy1xoN0MEqbYgCQ3Baz6Ehd++QMHhxg7MJ+yYR7llJEbwQhk1s9m6wziKUXA==');
define('SECURE_AUTH_KEY',  'mc6G9UGUVc8Ja4sywKXIzxZiL8Ej1XUaRjvqvssA2kHucbO7Uq+f0fQXOG3P1zY7j8BhfqAg+tdfrrkyekvbMQ==');
define('LOGGED_IN_KEY',    'p5H95FgY+JQDTD65IBTsFc1NJTvhriDsQPLQ+y0KaIGSjGz92yYgg8xr9lDJHJHBbQFheN8gtfW4LrpzsaBlVQ==');
define('NONCE_KEY',        'cVdia354B0bHmosdbcm9scx5O5eWQr+riiAu7yAiJuj7IvtLxHbqXySXl5P2tx4toOSjJkV4FmItqLxcNneKtw==');
define('AUTH_SALT',        'NbBEW/26yKcz5ViCF2QHTEYOtPOPNIOrQV4ZMlSLogha0MDeV4IghyVLMJaRxhC8YSbG6ItX6Rcto5E04k7oFQ==');
define('SECURE_AUTH_SALT', 'ewjeo0hyZrIrEMzdU+atjc/p1FBVBcZUSGWLofSo/9wZYYpimnRbqKjOI4N/URYNN2a8dyeb3E3XuoC9dbcy/w==');
define('LOGGED_IN_SALT',   'FNz5Nc0Qb6beJpg07wRcgrfADsG6QuSO5utUo7uzqYKVl1OcjV2jl+BtB7H35uTTtGjx9aN32K7pclrhZYUMPA==');
define('NONCE_SALT',       '/DAhXL1nzF6qKfbPoipQ9Q84CYmQWbLJBXm3lcBP5azubykVzky/kbURlOMYqoPllRLJ0RomxADA5JK+pUlDUg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
