<?php

namespace Kinikit\Core\Exception;

/**
 * Exception raised if an attempt is made to read a property which does not exist.
 * 
 * @author mark
 *
 */
class PropertyNotWritableException extends \Exception {
	
	public function __construct($className, $propertyName) {
		parent::__construct ( "An attempt was made to write to the property '" . $propertyName . "' on the class '" . $className . "' Which has no public / protected member or set method." );
	}

}

?>