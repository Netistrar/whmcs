<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 13:01
 */

namespace Kinikit\Core\Util\Annotation;


use Kinikit\Core\Object\SerialisableObject;

class Annotation extends SerialisableObject{

    private $label;
    private $value;

    /**
     * Annotation constructor.
     * @param $label
     * @param $value
     */
    public function __construct($label, $value = null) {
        $this->label = $label;
        $this->value = $value;
    }


    /**
     * @return mixed
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }


    /**
     * Return value as an array split by ","
     */
    public function getValues() {
        $exploded = explode(",", $this->getValue());
        $values = array();
        foreach ($exploded as $entry){
            $values[] = trim($entry);
        }

        return $values;
    }


}