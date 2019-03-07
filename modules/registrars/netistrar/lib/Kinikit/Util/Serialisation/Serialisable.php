<?php

namespace Kinikit\Core\Util\Serialisation;


use Kinikit\Core\Exception\PropertyNotReadableException;
use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Util\CodeUtils;

/**
 * Shared Serialisable trait - used in both the Serialisable Object and Serialisable Exception classes.
 *
 * Trait Serialisable
 * @package Kinikit\Core\Util\Serialisation
 */
trait Serialisable {


    /**
     * Store for efficiency purposes the accessor maps for every serialisable type encountered once.
     *
     * @var array
     */
    private static $__accessorMaps;


    /**
     * Get a serialisable property value by name if it exists.  Throw an exception if it doesn't exist.
     *
     * @param string $propertyName
     */
    public function __getSerialisablePropertyValue($propertyName) {
        $map = $this->__getSerialisablePropertyMap();
        if (array_key_exists($propertyName, $map)) {
            return $map [$propertyName];
        } elseif (array_key_exists(substr(strtoupper($propertyName), 0, 1) . substr($propertyName, 1), $map)) {
            return $map [substr(strtoupper($propertyName), 0, 1) . substr($propertyName, 1)];
        } else {
            throw new PropertyNotReadableException (get_class($this), $propertyName);
        }
    }

    /**
     * Set a serialisable property by name if it exists.  Throw an exception if it doesn't exist.
     *
     * @param string $propertyName
     * @param mixed $newValue
     */
    public function __setSerialisablePropertyValue($propertyName, $newValue) {
        $this->__setSerialisablePropertyMap(array($propertyName => $newValue));
    }

    /**
     * Get an associative array of all of the serialisable properties defined on this class.
     */
    public function __getSerialisablePropertyMap() {
        $propertyAccessors = $this->__findSerialisablePropertyAccessors();
        $propertyMap = array();
        foreach ($propertyAccessors as $accessorSet) {

            // If a get accessor exists, add it to the map
            if (isset ($accessorSet ["get"])) {
                $accessor = $accessorSet ["get"];
                if ($accessor instanceof \ReflectionMethod) {

                    // Check that this is not qualified with a @no-serialise tag
                    if (strpos($accessor->getDocComment(), "@no-serialise"))
                        continue;

                    $propertyName = substr($accessor->getName(), 3);
                    $propertyName = CodeUtils::instance()->normalisePropertyName($propertyName);
                    if ($accessor->isPublic())
                        $propertyMap [$propertyName] = $accessor->invoke($this);
                    else if ($accessor->isProtected()) {
                        $methodName = "get" . $propertyName;
                        $propertyMap [$propertyName] = $this->$methodName ();
                    }
                } else if ($accessor instanceof \ReflectionProperty) {

                    if (strpos($accessor->getDocComment(), "@no-serialise"))
                        continue;


                    $propertyName = $accessor->getName();
                    if ($accessor->isPublic())
                        $propertyMap [$propertyName] = $accessor->getValue($this);
                    else if ($accessor->isProtected()) {
                        $propertyName = $accessor->getName();
                        $propertyMap [$propertyName] = $this->$propertyName;
                    }
                }
            }

        }

        return $propertyMap;

    }


    /**
     * Set an associative array of serialisable properties
     */
    public function __setSerialisablePropertyMap($propertyMap, $ignoreNoneWritableProperties = false) {
        $propertyAccessors = $this->__findSerialisablePropertyAccessors();

        $noneWritables = array();
        foreach ($propertyMap as $inputPropertyName => $propertyValue) {
            $propertyName = strtolower($inputPropertyName);

            // If we get a match, call the appropriate function
            if (isset ($propertyAccessors [$propertyName]) && isset ($propertyAccessors [$propertyName] ["set"])) {
                $accessor = $propertyAccessors [$propertyName] ["set"];
                if ($accessor instanceof \ReflectionMethod) {
                    if ($accessor->isPublic())
                        $accessor->invoke($this, $propertyValue);
                    else if ($accessor->isProtected()) {
                        $methodName = "set" . $propertyName;
                        $this->$methodName ($propertyValue);
                    } else {
                        if (!$ignoreNoneWritableProperties)
                            throw new PropertyNotWritableException (get_class($this), $propertyName);
                    }
                } else if ($accessor instanceof \ReflectionProperty) {
                    if ($accessor->isPublic())
                        $accessor->setValue($this, $propertyValue);
                    else if ($accessor->isProtected()) {
                        $propertyName = $accessor->getName();
                        $this->$propertyName = $propertyValue;
                    } else {
                        if (!$ignoreNoneWritableProperties)
                            throw new PropertyNotWritableException (get_class($this), $propertyName);
                    }
                }
            } else {
                // If none writable properties ignored, instead add them to an array for return for processing
                // higher if required, else throw exceptions.
                if ($ignoreNoneWritableProperties) {
                    $noneWritables [$inputPropertyName] = $propertyValue;
                } else {
                    throw new PropertyNotWritableException (get_class($this), $propertyName);
                }
            }

        }

        return $noneWritables;
    }


    /**
     * Find all serialisable property objects, return a map of accessor objects keyed in by GET and SET.
     */
    public function __findSerialisablePropertyAccessors() {

        // Grab the class name first.
        $className = get_class($this);

        // If no accessor map has been previously cached for this class type, cache it now.
        if (!isset (self::$__accessorMaps [$className])) {


            $reflectionClass = new \ReflectionClass ($className);

            // Create the accessors array for storing all possible accessors.
            $accessors = array();

            // Loop through all methods, checking for public get and set accessors first.
            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {

                if ($method->isStatic())
                    continue;

                if ((substr($method->getName(), 0, 3) == "get") && ($method->getNumberOfRequiredParameters() == 0)) {
                    $propertyName = strtolower(substr($method->getName(), 3));
                    if (!isset ($accessors [$propertyName])) {
                        $accessors [$propertyName] = array();
                    }
                    $accessors [$propertyName] ["get"] = $method;
                } else if (substr($method->getName(), 0, 3) == "set") {
                    $propertyName = strtolower(substr($method->getName(), 3));
                    if (!isset ($accessors [$propertyName])) {
                        $accessors [$propertyName] = array();
                    }
                    $accessors [$propertyName] ["set"] = $method;
                }
            }

            // Now loop through all properties, checking for any public / protected ones
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {

                if ($property->isStatic())
                    continue;

                $propertyName = strtolower($property->getName());

                if (!isset ($accessors [$propertyName])) {
                    $accessors [$propertyName] = array();
                }
                if (!isset ($accessors [$propertyName] ["get"]))
                    $accessors [$propertyName] ["get"] = $property;
                if (!isset ($accessors [$propertyName] ["set"]))
                    $accessors [$propertyName] ["set"] = $property;

            }

            self::$__accessorMaps [$className] = $accessors;

        }

        return self::$__accessorMaps [$className];
    }


}