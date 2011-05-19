<?php
/**
 * User: jbrinley
 * Date: 5/18/11
 * Time: 1:46 PM
 */
 
class WP_Router_Utility {
	const PLUGIN_NAME = 'WP Router';
	const TEXT_DOMAIN = 'wp-router';
	const DEBUG = FALSE;
	const MIN_PHP_VERSION = '5.2';
	const MIN_WP_VERSION = '3.1';
	const VERSION = '0.1';
	const DB_VERSION = 1;
	const PLUGIN_INIT_HOOK = 'wp_router_init';
	const POST_TYPE = 'WP_Router';
	private static $rewrite_slug = 'WP_Router';
	private static $post_id = 0; // The ID of the post this plugin uses


	/**
	 * A wrapper around WP's __() to add the plugin's text domain
	 *
	 * @param string $string
	 * @return string|void
	 */
	public static function __( $string ) {
		return __($string, self::TEXT_DOMAIN);
	}

	/**
	 * A wrapper around WP's _e() to add the plugin's text domain
	 *
	 * @param string $string
	 * @return void
	 */
	public static function _e( $string ) {
		return _e($string, self::TEXT_DOMAIN);
	}

	/**
	 * @static
	 * @return string The system path to this plugin's directory, with no trailing slash
	 */
	public static function plugin_path() {
		return WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) );
	}

	/**
	 * @static
	 * @return string The url to this plugin's directory, with no trailing slash
	 */
	public static function plugin_url() {
		return WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) );
	}

	/**
	 * Check that the minimum PHP and WP versions are met
	 *
	 * @static
	 * @param string $php_version
	 * @param string $wp_version
	 * @return bool Whether the test passed
	 */
	public static function prerequisites_met( $php_version, $wp_version ) {
		$pass = TRUE;
		$pass = $pass && version_compare( $php_version, self::MIN_PHP_VERSION, '>=');
		$pass = $pass && version_compare( $wp_version, self::MIN_WP_VERSION, '>=');
		return $pass;
	}

	public static function failed_to_load_notices( $php_version = self::MIN_PHP_VERSION, $wp_version = self::MIN_WP_VERSION ) {
		printf( '<div class="error"><p>%s</p></div>', sprintf( self::__( '%1$s requires WordPress %2$s or higher and PHP %3$s or higher.' ), self::PLUGIN_NAME, $wp_version, $php_version ) );
	}

	public static function init() {
		self::register_post_type();
		do_action(self::PLUGIN_INIT_HOOK);
	}

	/**
	 * Register a post type to use when displaying pages
	 * @static
	 * @return void
	 */
	private static function register_post_type() {
		// a very quiet post type
		$args = array(
			'public' => FALSE,
			'show_ui' => FALSE,
			'exclude_from_search' => TRUE,
			'publicly_queryable' => TRUE,
			'show_in_menu' => FALSE,
			'show_in_nav_menus' => FALSE,
			'supports' => array('title'),
			'has_archive' => TRUE,
			'rewrite' => array(
				'slug' => self::$rewrite_slug,
				'with_front' => FALSE,
				'feeds' => FALSE,
				'pages' => FALSE,
			)
		);
		register_post_type(self::POST_TYPE, $args);
	}
}
