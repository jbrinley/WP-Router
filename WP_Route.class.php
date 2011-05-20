<?php
/**
 * User: jbrinley
 * Date: 5/18/11
 * Time: 12:34 PM
 */
 
class WP_Route extends WP_Router_Utility {
	const QUERY_VAR = 'WP_Route';
	
	private $id = '';
	private $path = '';
	private $query_vars = array();
	private $wp_rewrite = '';
	private $title = '';
	private $title_callback = '__';
	private $title_arguments = array('');
	private $page_callback = '';
	private $page_arguments = array();
	private $access_callback = TRUE;
	private $access_arguments = array();
	private $template = '';
	private $properties = array();

	/**
	 * @throws Exception
	 * @param string $id A unique string used to refer to this route
	 * @param array $properties An array of key/value pairs used to set
	 * the properties of the route. At a minimum, must include:
	 *  - path
	 *  - page_callback
	 */
	public function __construct( $id, array $properties ) {
		$this->set('id', $id);

		foreach ( array('path', 'page_callback') as $property ) {
			if ( !isset($properties[$property]) || !$properties[$property] ) {
				throw new Exception(self::__("Missing $property"));
			}
		}
		
		foreach ( $properties as $property => $value ) {
			$this->set($property, $value);
		}
		
		if ( $this->access_arguments && $properties['access_callback'] ) {
			$this->set('access_callback', 'current_user_can');
		}

	}

	/**
	 * Get the value of the the given property
	 *
	 * @throws Exception
	 * @param string $property
	 * @return mixed
	 */
	public function get( $property ) {
		if ( isset($this->$property) ) {
			return $this->$property;
		} elseif ( isset($this->properties[$property]) ) {
			return $this->properties[$property];
		} else {
			throw new Exception(self::__("Property not found: $property."));
		}
	}

	/**
	 * Set the value of the given property to $value
	 *
	 * @throws Exception
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */
	public function set( $property, $value ) {
		if ( in_array($property, array('id', 'path', 'page_callback')) && !$value ) {
			throw new Exception(self::__("Invalid value for $property. Value may not be empty."));
		}
		if ( in_array($property, array('query_vars', 'title_arguments', 'page_arguments', 'access_arguments')) && !is_array($value) ) {
			throw new Exception(self::__("Invalid value for $property: $value. Value must be an array."));
		}
		if ( isset($this->$property) ) {
			$this->$property = $value;
		} else {
			$this->properties[$property] = $value;
		}
	}

	/**
	 * Execute the callback function for this route.
	 *
	 * @param array $query_vars
	 * @return void
	 */
	public function execute( array $query_vars ) {
		// TODO
	}

	/**
	 * @return array WordPress rewrite rules that should point to this instance's callback
	 */
	public function rewrite_rules() {
		$this->generate_rewrite();
		return array(
			$this->path => $this->wp_rewrite,
		);
	}

	/**
	 * Generate the WP rewrite rule for this route
	 *
	 * @return void
	 */
	private function generate_rewrite() {
		$rule = "index.php?";
		$vars = array();
		foreach ( $this->query_vars as $var => $value ) {
			if ( is_int($value) ) {
				$vars[] = $var.'='.$this->preg_index($value);
			} else {
				$vars[] = $var.'='.$value;
			}
		}
		$vars[] = self::QUERY_VAR.'='.$this->id;
		$rule .= implode('&', $vars);
		$this->wp_rewrite = $rule;
	}

	/**
	 * Pass an integer through $wp_rewrite->preg_index()
	 *
	 * @param int $matches
	 * @return string
	 */
	protected function preg_index( $int ) {
		global $wp_rewrite;
		return $wp_rewrite->preg_index($int);
	}
}
