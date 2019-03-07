<?php

namespace Kinikit\Core\Util\Serialisation;

/**
 * Interface implemented by specific object to format converters.  This enables the use of a generic service dispatcher
 * 
 * @author mark
 *
 */
interface ObjectToFormatConverter {
	
	/**
	 * Only required method.  Converts a PHP structure (primitives / arrays or objects) 
	 * into the required format.
	 * 
	 * @param mixed $object
	 */
	public function convert($object);
}

?>