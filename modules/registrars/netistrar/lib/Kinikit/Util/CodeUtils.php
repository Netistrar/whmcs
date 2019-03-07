<?php

namespace Kinikit\Core\Util;


/**
 * Normalise property names (i.e. produce camel case representation)
 * Normally, this involves simply lower casing the first character, but sometimes if there are multiple 
 * capital letters involved it gets a little bit more complicated.
 * 
 * @author mark
 *
 */
class CodeUtils {
	
	private static $instance;
	
	// Only allow singleton behaviour
	private function __construct() {
	}
	
	/**
	 * Return the singleton instance.
	 * 
	 * @return CodeUtils
	 */
	public static function instance() {
		if (! CodeUtils::$instance) {
			CodeUtils::$instance = new CodeUtils ();
		}
		
		return CodeUtils::$instance;
	}
	
	/**
	 * Normalise the property name using the standard rules we might expect.
	 * 
	 * @param string $propertyName
	 */
	public function normalisePropertyName($propertyName) {
		
		if ((strlen ( $propertyName ) > 1) && ((ord ( $propertyName [1] ) >= ord ( "a" )) || is_numeric ( $propertyName [1] ))) {
			$propertyName = strtolower ( $propertyName [0] ) . substr ( $propertyName, 1 );
		}
		
		return $propertyName;
	}

}

?>