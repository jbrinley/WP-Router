=== WP Router ===
Contributors: jbrinley
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A69NZPKWGB6H2
Tags: URL mapping, callback functions
Requires at least: 3.1
Tested up to: 3.1
Stable tag: 0.1

Provides a simple API for mapping requests to callback functions.

== Description ==

Provides a simple API for mapping requests to callback functions.

Created by [Adelie Design](http://www.AdelieDesign.com)

== Installation ==

1. Download and unzip the plugin
1. Upload the `WP-Router` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You should see the sample page at http://example.org/wp_router/sample/. Apart from that, there is no public UI for this plugin. You will not see any changes unless the plugin's API is called by another active plugin.

== Usage ==

* Your plugin should hook into the `wp_router_generate_routes` action. The callback should take one argument, a `WP_Router` object.
* Register a route and its callback using `WP_Router::add_route( $id, $args )`
** `$id` is a unique string your plugin should use to identify the route
** `$args` is an associative array, that sets the following properties for your route
*** `path` - A regular expression to match against the request path. This corresponds to the array key you would use when creating rewrite rules for WordPress.
*** `query_vars` - An associative array, with the keys being query vars, and the values being explicit strings or integers corresponding to matches in the path regexp. Any query variables included here will be automatically registered.
*** `title` - The title of the page.
*** `title_callback` - A callback to use for dynamically generating the title. Defaults to `__()`. If `NULL`, the `title` argument will be used as-is. if `page_callback` or `access_callback` returns `FALSE`, `title_callback` will not be called.
*** `title_arguments` - An array of query variables whose values will be passed as arguments to `title_callback`. Defaults to the value of `title`. If an argument is not a registered query variable, it will be passed as-is.
*** `page_callback` - A callback to use for dynamically generating the contents of the page. The callback should either echo or return the contents of the page (if both, the returned value will be appended to the echoed value). If `FALSE` is returned, nothing will be output, and control of the page contents will be handed back to WordPress. The callback will be called during the `parse_request` phase of WordPress's page load. If `access_callback` returns `FALSE`, `page_callback` will not be called.
*** `page_arguments` - An array of query variables whose values will be passed as arguments to `page_callback`. If an argument is not a registered query variable, it will be passed as-is.
*** `access_callback` - A callback to determine if the user has permission to access this page. If `access_arguments` is provided, default is `current_user_can`, otherwise default is `TRUE`. If the callback returns `FALSE`, anonymous users are redirected to the login page, authenticated users get a 403 error.
*** `access_arguments` - An array of query variables whose values will be passed as arguments to `access_callback`. If an argument is not a registered query variable, it will be passed as-is.
*** `template` - Not yet implemented.

== Changelog ==

= 0.1 =

*Initial version
