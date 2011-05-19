<?php
/**
 * User: jbrinley
 * Date: 5/18/11
 * Time: 12:29 PM
 */
 
class WP_Router extends WP_Router_Utility {
	private static $routes = array();
	private static $query_vars = array();

	/****************************************************
	 * PUBLIC API
	 ****************************************************/

	public static function add_route( $id, array $rules ) {
		// TODO
	}

	public static function get_route( $id ) {
		// TODO
	}

	public static function edit_route( $id, array $changes ) {
		// TODO
	}

	public static function remove_route( $id ) {
		// TODO
	}

	/****************************************************
	 * PLUMBING
	 ****************************************************/

	public static function init() {
		add_action('init', array(get_class(), 'generate_routes'), 1000, 0);
		add_action('parse_request', array(get_class(), 'parse_request'), 10, 1);
		add_filter('rewrite_rules_array', array(get_class(), 'add_rewrite_rules'), 10, 1);
	}

	public static function generate_routes() {
		do_action('wp_router_generate_routes');
		do_action('wp_router_alter_routes');
	}

	public static function add_rewrite_rules( $rules ) {
		$new_rules = array();
		foreach ( self::$routes as $id => $route ) {
			$new_rules = array_merge($new_rules, $route->rewrite_rules());
		}
		return $new_rules + $rules;
	}

	public static function parse_request( WP $query ) {
		if ( !isset($query->query_vars[WP_Route::QUERY_VAR]) ) {
			return;
		}
		$route = $query->query_vars[WP_Route::QUERY_VAR];
		if ( !isset(self::$routes[$route]) || !is_a(self::$routes[$route], 'WP_Route') ) {
			return;
		}
		self::$routes[$route]->execute($query->query_vars);
	}
}
