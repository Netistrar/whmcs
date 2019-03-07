<?php

namespace Kinikit\Core\Util\Serialisation\JSON;

use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Util\ClassUtils;
use Kinikit\Core\Util\Logging\Logger;
use Kinikit\Core\Util\SerialisableArrayUtils;
use Kinikit\Core\Util\Serialisation\FormatToObjectConverter;

class JSONToObjectConverter implements FormatToObjectConverter {

    /**
     * Convert a json string into objects.  If the mapToClass member is passed
     * the converter will attempt to map the result to an instance of that class type or array.
     *
     * @param string $jsonString
     * @param string $mapToClass
     */
    public function convert($jsonString, $mapToClass = null) {

        // Decode the string using PHP JSON Decode routine
        $converted = json_decode($jsonString, true);

        if ($mapToClass) {
            $converted = SerialisableArrayUtils::convertArrayToSerialisableObjects($converted, $mapToClass);
        }

        // Now convert to objects and return
        return $converted;
    }


}

?>