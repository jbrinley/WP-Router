=== WP Router ===
Contributors: jbrinley
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A69NZPKWGB6H2
Tags: URL mapping, callback functions
Requires at least: 3.1
Tested up to: 3.1
Stable tag: trunk

Provides a simple API for mapping requests to callback functions.

== Description ==

WordPress's rewrite rules and query variables provide a powerful system
for mapping URL strings to collections of posts. Every request is parsed
into query variables and turned into a SQL query via `$wp_query->query()`.

Sometimes, though, you don't want to display a list of posts. You just want
a URL to map to a callback function, with the output displayed in place of
posts in whatever theme you happen to be using.

That's where WP Router comes in. It handles all the messy bits of registering
post types, query variables, rewrite rules, etc., and lets you write code to
do what you want it to do. One function call is all it takes to map a
URL to your designated callback function and display the return value in the page.

Created by [Adelie Design](http://www.AdelieDesign.com)

== Installation ==

1. Download and unzip the plugin
1. Upload the `WP-Router` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You should see the sample page at http://example.org/wp_router/sample/. Apart from that, there is no public UI for this plugin. You will not see any changes unless the plugin's API is called by another active plugin.

== Usage ==

= Creating Routes =

* Your plugin should hook into the `wp_router_generate_routes` action. The callback should take one argument, a `WP_Router` object.
* Register a route and its callback using `WP_Router::add_route( $id, $args )`
	* `$id` is a unique string your plugin should use to identify the route
	* `$args` is an associative array, that sets the following properties for your route. Any omitted argument will use the default value.
		* `path` (required) - A regular expression to match against the request path. This corresponds to the array key you would use when creating rewrite rules for WordPress.
		* `query_vars` - An associative array, with the keys being query vars, and the values being explicit strings or integers corresponding to matches in the path regexp. Any query variables included here will be automatically registered.
		* `title` - The title of the page.
		* `title_callback` - A callback to use for dynamically generating the title. Defaults to `__()`. If `NULL`, the `title` argument will be used as-is. if `page_callback` or `access_callback` returns `FALSE`, `title_callback` will not be called.
		* `title_arguments` - An array of query variables whose values will be passed as arguments to `title_callback`. Defaults to the value of `title`. If an argument is not a registered query variable, it will be passed as-is.
		* `page_callback` (required) - A callback to use for dynamically generating the contents of the page. The callback should either echo or return the contents of the page (if both, the returned value will be appended to the echoed value). If `FALSE` is returned, nothing will be output, and control of the page contents will be handed back to WordPress. The callback will be called during the `parse_request` phase of WordPress's page load. If `access_callback` returns `FALSE`, `page_callback` will not be called.
		* `page_arguments` - An array of query variables whose values will be passed as arguments to `page_callback`. If an argument is not a registered query variable, it will be passed as-is.
		* `access_callback` - A callback to determine if the user has permission to access this page. If `access_arguments` is provided, default is `current_user_can`, otherwise default is `TRUE`. If the callback returns `FALSE`, anonymous users are redirected to the login page, authenticated users get a 403 error.
		* `access_arguments` - An array of query variables whose values will be passed as arguments to `access_callback`. If an argument is not a registered query variable, it will be passed as-is.
		* `template` - Reserved, but not yet implemented.

Example:
`$router->add_route('wp-router-sample', array(
	'path' => '^wp_router/(.*?)$',
	'query_vars' => array(
		'sample_argument' => 1,
	),
	'page_callback' => array(get_class(), 'sample_callback'),
	'page_arguments' => array('sample_argument'),
	'access_callback' => TRUE,
	'title' => 'WP Router Sample Page',
));`

= Editing Routes =

* You can hook into the `wp_router_alter_routes` action to modify routes created by other plugins. The callback should take one argument, a `WP_Router` object.

= Public API Functions =

Creating or changing routes should always occur in the context of the `wp_router_generate_routes` or `wp_router_alter_routes` actions, using the `WP_Router` object supplied to your callback function.

* `WP_Router::edit_route( string $id, array $changes )` - update each property given in `$changes` for the route with the given ID. Any properties not given in `$changes` will be left unaltered.
* `WP_Router::remove_route( string $id )` - delete the route with the given ID
* `WP_Router::get_route( string $id )` - get the `WP_Route` object for the given ID
* `WP_Route::get( string $property )` - get the value of the specified property for the `WP_Route` instance

== Changelog ==

= 0.1 =

*Initial version
