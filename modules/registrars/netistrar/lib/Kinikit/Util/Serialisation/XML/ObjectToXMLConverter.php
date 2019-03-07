<?php

namespace Kinikit\Core\Util\Serialisation\XML;

use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Util\Serialisation\ObjectToFormatConverter;

/**
 * General singleton conversion class which takes any object (including primitive data types and arrays) and
 * outputs an XML representation.  The converter works recursively creating suitable XML for nested objects too
 * if required
 *
 */
class ObjectToXMLConverter implements ObjectToFormatConverter {

    private $objectPath = array();
    private $tagClassMap;

    public function __construct($tagClassMap = array()) {
        $this->tagClassMap = $tagClassMap;
    }

    /**
     * Convert the source object to XML whichever form this might take.  Operate recursively
     * to return a nice nested XML structure for complex objects.
     *
     * The omit container elements will produce a looser structure (still parsable) with the formal Array
     * and Value tags omitted.  This produces better output for external systems.
     *
     * The include singleton class elements will remove object class tags within none array object members
     * This is useful for external system compatibility.
     *
     * e.g. <contact><Contact><contactName>Mark</contactName></Contact></contact>
     * Would become simply <contact><contactName>Mark</contactName></contact>
     *     *
     * NB:  This flag will break compatibility with the inverse XMLToObjectConverter as the class information
     * is lost by removing this tag.
     *
     *
     *
     * @param Object $object
     */
    public function convert($object, $ignoreEmptyMembers = false, $omitContainerElements = false, $includeSingletonClassElements = true) {

        $returnValue = "";

        // Check argument is object first
        if (is_object($object)) {

            if (!($object instanceof SerialisableObject)) {
                throw new ClassNotSerialisableException (get_class($object));
            }

            // Get a map containing all member names with their respective values using an object property inspector
            $allMemberMap = $object->__getSerialisablePropertyMap();

            // Check for id field and add to key
            $hasId = isset ($allMemberMap ["id"]);
            if ($hasId) {

                $idValue = get_class($object) . $allMemberMap ["id"];

                // If the object is a descendent of itself, return ""
                if (array_search($idValue, $this->objectPath)) {
                    return null;
                }

                array_push($this->objectPath, $idValue);
            }

            // Get the class name
            $objectClassName = get_class($object);

            if (strpos($objectClassName, "\\")) {
                $explodedClass = explode("\\", $objectClassName);
                $objectClassName = array_pop($explodedClass);
                $namespace = join("\\", $explodedClass);
            } else {
                $namespace = null;
            }


            if (isset($this->tagClassMap[$objectClassName])) {
                $objectClassName = $this->tagClassMap[$objectClassName];
                $namespace = null;
            }


            // Check to see whether the magic text or key members exist
            $hasText = isset ($allMemberMap ["text"]);
            $hasKey = isset ($allMemberMap ["key"]);


            if ($includeSingletonClassElements) {

                // Start with the opening class name XML tag
                $returnValue = "<" . $objectClassName;

                if ($namespace) {
                    $returnValue .= ' phpNameSpace="' . $namespace . '"';
                }

                // If magic key member, write the key attribute and unset the array value to prevent double writing.
                if ($hasKey) {
                    $returnValue .= ' key="' . $allMemberMap ["key"] . '"';
                }

                // If no text, close the opening tag as we will write all members as seperate elements
                if (!$hasText)
                    $returnValue .= ">";

            }


            // Draw xml for each member evaluating the value recursively.
            foreach ($allMemberMap as $memberName => $memberValue) {

                $memberName = $this->processMemberName($memberName);

                // Ignore key members as these have been dealt with seperately above
                if (strtolower($memberName) != "key") {

                    if ($hasText) {
                        if (strtolower($memberName) != "text")
                            $returnValue .= ' ' . $memberName . '="' . $memberValue . '"';
                    } else {
                        if (!$ignoreEmptyMembers || $memberValue != null) {
                            $returnValue .= "<" . $memberName . ">";
                            $returnValue .= $this->convert($memberValue, $ignoreEmptyMembers, $omitContainerElements, $includeSingletonClassElements);
                            $returnValue .= "</" . $memberName . ">";
                        }
                    }
                }
            }

            // If has text, write the text bit
            if ($hasText)
                $returnValue .= ">" . $allMemberMap ["text"];


            if ($includeSingletonClassElements)

                // End with the closing class name XML tag
                $returnValue .= "</" . $objectClassName . ">";

            if ($hasId)
                array_pop($this->objectPath);

        } // Now process arrays
        else if (is_array($object)) {

            if (!$omitContainerElements)
                $returnValue = "<Array>";

            // Check to see if the array is associative to decide whether or not to doctor with the appropriate key
            $associative = ($object != array_values($object));

            foreach ($object as $arrayKey => $arrayItem) {

                $xmlForItem = "";


                if (!$includeSingletonClassElements && is_object($arrayItem)) {
                    $objectClassName = get_class($arrayItem);
                    $objectClassName = isset($this->tagClassMap[$objectClassName]) ? $this->tagClassMap[$objectClassName] : $objectClassName;
                    $xmlForItem .= "<" . $objectClassName . ">";
                }

                $xmlForItem .= $this->convert($arrayItem, $ignoreEmptyMembers, $omitContainerElements, $includeSingletonClassElements);

                if (!$includeSingletonClassElements && is_object($arrayItem))
                    $xmlForItem .= "</" . $objectClassName . ">";


                // If the returned value was not XML, use <Value> Tag (for primitives)
                if ($associative || (substr($xmlForItem, 0, 3) == "<![" || $xmlForItem == 1 || $xmlForItem == 0)) {

                    if (!$omitContainerElements) {
                        $returnValue .= "<Value";
                        if ($associative)
                            $returnValue .= ' key="' . $arrayKey . '"';
                        $returnValue .= ">";
                    }
                }

                // Add the value returned
                $returnValue .= $xmlForItem;

                // If not xml returned close </Value> Tag
                if ($associative || (substr($xmlForItem, 0, 3) == "<![" || $xmlForItem == 1 || $xmlForItem == 0)) {
                    if (!$omitContainerElements)
                        $returnValue .= "</Value>";
                }
            }

            if (!$omitContainerElements)
                $returnValue .= "</Array>";
        } // Booleans next
        else if (is_bool($object)) {
            $returnValue = $object ? 1 : 0;
        } // Otherwise simply echo source material provided no cdata breakages within the data (assume primitive object type)
        else {
            $returnValue = ((!is_numeric($object) && $object == null) || strpos($object, "]]>")) ? "" : "<![CDATA[" . utf8_encode($object) . "]]>";
        }

        return $returnValue;

    }


    /**
     * Overridable method for processing member names if required.
     *
     * @param $memberName
     * @return mixed
     */
    public function processMemberName($memberName) {
        return $memberName;
    }


}

?>