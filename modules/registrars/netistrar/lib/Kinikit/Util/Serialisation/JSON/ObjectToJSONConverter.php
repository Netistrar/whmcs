<?php

namespace Kinikit\Core\Util\Serialisation\JSON;

use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Exception\SerialisableException;
use Kinikit\Core\Object\AssociativeArray;
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Util\Logging\Logger;
use Kinikit\Core\Util\Serialisation\ObjectToFormatConverter;

/**
 * Converter which recursively converts Objects / Values to JSON.
 *
 */
class ObjectToJSONConverter implements ObjectToFormatConverter {

    /**
     * Convert a passed object to JSON notation
     *
     */
    public function convert($object, $ignoreNonSerialisables = false, $prettyPrint = false) {

        // Normalise the input to array form.
        $object = $this->normaliseToArrayForm($object, $ignoreNonSerialisables);

        // Then simply encode using PHP JSON libraries.
        return json_encode($object, $prettyPrint ? JSON_PRETTY_PRINT : null);
    }

    /**
     * Normalise an object / array into standard array form for JSON conversion.
     *
     * @param mixed $object
     */
    private function normaliseToArrayForm($object, $ignoreNonSerialisables = false) {

        // If object input, deal with it now.
        if (is_object($object)) {

            $className = get_class($object);

            // Bail out if attempt to convert a none serialisable.
            if (!($object instanceof SerialisableObject || $object instanceof SerialisableException)) {
                if ($ignoreNonSerialisables) {
                    return null;
                } else {
                    throw new ClassNotSerialisableException ($className);
                }
            }

            // Initialise the return array with the className parameter as a minimum
            $returnValue = array();

            // Get all members from the object
            $allMemberMap = $object->__getSerialisablePropertyMap();

            // Loop through all members in the all member map, create
            foreach ($allMemberMap as $memberName => $memberValue) {

                // Camel case the name if not an associative array
                if (!$object instanceof AssociativeArray)
                    $modifiedMemberName = strtolower($memberName [0]) . substr($memberName, 1);
                else $modifiedMemberName = $memberName;

                $returnValue [$modifiedMemberName] = $this->normaliseToArrayForm($memberValue, $ignoreNonSerialisables);
            }

        } // If array input, deal with it now.
        else if (is_array($object)) {
            $returnValue = array();
            foreach ($object as $key => $item) {
                $returnValue [$key] = $this->normaliseToArrayForm($item, $ignoreNonSerialisables);
            }

        } // Otherwise leave the input intact
        else if (is_bool($object) || is_numeric($object)) {
            $returnValue = $object;
        } else {
            $returnValue = $object;
        }

        return $returnValue;

    }

}

?>