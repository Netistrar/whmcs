<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 17:04
 */

namespace Kinikit\Core\Util;


use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Object\AssociativeArray;
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Util\Annotation\ClassAnnotationParser;
use Kinikit\Core\Util\Logging\Logger;

class SerialisableArrayUtils {


    /**
     * Get an array of member values for a given member for the array of objects passed
     * using the same indexing system as the passed objects.
     *
     * @static
     * @param $member
     * @param $objects
     */
    public static function getMemberValueArrayForObjects($member, $objects) {

        $returnValues = array();

        foreach ($objects as $key => $value) {

            if (is_object($value)) {
                if ($value instanceof SerialisableObject) {
                    $returnValues[$key] = $value->__getSerialisablePropertyValue($member);
                } else {
                    throw new ClassNotSerialisableException(get_class($value));
                }
            }
        }

        return $returnValues;

    }


    /**
     * Index the array of passed objects by the supplied member, returning an associative array.
     *
     * @param $member
     * @param $objects
     */
    public static function indexArrayOfObjectsByMember($member, $objects) {

        $returnValues = array();

        foreach ($objects as $object) {
            if ($object instanceof SerialisableObject) {
                $returnValues[$object->__getSerialisablePropertyValue($member)] = $object;
            } else {
                throw new ClassNotSerialisableException(get_class($object));
            }
        }

        return $returnValues;

    }


    /**
     * Filter an array of objects by a specified member.  Perhaps in the future extend
     * to multiple match types.
     *
     * @param $member
     * @param $objects
     * @param $filterValue
     */
    public static function filterArrayOfObjectsByMember($member, $objects, $filterValue) {

        $filterValues = is_array($filterValue) ? $filterValue : array($filterValue);

        $filteredObjects = array();

        foreach ($objects as $object) {
            if ($object instanceof SerialisableObject) {
                foreach ($filterValues as $value) {
                    if ($value == $object->__getSerialisablePropertyValue($member)) {
                        $filteredObjects[] = $object;
                        break;
                    }
                }
            } else {
                throw new ClassNotSerialisableException(get_class($object));
            }
        }

        return $filteredObjects;

    }


    /**
     * Group an array of objects by a given member.
     *
     * @param $member
     * @param $objects
     */
    public static function groupArrayOfObjectsByMember($member, $objects) {

        if (!is_array($member))
            $member = array($member);

        $leafMember = array_pop($member);


        $groupedObjects = new AssociativeArray();

        foreach ($objects as $object) {

            $rootNode = $groupedObjects;
            foreach ($member as $memberComponent) {
                $groupValue = $object->__getSerialisablePropertyValue($memberComponent);
                if (!$groupValue && !is_numeric($groupValue)) $groupValue = "NULL";

                if (!isset($rootNode[$groupValue]))
                    $rootNode[$groupValue] = new AssociativeArray();

                $rootNode = $rootNode[$groupValue];
            }

            $leafValue = $object->__getSerialisablePropertyValue($leafMember);
            if (!$leafValue && !is_numeric($leafValue)) $leafValue = "NULL";

            if (!$rootNode[$leafValue])
                $rootNode[$leafValue] = array();

            $leafValues = $rootNode[$leafValue];
            $leafValues[] = $object;
            $rootNode[$leafValue] = $leafValues;
        }

        return $groupedObjects->toArray();
    }


    /**
     * Convert an array of data into serialisable.  Pass the top level class
     * which either maps to an array / item of a given type.
     *
     * @param $sourceArray
     * @param $initialClass
     */
    public static function convertArrayToSerialisableObjects($sourceArray, $initialClass, $annotations = null) {


        // Get any array qualifiers from the string
        preg_match_all("/\[.*?\]/", $initialClass, $matches);
        $arrayQualifiers = $matches[0];

        $arrayOfObjects = sizeof($arrayQualifiers) > 0;

        $trimmedInitialClass = preg_replace("/\[.*]/", "", $initialClass);

        if (!$annotations)
            $annotations = ClassAnnotationParser::instance()->parse($trimmedInitialClass);


        $primitiveTypes = array("boolean", "int", "integer", "string", "float", "double", "mixed");

        if (is_array($sourceArray) && !in_array($trimmedInitialClass, $primitiveTypes)) {

            // If associative, assume the
            if (!$arrayOfObjects && (ArrayUtils::isAssociative($sourceArray) || sizeof($sourceArray) == 0)) {

                $newInstance = new $trimmedInitialClass();

                foreach ($sourceArray as $key => $value) {

                    if (is_array($value)) {

                        // Check to see if there is a type annotation.
                        $type = $annotations->getFieldAnnotationsForMatchingTag("var", $key);
                        if (sizeof($type) > 0) {
                            $nestedClassType = trim(str_replace("$" . $key, "", $type[0]->getValue()));
                            $nestedClassType = explode(" ", $nestedClassType);
                            $nestedClassType = $nestedClassType[0];
                            $value = self::convertArrayToSerialisableObjects($value, $nestedClassType);
                        }

                    }

                    try {
                        $newInstance->__setSerialisablePropertyValue($key, $value);
                    } catch (PropertyNotWritableException $e) {
                        // Carry on
                    }
                }

                return $newInstance;


            } else if ($arrayOfObjects) {

                $returnValues = array();

                // Knock off the first array qualifier and create the next level down
                array_shift($arrayQualifiers);

                $subClass = $trimmedInitialClass . join("", $arrayQualifiers);

                // Loop through array of objects
                foreach ($sourceArray as $key => $object) {
                    $returnValues[$key] = self::convertArrayToSerialisableObjects($object, $subClass, $annotations);
                }

                return $returnValues;

            } else {
                return $sourceArray;
            }
        } else {
            return $sourceArray;
        }


    }


    /**
     * Convert an array of any depth containing serialisable objects to
     * key / value associative arrays.
     *
     * @param $objects array
     */
    public static function convertSerialisableObjectsToAssociativeArrays($objects, $addValuesToAssociativeArrays = false) {

        $newObjects = array();
        foreach ($objects as $key => $item) {

            if ($item instanceof SerialisableObject) {
                $newObjects[$key] = $item->__toArray();
            } else if (is_array($item)) {
                $newObjects[$key] = self::convertSerialisableObjectsToAssociativeArrays($item, $addValuesToAssociativeArrays);
            } else {
                $newObjects[$key] = $item;
            }

        }

        // if an associative array and adding a values object, add it in
        if ($addValuesToAssociativeArrays && ArrayUtils::isAssociative($objects)) {
            $newObjects["_values"] = array_values($newObjects);
        }

        return $newObjects;
    }


}