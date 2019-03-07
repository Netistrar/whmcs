<?php

namespace Kinikit\Core\Object;

use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Exception\PropertyNotReadableException;
use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Util\CodeUtils;
use Kinikit\Core\Util\ObjectUtils;
use Kinikit\Core\Util\SerialisableArrayUtils;
use Kinikit\Core\Util\Serialisation\Serialisable;
use Kinikit\Core\Validation\Validator;

/**
 * Base serialisable object class.  This is the base class for any business objects which participate in any of the core OOA persistence
 * libraries as well as data transfer objects used by the transport libraries.
 *
 * @author mark
 * @package ooacore,common
 */
class SerialisableObject {

    // Use the serialisable trait.
    use Serialisable;


    /**
     * Default validate method.  This uses the Validator class to validate via annotation based markup.
     *
     * If any errors occur this returns an array indexed by field name where each entry is an array of
     * FieldValidationError objects indexed by validatorKey.
     *
     * @return array
     */
    public function validate() {
        return Validator::instance()->validateObject($this);
    }

    /**
     * Bind an array of items to this object.  Designed for form binding where fields are supplied as a flat array
     * with string keys using . or [] notation.
     *
     * @param $bindObject
     */
    public function bind($bindObject) {
        // Loop over object and set property value
        foreach ($bindObject as $key => $value) {
            ObjectUtils::setNestedObjectProperty($value, $this, $key);
        }
    }


    /**
     * Get a clone of an object
     */
    public function __clone() {
        return unserialize(serialize($this));
    }


    /**
     * Convert to array
     *
     * @return array
     */
    public function __toArray() {
        $array = array();
        foreach ($this->__getSerialisablePropertyMap() as $key => $value) {
            if ($value instanceof SerialisableObject) {
                $array[$key] = $value->__toArray();
            } else if (is_array($value)) {
                $array[$key] = SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

}

?>