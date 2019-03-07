<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 13:02
 */

namespace Kinikit\Core\Util\Annotation;


class ClassAnnotations {

    private $className;
    private $classAnnotations;
    private $fieldAnnotations;
    private $methodAnnotations;

    /**
     * ClassAnnotations constructor.
     * @param $className
     * @param $classAnnotations
     * @param $fieldAnnotations
     */
    public function __construct($className, $classAnnotations, $fieldAnnotations, $methodAnnotations) {
        $this->className = $className;
        $this->classAnnotations = $classAnnotations;
        $this->fieldAnnotations = $fieldAnnotations;
        $this->methodAnnotations = $methodAnnotations;
    }


    /**
     * @return mixed
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className) {
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getClassAnnotations() {
        return $this->classAnnotations;
    }

    /**
     * @param mixed $classAnnotations
     */
    public function setClassAnnotations($classAnnotations) {
        $this->classAnnotations = $classAnnotations;
    }

    /**
     * @return mixed
     */
    public function getFieldAnnotations() {
        return $this->fieldAnnotations ? $this->fieldAnnotations : array();
    }

    /**
     * @param mixed $fieldAnnotations
     */
    public function setFieldAnnotations($fieldAnnotations) {
        $this->fieldAnnotations = $fieldAnnotations;
    }

    /**
     * @return mixed
     */
    public function getMethodAnnotations() {
        return $this->methodAnnotations ? $this->methodAnnotations : array();
    }

    /**
     * @param mixed $methodAnnotations
     */
    public function setMethodAnnotations($methodAnnotations) {
        $this->methodAnnotations = $methodAnnotations;
    }

    /**
     * Get any class annotations with a matching tag.  Return these indexed by field name.
     *
     * @param $string
     */
    public function getClassAnnotationForMatchingTag($tag) {

        if (isset($this->classAnnotations[$tag])) {
            return $this->classAnnotations[$tag][0];
        }

    }


    /**
     * Get any method annotations with a matching tag.  Return these indexed by field name.
     *
     * @param $tag
     * @param $methodName
     * @return null
     */
    public function getMethodAnnotationsForMatchingTag($tag, $methodName = null) {
        if ($methodName) {
            return isset($this->methodAnnotations[$methodName][$tag]) ? $this->methodAnnotations[$methodName][$tag] : array();
        } else {
            $matchingAnnotations = array();
            foreach ($this->getMethodAnnotations() as $method => $methodAnnotations) {
                if (isset($methodAnnotations[$tag])) {
                    $matchingAnnotations[$method] = $methodAnnotations[$tag];
                }
            }

            return $matchingAnnotations;
        }
    }


    /**
     * Get any field annotations with a matching tag.  Return these indexed by field name.
     *
     * @param $string
     */
    public function getFieldAnnotationsForMatchingTag($tag, $fieldName = null) {

        if ($fieldName) {
            return isset($this->fieldAnnotations[$fieldName][$tag]) ? $this->fieldAnnotations[$fieldName][$tag] : array();
        } else {

            $matchingAnnotations = array();
            foreach ($this->getFieldAnnotations() as $field => $fieldAnnotations) {
                if (isset($fieldAnnotations[$tag])) {
                    $matchingAnnotations[$field] = $fieldAnnotations[$tag];
                }
            }

            return $matchingAnnotations;

        }
    }


    /**
     * Get the full set of field annotations for any fields containing a tag.
     *
     * @param $tag
     */
    public function getFieldAnnotationsContainingMatchingTag($tag) {
        $matchingAnnotations = array();
        foreach ($this->getFieldAnnotations() as $field => $fieldAnnotations) {
            if (isset($fieldAnnotations[$tag])) {
                $matchingAnnotations[$field] = $fieldAnnotations;
            }
        }

        return $matchingAnnotations;

    }


    /**
     * Get all field annotations not containing passed tags.
     *
     * @param array $tags
     * @return array
     */
    public function getFieldAnnotationsNotContainingTags($tags = array()) {
        $matchingAnnotations = array();
        foreach ($this->getFieldAnnotations() as $field => $fieldAnnotations) {

            $fieldAnnotationKeys = array_keys($fieldAnnotations);
            if (!array_intersect($fieldAnnotationKeys, $tags)) {
                $matchingAnnotations[$field] = $fieldAnnotations;
            }
        }

        return $matchingAnnotations;
    }

}