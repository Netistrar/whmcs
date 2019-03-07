<?php

namespace Netistrar\ClientAPI\Objects\Domain\Descriptor;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Configuration options for domain name suggestions.
 * Class DomainNameSuggestionOptions
*/
class DomainNameSuggestionOptions extends SerialisableObject {

    /**
     * Specify whether to include Tlds in general suggestions
     *
     * @var boolean 
     */
    private $includeTlds;

    /**
     * Specify whether to include category tlds in general suggestions
     *
     * @var boolean 
     */
    private $includeCategories;

    /**
     * If specified, this will fill the suggestions up to the supplied
     * number of results.  If supplied as null it will return one suggestion per
     * included TLD only.
     *
     * @var integer 
     */
    private $fillCount;



    /**
     * Constructor
     *
     * @param  $includeTlds
     * @param  $includeCategories
     * @param  $fillCount
     */
    public function __construct($includeTlds = 1, $includeCategories = 1, $fillCount = 25){

        $this->includeTlds = $includeTlds;
        $this->includeCategories = $includeCategories;
        $this->fillCount = $fillCount;
        
    }

    /**
     * Get the includeTlds
     *
     * @return boolean
     */
    public function getIncludeTlds(){
        return $this->includeTlds;
    }

    /**
     * Set the includeTlds
     *
     * @param boolean $includeTlds
     * @return DomainNameSuggestionOptions
     */
    public function setIncludeTlds($includeTlds){
        $this->includeTlds = $includeTlds;
        return $this;
    }

    /**
     * Get the includeCategories
     *
     * @return boolean
     */
    public function getIncludeCategories(){
        return $this->includeCategories;
    }

    /**
     * Set the includeCategories
     *
     * @param boolean $includeCategories
     * @return DomainNameSuggestionOptions
     */
    public function setIncludeCategories($includeCategories){
        $this->includeCategories = $includeCategories;
        return $this;
    }

    /**
     * Get the fillCount
     *
     * @return integer
     */
    public function getFillCount(){
        return $this->fillCount;
    }

    /**
     * Set the fillCount
     *
     * @param integer $fillCount
     * @return DomainNameSuggestionOptions
     */
    public function setFillCount($fillCount){
        $this->fillCount = $fillCount;
        return $this;
    }


}