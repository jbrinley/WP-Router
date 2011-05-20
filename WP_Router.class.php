<?php
/**
 * User: jbrinley
 * Date: 5/18/11
 * Time: 12:29 PM
 */
 
class WP_Router extends WP_Router_Utility {
	const ROUTE_CACHE_OPTION = 'WP_Router_route_hash';
	private $routes = array();
	private $query_vars = array();

	/**
	 * @var WP_Router The one instance of this singleton class
	 */
	private static $instance;

	/**
	 * Exist!
	 *
	 * @static
	 * @return void
	 */
	public static function init() {
		self::$instance = self::get_instance();
	}

	/****************************************************
	 * PUBLIC API
	 ****************************************************/

	/**
	 * Add a new route
	 *
	 * @param string $id
	 * @param array $properties
	 * @return null|WP_Route
	 */
	public function add_route( $id, array $properties ) {
		if ( $route = $this->create_route($id, $properties) ) {
			$this->routes[$id] = $route;
		}
		return $route;
	}

	/**
	 * Get a previously registered route
	 *
	 * @param string $id
	 * @return null|WP_Route
	 */
	public function get_route( $id ) {
		if ( isset($this->routes[$id]) ) {
			return $this->routes[$id];
		} else {
			return NULL;
		}
	}

	/**
	 * Update each property included in $changes for the given route
	 *
	 * @param string $id
	 * @param array $changes
	 * @return void
	 */
	public function edit_route( $id, array $changes ) {
		if ( !isset($this->routes[$id]) ) {
			return;
		}
		foreach ( $changes as $key => $value ) {
			if ( $key != 'id' ) {
				try {
					$this->routes[$id]->set($key, $value);
				} catch ( Exception $e ) {
					// Error setting the property. Failing silently
				}
			}
		}
	}

	/**
	 * Get rid of the route with the given $id
	 *
	 * @param string $id
	 * @return void
	 */
	public function remove_route( $id ) {
		if ( isset($this->routes[$id]) ) {
			unset($this->routes[$id]);
		}
	}

	/****************************************************
	 * PLUMBING
	 ****************************************************/

	/*
	 * Singleton Design Pattern
	 * ------------------------------------------------- */

	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Hook into WordPress
	 */
	private function __construct() {
		add_action('init', array($this, 'generate_routes'), 1000, 0);
		add_action('parse_request', array($this, 'parse_request'), 10, 1);
		add_filter('rewrite_rules_array', array($this, 'add_rewrite_rules'), 10, 1);
	}

	private function __clone() {
		// cannot be cloned
		trigger_error(__CLASS__.' may not be cloned', E_USER_ERROR);
	}

	private function __sleep() {
		// cannot be serialized
		trigger_error(__CLASS__.' may not be serialized', E_USER_ERROR);
	}

	/**
	 * WordPress hook callbacks
	 * ------------------------------------------------- */

 	/**
	 * Announce to other plugins that it's time to create rules
	 * Action: init
	 *
	 * @uses do_action() Calls 'wp_router_generate_routes'
	 * @uses do_action() Calls 'wp_router_alter_routes'
	 * @return void
	 */
	public function generate_routes() {
		do_action('wp_router_generate_routes');
		do_action('wp_router_alter_routes');
		$rules = $this->rewrite_rules();
		if ( $this->hash($rules) != get_option(self::ROUTE_CACHE_OPTION) ) {
			$this->flush_rewrite_rules();
		}
	}

	/**
	 * Update WordPress's rewrite rules array with registered routes
	 * Filter: rewrite_rules_array
	 *
	 * @param array $rules
	 * @return array
	 */
	public function add_rewrite_rules( $rules ) {
		$new_rules = $this->rewrite_rules();
		update_option(self::ROUTE_CACHE_OPTION, $this->hash($new_rules));
		return $new_rules + $rules;
	}

	/**
	 * If a callback is in order, call it.
	 * Action: parse_request
	 *
	 * @param WP $query
	 * @return
	 */
	public function parse_request( WP $query ) {
		if ( $id = $this->identify_route($query) ) {
			$this->routes[$id]->execute($query->query_vars);
		}
	}

	/**
	 * Identify the route based on the request's query variables
	 *
	 * @param WP|WP_Query $query
	 * @return string|NULL
	 */
	private function identify_route( $query ) {
		if ( !isset($query->query_vars[WP_Route::QUERY_VAR]) ) {
			return NULL;
		}
		$id = $query->query_vars[WP_Route::QUERY_VAR];
		if ( !isset($this->routes[$id]) || !is_a($this->routes[$id], 'WP_Route') ) {
			return NULL;
		}
		return $id;
	}

	/**
	 * Create a new WP_Route with the given id and properties
	 *
	 * protected so it can be mocked in testing
	 *
	 * @param string $id
	 * @param array $properties
	 * @return null|WP_Route
	 */
	protected function create_route( $id, array $properties ) {
		try {
			$route = new WP_Route($id, $properties);
		} catch ( Exception $e ) {
			// invalid route $properties
			return NULL;
		}
		return $route;
	}

	/**
	 * Get the array of rewrite rules from all registered routes
	 *
	 * @return array
	 */
	private function rewrite_rules() {
		$rules = array();
		foreach ( $this->routes as $id => $route ) {
			$rules = array_merge($rules, $route->rewrite_rules());
		}
		return $rules;
	}

	/**
	 * Create a hash of the registered rewrite rules
	 *
	 * @param array $rules
	 * @return string
	 */
	private function hash( $rules ) {
		return md5(serialize($rules));
	}

	/**
	 * Tell WordPress to flush its rewrite rules
	 * 
	 * @return void
	 */
	private function flush_rewrite_rules() {
		flush_rewrite_rules();
	}
}
