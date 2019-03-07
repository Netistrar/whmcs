<?php

namespace Netistrar\ClientAPI\Objects\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Encodes the results from a call to <b>getHintedAvailability</b>
*/
class DomainAvailabilityResults extends SerialisableObject {

    /**
     * The direct domain result as a DomainAvailability object.  This will only be available if the domainString argument to getHintedAvailability is a fully qualified domain name e.g. mydomain.com
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainAvailability 
     */
    protected $directResult;

    /**
     * An associative array indexed by the requested category names supplied in the tldCategories argument to getHintedAvailability.
     * Each array item is itself an associative array indexed by the TLD names for each TLD in the given category.
     * The entries of this array are DomainAvailability objects pertaining to the domain name formed by combining the prefix of the domainString and the respective TLD.
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[string][string] 
     */
    protected $categoryResults;

    /**
     * An associative array indexed by the requested TLD names supplied in the tlds argument to getHintedAvailability.  Each array item is a DomainAvailability object pertaining to the domain name formed by combining the prefix of the domainString and the TLD in question.
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[string] 
     */
    protected $tldResults;

    /**
     * An associative array indexed by tld of suggestions.  Each array item is itself an array of suggestions for the given tld.
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[string][] 
     */
    protected $tldSuggestions;

    /**
     * An array of available suggestions for search result listings, this will prioritise exact matches and return
     * suggestions if not available.
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[] 
     */
    protected $suggestions;



    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }

    /**
     * Get the directResult
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailability
     */
    public function getDirectResult(){
        return $this->directResult;
    }

    /**
     * Get the categoryResults
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[string][string]
     */
    public function getCategoryResults(){
        return $this->categoryResults;
    }

    /**
     * Get the tldResults
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[string]
     */
    public function getTldResults(){
        return $this->tldResults;
    }

    /**
     * Get the tldSuggestions
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[string][]
     */
    public function getTldSuggestions(){
        return $this->tldSuggestions;
    }

    /**
     * Get the suggestions
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailability[]
     */
    public function getSuggestions(){
        return $this->suggestions;
    }


}