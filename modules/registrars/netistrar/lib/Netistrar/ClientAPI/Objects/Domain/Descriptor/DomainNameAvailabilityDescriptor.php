<?php

namespace Netistrar\ClientAPI\Objects\Domain\Descriptor;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Descriptor for a domain name hinted availability operation.  This is passed to the getHintedAvailability function on the Domains API.
*/
class DomainNameAvailabilityDescriptor extends SerialisableObject {

    /**
     *
     * @var string The search string to seed the availability search
     */
    private $searchString;

    /**
     *
     * @var string[] An optional array of categories to limit the availability search to specific TLD categories as obtained by a call to <b>getTLDCategories</b>.
     */
    private $tldCategories;

    /**
     *
     * @var string[] An optional array of tlds to limit the availability search to specific TLDs as obtained by a call to <b>getTLDs</b>.
     */
    private $tlds;

    /**
     *
     * @var boolean A flag to indicate whether or not to include suggestions in the result set.
     */
    private $suggestions;

    /**
     * If suggestions is set to true an options object may be passed to configure the suggestions
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameSuggestionOptions 
     */
    private $suggestionOptions;



    /**
     * Constructor
     *
     * @param  $searchString
     * @param  $tldCategories
     * @param  $tlds
     * @param  $suggestions
     * @param  $suggestionOptions
     */
    public function __construct($searchString = null, $tldCategories = null, $tlds = null, $suggestions = null, $suggestionOptions = null){

        $this->searchString = $searchString;
        $this->tldCategories = $tldCategories;
        $this->tlds = $tlds;
        $this->suggestions = $suggestions;
        $this->suggestionOptions = $suggestionOptions;
        
    }

    /**
     * Get the searchString
     *
     * @return string
     */
    public function getSearchString(){
        return $this->searchString;
    }

    /**
     * Set the searchString
     *
     * @param string $searchString
     * @return DomainNameAvailabilityDescriptor
     */
    public function setSearchString($searchString){
        $this->searchString = $searchString;
        return $this;
    }

    /**
     * Get the tldCategories
     *
     * @return string[]
     */
    public function getTldCategories(){
        return $this->tldCategories;
    }

    /**
     * Set the tldCategories
     *
     * @param string[] $tldCategories
     * @return DomainNameAvailabilityDescriptor
     */
    public function setTldCategories($tldCategories){
        $this->tldCategories = $tldCategories;
        return $this;
    }

    /**
     * Get the tlds
     *
     * @return string[]
     */
    public function getTlds(){
        return $this->tlds;
    }

    /**
     * Set the tlds
     *
     * @param string[] $tlds
     * @return DomainNameAvailabilityDescriptor
     */
    public function setTlds($tlds){
        $this->tlds = $tlds;
        return $this;
    }

    /**
     * Get the suggestions
     *
     * @return boolean
     */
    public function getSuggestions(){
        return $this->suggestions;
    }

    /**
     * Set the suggestions
     *
     * @param boolean $suggestions
     * @return DomainNameAvailabilityDescriptor
     */
    public function setSuggestions($suggestions){
        $this->suggestions = $suggestions;
        return $this;
    }

    /**
     * Get the suggestionOptions
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameSuggestionOptions
     */
    public function getSuggestionOptions(){
        return $this->suggestionOptions;
    }

    /**
     * Set the suggestionOptions
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameSuggestionOptions $suggestionOptions
     * @return DomainNameAvailabilityDescriptor
     */
    public function setSuggestionOptions($suggestionOptions){
        $this->suggestionOptions = $suggestionOptions;
        return $this;
    }


}