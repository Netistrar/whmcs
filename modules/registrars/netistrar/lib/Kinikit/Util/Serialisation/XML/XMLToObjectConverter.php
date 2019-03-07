<?php

namespace Kinikit\Core\Util\Serialisation\XML;

use Kinikit\Core\Exception\ClassNotFoundException;
use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Object\DynamicSerialisableObject;
use Kinikit\Core\Util\ClassUtils;
use Kinikit\Core\Util\Serialisation\FormatToObjectConverter;

/**
 * General singleton class for converting xml (usually in the form of a DOM node) into objects within the system.
 *
 * The XML structure is best demonstrated by an example.
 *
 * Suppose we have a contact entity with name, phone number, age and an array of notes.  The source XML would look like
 *
 * <Contact>
 * <name>Mark Robertshaw</name>
 * <phoneNumber>01865 989899</phoneNumber>
 * <age>30</age>
 * <notes>
 * <Array>
 * <value>Good kind of chap</value>
 * <value>Need some encouragement</value>
 * </Array>
 * </notes>
 * </Contact>
 *
 *
 * Provided an entity exists (and already included) in the system called Contact with a blank constructor and
 * access (either member access or via set methods) is granted to the properties name, phone, age and notes this can
 * be mapped to a new instance.  NB:  If the contact entity extends ProtectedAccessible these accessor fields / methods
 * can be protected.
 *
 *
 *
 */
class XMLToObjectConverter implements FormatToObjectConverter {

    private $tagClassMap = array();
    private $strictMode = true;
    private $stopNodes = array();

    /**
     * Construct optionally with a tag class map for custom mappings (such as for configuration files etc)
     * If strict mode is true, class not found exceptions will be raised if classes do not exist as required.  Otherwise
     * elements without non-existent class mappings will be mapped to DynamicSerialisableObjects.
     *
     * If stop nodes are specified, these will cause any contained data to be left as string content
     *
     * @param array $tagClassMap
     * @param boolean $strictMode
     * @param array $stopNodes
     */
    public function __construct($tagClassMap = array(), $strictMode = true, $stopNodes = array()) {
        $this->tagClassMap = $tagClassMap;
        $this->strictMode = $strictMode;
        $this->stopNodes = $stopNodes;
    }

    /**
     * Main conversion API method to convert an XML string into an object tree.
     *
     * @param unknown_type $xmlString
     */
    public function convert($xmlString) {

        $dom = new \DOMDocument ();
        $dom->loadXML($xmlString);

        $documentElement = $dom->documentElement;
        return $documentElement ? $this->convertDOM($dom->documentElement) : $xmlString;
    }

    /**
     * Convert a DOM element into an object tree
     *
     * @param DOMElement $domElement
     */
    public function convertDOM($domElement) {

        $value = "";

        // Deal with straightforward primitives first.  These occur when a text node is passed
        if ($domElement instanceof \DOMText || $domElement instanceof \DOMAttr) {

            // Get the value
            $value = utf8_decode($domElement->nodeValue);

            // Handle booleans as special case
            if (strtolower($value) == "true") {
                $value = true;
            } elseif (strtolower($value) == "false") {
                $value = false;
            }

        } // Now deal with the inherently more complex element cases
        else if ($domElement instanceof \DOMElement) {

            // Get the name
            $elementName = $domElement->nodeName;

            // Handle the Array case next
            if (strtolower($elementName) == "array" || (isset($this->tagClassMap[$elementName]) && strtolower($this->tagClassMap [$elementName]) == "array")) {

                $value = array();

                // Loop through each child.  If we encounter "Value" elements we find the first child of the value item and pass this off for
                // recursive processing, otherwise we pass the child node off for processing and add the result to the array to return
                $arrayElements = $domElement->childNodes;
                for ($i = 0; $i < $arrayElements->length; $i++) {
                    $arrayElement = $arrayElements->item($i);

                    if ($arrayElement instanceof \DOMElement) {

                        $key = $arrayElement->getAttribute("key");

                        // Convert the value into an object
                        $convertedValue = $this->convertDOM($arrayElement);

                        // Now set the array value either with a key or not depending on whether a key attribute is set.
                        if (isset ($key) && $key) {
                            $value [$key] = $convertedValue;
                        } else {
                            $value [] = $convertedValue;
                        }
                    }
                }

            } // Handle the case where a value node is passed.  This only occurs in the case where a lazy array is defined
            // With a single entry.  Here we simply evaluate and pass back the child entity
            else if (strtolower($elementName) == "value") {

                $valueElements = $domElement->childNodes;

                // Check for the count of child nodes to see whether we need to implicitly create an array wrapper
                if ($valueElements->length == 0) {
                    $value = "";
                } else if ($valueElements->length == 1) {
                    $value = $this->convertDOM($valueElements->item(0));
                } else {
                    $value = array();
                    for ($j = 0; $j < $valueElements->length; $j++) {
                        $currentItem = $valueElements->item($j);
                        if (!($currentItem instanceof \DOMText) && !($currentItem instanceof \DOMComment)) {
                            $key = $currentItem->getAttribute("key");
                            if (isset ($key) && $key) {
                                $value [$key] = $this->convertDOM($currentItem);
                            } else {
                                $value [] = $this->convertDOM($currentItem);
                            }
                        }
                    }
                    // If the result was only in fact a single item, reduce the array to a single value again.
                    if (sizeof($value) == 1) {
                        $value = $value [0];
                    }
                }

            } // Otherwise we must be in the object case
            else {

                // Array of property sets which pertain to set or get methods.  These
                // ensure that we write non accessor members first and prevent
                // ordering problems when we provide custom functionality to write to
                // properties in other get / set methods.
                $deferredPropertySets = array();

                // If a mapping has been defined for this element, use the mapping
                if (isset ($this->tagClassMap [$elementName])) {
                    $elementName = $this->tagClassMap [$elementName];
                }

                // Create the class

                if (strtolower($elementName) == "value") {
                    $value = $this->convertDOM($domElement->childNodes->item(0));
                } else {
                    $value = $this->loadObject($domElement, $elementName);
                }
            }

        }

        return $value;

    }

    public function loadObject($domElement, $elementName) {

        $value = "";

        $deferredPropertySets = array();

        try {

            if ($domElement->getAttribute("phpNameSpace")) {
                $elementName = $domElement->getAttribute("phpNameSpace") . "\\" . $elementName;
            }

            $value = ClassUtils::createNewClassInstance($elementName, null, true);
        } catch (ClassNotFoundException $e) {
            if ($this->strictMode) {
                throw $e;
            } else {
                $value = new DynamicSerialisableObject(false);
                $value->__setSerialisablePropertyValue("entityClass", $elementName);
            }
        }


        // Loop through all attributes, these are member references
        $memberAttributes = $domElement->attributes;
        for ($i = 0; $i < $memberAttributes->length; $i++) {
            $memberAttribute = $memberAttributes->item($i);
            $memberName = $memberAttribute->name;

            if ($memberName == "phpNameSpace")
                continue;

            // Convert the attribute value recursively to handle special cases.
            $memberValue = $this->convertDOM($memberAttribute);

            try {
                // Set the property value if no setter exists for it, otherwise defer until the end.
                $value->__setSerialisablePropertyValue($memberName, $memberValue);
            } catch (PropertyNotWritableException $e) {
                if ($this->strictMode)
                    throw ($e);
            }
        }

        // Loop through each child node, these are member references
        $memberElements = $domElement->childNodes;

        for ($i = 0; $i < $memberElements->length; $i++) {
            $memberElement = $memberElements->item($i);
            $memberName = $memberElement->nodeName;

            if (!($memberElement instanceof \DOMText) && !($memberElement instanceof \DOMComment)) {

                // Get the value elements.
                $valueElements = $memberElement->childNodes;

                // Check for the count of child nodes to see whether we need to implicitly create an array wrapper
                if ($valueElements->length == 0) {
                    $memberValue = "";
                } else if ($valueElements->length == 1) {

                    if (!is_numeric(array_search($memberName, $this->stopNodes)))
                        $memberValue = $this->convertDOM($valueElements->item(0));

                    else {
                        $node = $valueElements->item(0);
                        $document = new \DOMDocument();
                        $document->appendChild($document->importNode($node, true));
                        $memberValue = $document->saveXML();
                    }

                } else {
                    $memberValue = array();
                    for ($j = 0; $j < $valueElements->length; $j++) {
                        $currentItem = $valueElements->item($j);
                        if (!($currentItem instanceof \DOMText) && !($currentItem instanceof \DOMComment)) {
                            $key = $currentItem->getAttribute("key");
                            if (isset ($key) && $key) {
                                $memberValue [$key] = $this->convertDOM($currentItem);
                            } else {
                                $memberValue [] = $this->convertDOM($currentItem);
                            }
                        }
                    }
                    // If the result was only in fact a single item, reduce the array to a single value again.
                    if (sizeof($memberValue) == 1 && isset($memberValue[0])) {
                        $memberValue = $memberValue [0];
                    }
                }

            } // If a none zero text node, assume the magic text member.
            else if (strlen(trim($memberElement->nodeValue)) > 0) {
                $memberName = "text";
                $memberValue = $this->convertDOM($memberElement);
            } else {
                continue;
            }

            try {
                $value->__setSerialisablePropertyValue($memberName, $memberValue);
            } catch (PropertyNotWritableException $e) {
                if ($this->strictMode)
                    throw ($e);
            }
        }

        return $value;

    }

}

?>
