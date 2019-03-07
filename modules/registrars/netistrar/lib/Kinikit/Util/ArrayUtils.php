<?php

namespace Kinikit\Core\Util;


use Kinikit\Core\Object\AssociativeArray;

/**
 * Array utilities
 *
 * @author mark
 *
 */
class ArrayUtils {


    /**
     * Return a boolean if the array is associative
     *
     * @param $array
     */
    public static function isAssociative($array) {
        return array_values($array) !== $array;
    }

    /**
     * Take an associative array as input and return a new array with all the original keys prefixed by the
     * passed prefix.
     *
     * @param array $array
     * @param string $prefix
     *
     * @return array
     */
    public static function prefixArrayKeys($array, $prefix) {

        $prefixedArray = array();
        foreach ($array as $key => $value) {
            $prefixedArray [$prefix . $key] = $value;
        }

        return $prefixedArray;

    }

    /**
     * Find all parameters in the passed array with a key starting with the supplied prefix.
     * Return them as an associative array by key.
     *
     * @param array $array
     * @param string $prefix
     *
     * @return array
     */
    public static function getAllArrayItemsByKeyPrefix($array, $prefix, $stripPrefix = false) {
        $returnValues = array();
        foreach ($array as $key => $value) {
            $positionOfPrefix = strpos($key, $prefix);
            if (is_numeric($positionOfPrefix) && $positionOfPrefix == 0) {
                if ($stripPrefix) $key = substr($key, strlen($prefix));
                $returnValues [$key] = $value;
            }
        }

        return $returnValues;
    }


    /**
     * Handy alternative to direct array access, particularly useful when
     * accessing array elements from function return values.
     *
     * @param $array
     * @param $key
     */
    public static function arrayElementValue($array, $key) {
        return $array[$key];
    }


    /**
     * Diff function for arrays which works with arrays of objects as well as string comparables.
     *
     * @param $array1
     * @param $array2
     */
    public static function arrayDiff($array1, $array2) {
        return array_udiff($array1, $array2,
            function ($a, $b) {
                if (is_object($a)) {
                    return strcmp(spl_object_hash($a), spl_object_hash($b));
                } else if (is_array($a)) {
                    return ArrayUtils::arrayDiff($a, $b);
                } else {
                    return strcmp($a, $b);
                }
            }
        );
    }

    /**
     * Recursively merge array1 and array2.  If keys exist in both arrays at any level, take the value from array1 in preference.
     *
     * @param $array1
     * @param $array2
     */
    public static function recursiveMerge($array1, $array2) {

        if ($array1 instanceof AssociativeArray) {
            $array1 = $array1->toArray();
        }

        if ($array2 instanceof AssociativeArray) {
            $array2 = $array2->toArray();
        }

        if (ArrayUtils::isAssociative($array1) != ArrayUtils::isAssociative($array2)) {

            if (ArrayUtils::isAssociative($array1)) {
                $array1 = array($array1);
            } else {
                $array2 = array($array2);
            }
        }

        $combinedKeys = array_unique(array_merge(array_keys($array1), array_keys($array2)));

        $mergedArray = array();
        foreach ($combinedKeys as $key) {


            if (isset($array1[$key]) && isset($array2[$key])) {

                if ((is_array($array1[$key]) || $array1[$key] instanceof AssociativeArray) && (is_array($array2[$key]) || $array2[$key] instanceof AssociativeArray)) {
                    $mergedArray[$key] = ArrayUtils::recursiveMerge($array1[$key], $array2[$key]);
                } else {
                    $mergedArray[$key] = $array1[$key];
                }

            } else {
                $mergedArray[$key] = isset($array1[$key]) ? $array1[$key] : $array2[$key];
            }

        }


        return $mergedArray;
    }


    /**
     * Return an array of different elements within the 2 arrays.  If matching keys only is supplied, only elements which exist in both but which are different are included.
     *
     * The returned array contains an associative array for each differing value with a "value1" and "value2" element.
     *
     * @param $array1
     * @param $array2
     *
     * @param bool $matchingKeysOnly
     */
    public static function recursiveDiff($array1, $array2, $caseInsensitive = true, $matchingKeysOnly = true) {

        if ($array1 instanceof AssociativeArray) {
            $array1 = $array1->toArray();
        }

        if ($array2 instanceof AssociativeArray) {
            $array2 = $array2->toArray();
        }

        $sharedKeys = array_intersect(array_keys($array1), array_keys($array2));

        $diffArray = array();

        // Only care about shared keys
        foreach ($sharedKeys as $key) {
            if ((is_array($array1[$key]) || $array1[$key] instanceof AssociativeArray) && (is_array($array2[$key]) || $array2[$key] instanceof AssociativeArray)) {

                $diffArray[$key] = ArrayUtils::recursiveDiff($array1[$key], $array2[$key], $caseInsensitive, $matchingKeysOnly);

            } else {

                $value1 = $caseInsensitive ? strtolower($array1[$key]) : $array1[$key];
                $value2 = $caseInsensitive ? strtolower($array2[$key]) : $array2[$key];

                if ($value1 != $value2) {
                    $diffArray[$key] = array("value1" => $array1[$key], "value2" => $array2[$key]);
                }
            }
        }

        return $diffArray;

    }


    /**
     * Reduce the array to only elements with the passed key at any depth.
     *
     * @param $array
     * @param $key
     */
    public static function reduceToElementsWithKey($array, $key) {

        if ($array instanceof AssociativeArray) {
            $array = $array->toArray();
        }

        $targetArray = array();

        foreach ($array as $itemKey => $value) {

            if ($itemKey == $key) {
                $targetArray[$key] = $value;
            } else if (is_array($value) || $value instanceof AssociativeArray) {
                $subArray = ArrayUtils::reduceToElementsWithKey($value, $key);
                if (sizeof($subArray) > 0) {
                    $targetArray[$itemKey] = $subArray;
                }
            }

        }

        return $targetArray;

    }


    /**
     * Find elements by key.
     *
     * @param $array
     * @param $key
     */
    public static function findElementsByKey($array, $key, &$targetArray = null, $prefix = "") {

        if ($array instanceof AssociativeArray) {
            $array = $array->toArray();
        }

        if (!$targetArray)
            $targetArray = array();

        foreach ($array as $itemKey => $value) {

            if ($itemKey === $key) {
                $targetArray[substr($prefix . "." . $key, 1)] = $value;
            } else if (is_array($value) || $value instanceof AssociativeArray) {

                if (is_numeric($itemKey)) {
                    $newPrefix = $prefix . "[" . $itemKey . "]";
                } else {
                    $newPrefix = $prefix . "." . $itemKey;
                }

                ArrayUtils::findElementsByKey($value, $key, $targetArray, $newPrefix);
            }
        }

        return $targetArray;

    }


    /**
     * Group an array of elements by one or more of it's keys.  Essentially convert into an indexed array for each key type.
     *
     * @param $array
     * @param $groupKeys
     */
    public static function groupElementsByValueKeys($array, $groupKeys) {

        if (!is_array($groupKeys)) {
            $groupKeys = array($groupKeys);
        }

        $newArray = new AssociativeArray();
        foreach ($array as $element) {

            $applyObject = $newArray;
            foreach ($groupKeys as $index => $groupKey) {
                if ($index < sizeof($groupKeys) - 1) {
                    $subArray = $applyObject[$element[$groupKeys[$index]]];
                    if (!$subArray) {
                        $subArray = new AssociativeArray();
                        $applyObject[$element[$groupKeys[$index]]] = $subArray;
                    }
                    $applyObject = $subArray;
                } else {
                    if (!is_array($applyObject[$element[$groupKeys[$index]]])) {
                        $applyObject[$element[$groupKeys[$index]]] = array();
                    }
                    $applyObject[$element[$groupKeys[$index]]][] = $element;

                }
            }

        }

        
        return $newArray->toArray();
    }


}

?>