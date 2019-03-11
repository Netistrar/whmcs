<?php
/**
 * Netistrar WHMCS Reseller Registry Module.
 *
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Netistrar\ClientAPI\APIProvider;
use Netistrar\ClientAPI\Exception\TransactionException;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameAvailabilityDescriptor;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameCreateDescriptor;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameTransferDescriptor;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameUpdateDescriptor;
use Netistrar\ClientAPI\Objects\Domain\DomainNameContact;
use Netistrar\ClientAPI\Objects\Domain\DomainNameGlueRecord;
use WHMCS\Domain\Registrar\Domain;
use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Module\Registrar\Registrarmodule\ApiClient;

// Run off vendor if exists for development
if (file_exists(__DIR__ . "/../../../vendor/netistrar"))
    include __DIR__ . "/../../../vendor/autoload.php";
else
    include __DIR__ . "/lib/autoload.php";


/**
 * Define module related metadata
 *
 * Provide some module information including the display name and API Version to
 * determine the method of decoding the input values.
 *
 * @return array
 */
function netistrar_MetaData() {
    return array(
        'DisplayName' => 'Netistrar',
        'APIVersion' => '1.1',
    );
}

/**
 * Define registrar configuration options.
 *
 * @return array
 */
function netistrar_getConfigArray() {
    return array(
        // Friendly display name for the module
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Netistrar',
        ),
        "environment" => array(
            "FriendlyName" => "Environment",
            "Type" => "radio", # Radio Selection of Options
            "Options" => "OTE,Production",
            "Description" => "Which environment to connect to",
            "Default" => "OTE",
        ),
        // The API Key
        'apiKey' => array(
            'FriendlyName' => "API Key",
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'API Key as found in My Account -> API Settings under REST API',
        ),
        // The API Secret
        'apiSecret' => array(
            'FriendlyName' => "API Secret",
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'API Secret as found in My Account -> API Settings under REST API',
        )
    );
}

/**
 * Register a domain.
 *
 * Attempt to register a domain with the domain registrar.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain registration order
 * * When a pending domain registration order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_RegisterDomain($params) {


    // Create the domain names structure.
    $domainNames = array($params['sld'] . "." . $params['tld']);


    // Construct nameservers
    $nameservers = array();
    if ($params["ns1"]) $nameservers[] = $params["ns1"];
    if ($params["ns2"]) $nameservers[] = $params["ns2"];
    if ($params["ns3"]) $nameservers[] = $params["ns3"];
    if ($params["ns4"]) $nameservers[] = $params["ns4"];
    if ($params["ns5"]) $nameservers[] = $params["ns5"];

    $phone = explode(".", $params["fullphonenumber"]);

    $ownerContact = new DomainNameContact($params["fullname"], $params["email"], $params["companyname"], $params["address1"], $params["address2"], $params["city"],
        $params["state"], $params["postcode"], $params["countrycode"], $phone[0] ? $phone[0] : null,
        isset($phone[1]) ? $phone[1] : null);


    $adminPhone = explode(".", $params["adminfullphonenumber"]);

    $adminContact = new DomainNameContact($params["adminfirstname"] . " " . $params["adminlastname"], $params["adminemail"], $params["admincompanyname"], $params["adminaddress1"],
        $params["adminaddress2"], $params["admincity"], $params["adminstate"], $params["adminpostcode"], $params["admincountrycode"],
        $adminPhone[0] ? $adminPhone[0] : null,
        isset($adminPhone[1]) ? $adminPhone[1] : null);


    $privacyProxy = (bool)$params['idprotection'] ? 1 : 2;

    // Create the structure we need
    $domainRegistrationDescriptor = new DomainNameCreateDescriptor($domainNames, $params['regperiod'], $ownerContact, $nameservers, $adminContact, $adminContact, $adminContact, $privacyProxy, false);


    try {

        // Create the domain using the API.
        $api = netistrar_GetAPIInstance($params);
        $transaction = $api->domains()->create($domainRegistrationDescriptor);

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            throw new Exception("An unexpected error occurred processing this registration.");
        }

        logModuleCall("netistrar", "Register Domain", $domainRegistrationDescriptor, $transaction);


        return array(
            'success' => true,
        );

    } catch (\Exception $e) {

        logModuleCall("netistrar", "Register Domain", $domainRegistrationDescriptor, $e);

        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Initiate domain transfer.
 *
 * Attempt to create a domain transfer request for a given domain.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain transfer order
 * * When a pending domain transfer order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_TransferDomain($params) {

    // Create the domain names structure.
    $domainName = $params['sld'] . "." . $params['tld'];
    $eppCode = $params['eppcode'];

    logModuleCall("netistrar", "EPPCODE", $params, $eppCode);

    // Construct nameservers
    $nameservers = array();
    if ($params["ns1"]) $nameservers[] = $params["ns1"];
    if ($params["ns2"]) $nameservers[] = $params["ns2"];
    if ($params["ns3"]) $nameservers[] = $params["ns3"];
    if ($params["ns4"]) $nameservers[] = $params["ns4"];
    if ($params["ns5"]) $nameservers[] = $params["ns5"];

    $phone = explode(".", $params["fullphonenumber"]);

    $ownerContact = new DomainNameContact($params["fullname"], $params["email"], $params["companyname"], $params["address1"], $params["address2"], $params["city"],
        $params["state"], $params["postcode"], $params["countrycode"], $phone[0] ? $phone[0] : null,
        isset($phone[1]) ? $phone[1] : null);


    $adminPhone = explode(".", $params["adminfullphonenumber"]);

    $adminContact = new DomainNameContact($params["adminfirstname"] . " " . $params["adminlastname"], $params["adminemail"], $params["admincompanyname"], $params["adminaddress1"],
        $params["adminaddress2"], $params["admincity"], $params["adminstate"], $params["adminpostcode"], $params["admincountrycode"],
        $adminPhone[0] ? $adminPhone[0] : null,
        isset($adminPhone[1]) ? $adminPhone[1] : null);


    $privacyProxy = (bool)$params['idprotection'] ? 1 : 2;


    $domainTransferDescriptor = new DomainNameTransferDescriptor(array($domainName . "," . $eppCode), $ownerContact, $adminContact, $adminContact, $adminContact, $privacyProxy);


    try {

        // Create the domain using the API.
        $api = netistrar_GetAPIInstance($params);
        $transaction = $api->domains()->transferCreate($domainTransferDescriptor);

        logModuleCall("netistrar", "Transfer Domain", $domainTransferDescriptor, $transaction);

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            $elementErrors = array_values($transaction->getTransactionElements()[$params['sld'] . "." . $params['tld']]->getElementErrors());
            throw new Exception($elementErrors[0]->getMessage());
        }

        return array(
            'success' => true,
        );

    } catch (\Exception $e) {

        logModuleCall("netistrar", "Transfer Domain", $domainTransferDescriptor, $e);

        return array(
            'error' => $e->getMessage(),
        );
    }


}

/**
 * Renew a domain.
 *
 * Attempt to renew/extend a domain for a given number of years.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain renewal order
 * * When a pending domain renewal order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_RenewDomain($params) {

    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {
        $transaction = $api->domains()->renew($params['sld'] . "." . $params['tld'], $params["regperiod"]);

        logModuleCall("netistrar", "Renew Domain", $params['sld'] . "." . $params['tld'] . " -> " . $params["regperiod"] . " yrs", $transaction);

        return array(
            'success' => true,
        );

    } catch (\Exception $e) {
        logModuleCall("netistrar", "Renew Domain", $params['sld'] . "." . $params['tld'] . " -> " . $params["regperiod"] . " yrs", $e);

        return array(
            'error' => $e->getMessage(),
        );
    }


}


/**
 * Combined get domain information method for returning core info about a domain.
 *
 * @param $params
 */
function netistrar_GetDomainInformation($params) {


    try {

        // Api instance
        $api = netistrar_GetAPIInstance($params);

        $info = $api->domains()->get($params['sld'] . "." . $params['tld']);

        logModuleCall("netistrar", "Get Domain Info", $params['sld'] . "." . $params['tld'], $info);

        $expiryDate = date_create_from_format("d/m/Y H:i:s", $info->getExpiryDate());
        $expiryDate = WHMCS\Carbon::createFromDate($expiryDate->format("Y"), $expiryDate->format("m"), $expiryDate->format("d"));

        $lockedUntil = null;
        if ($info->getLockedUntil()) {
            $lockedUntil = date_create_from_format("d/m/Y H:i:s", $info->getLockedUntil());
            $lockedUntil = WHMCS\Carbon::createFromDate($lockedUntil->format("Y"), $lockedUntil->format("m"), $lockedUntil->format("d"));
        }

        $pendingUntil = null;
        if ($info->getOwnerContact()->getPendingContact()) {
            $pendingUntil = new DateTime();
            $pendingUntil->add(new DateInterval("P1W"));
            $pendingUntil = WHMCS\Carbon::createFromDate($pendingUntil->format("Y"), $pendingUntil->format("m"), $pendingUntil->format("d"));
        }


        return (new Domain)
            ->setDomain($info->getDomainName())
            ->setNameservers($info->getNameservers())
            ->setRegistrationStatus($info->getStatus())
            ->setTransferLock($info->getLocked())
            ->setTransferLockExpiryDate($lockedUntil)
            ->setExpiryDate($expiryDate)
            ->setIdProtectionStatus($info->getPrivacyProxy() == 1)
            ->setIsIrtpEnabled($info->getLocked() ? true : false)
            ->setIrtpTransferLock($info->getLocked() ? true : false)
            ->setDomainContactChangePending($info->getOwnerContact()->getPendingContact() ? true : false)
            ->setDomainContactChangeExpiryDate($pendingUntil)
            ->setRegistrantEmailAddress($info->getOwnerContact()->getEmailAddress())
            ->setIrtpVerificationTriggerFields(
                [
                    'Registrant' => [
                        'First Name',
                        'Last Name',
                        'Organization Name',
                        'Email',
                    ],
                ]
            );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "Get Domain Info", $params['sld'] . "." . $params['tld'] . " -> " . $params["regperiod"] . " yrs", $e);

        $message = $e->getMessage();
        $reason = "UNKNOWN";
        if ($e instanceof TransactionException) {

            // Store the first reason.
            $reason = array_keys($e->getTransactionErrors());
            $reason = array_shift($reason);

            $transactionErrors = array_values($e->getTransactionErrors());
            $message = $transactionErrors[0]->getMessage();

        }

        return array(
            'error' => $message,
            'reason' => $reason
        );
    }


}


/**
 * Save nameserver changes.
 *
 * This function should submit a change of nameservers request to the
 * domain registrar.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_SaveNameservers($params) {

    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {

        // Construct nameservers
        $nameservers = array();
        if ($params["ns1"]) $nameservers[] = $params["ns1"];
        if ($params["ns2"]) $nameservers[] = $params["ns2"];
        if ($params["ns3"]) $nameservers[] = $params["ns3"];
        if ($params["ns4"]) $nameservers[] = $params["ns4"];
        if ($params["ns5"]) $nameservers[] = $params["ns5"];

        // Update the domain with new details.
        $domainNameUpdateDescriptor = new DomainNameUpdateDescriptor(array($params['sld'] . "." . $params['tld']), null, null, null, null, $nameservers);
        $transaction = $api->domains()->update($domainNameUpdateDescriptor);

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            throw new Exception("An unexpected error occurred processing this contact update.");
        }

        logModuleCall("netistrar", "Save Nameservers", $domainNameUpdateDescriptor, $transaction);

        return array(
            'success' => true,
        );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "Save Nameservers", $domainNameUpdateDescriptor, $e);

        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Get the current WHOIS Contact Information.
 *
 * Should return a multi-level array of the contacts and name/address
 * fields that be modified.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_GetContactDetails($params) {


    try {

        // Api instance
        $api = netistrar_GetAPIInstance($params);
        $info = $api->domains()->get($params['sld'] . "." . $params['tld']);

        $owner = explode(" ", $info->getOwnerContact()->getName());
        $technical = explode(" ", $info->getTechnicalContact()->getName());
        $billing = explode(" ", $info->getBillingContact()->getName());
        $admin = explode(" ", $info->getAdminContact()->getName());


        return array(
            'Registrant' => array(
                'First Name' => $owner[0],
                'Last Name' => isset($owner[1]) ? $owner[1] : "",
                'Company Name' => $info->getOwnerContact()->getOrganisation(),
                'Email Address' => $info->getOwnerContact()->getEmailAddress(),
                'Address 1' => $info->getOwnerContact()->getStreet1(),
                'Address 2' => $info->getOwnerContact()->getStreet2(),
                'City' => $info->getOwnerContact()->getCity(),
                'State' => $info->getOwnerContact()->getCounty(),
                'Postcode' => $info->getOwnerContact()->getPostcode(),
                'Country' => $info->getOwnerContact()->getCountry(),
                'Phone Number' => $info->getOwnerContact()->getTelephone() ? $info->getOwnerContact()->getTelephoneDiallingCode() . "." . $info->getOwnerContact()->getTelephone() : "",
                'Fax Number' => $info->getOwnerContact()->getFax() ? $info->getOwnerContact()->getFaxDiallingCode() . "." . $info->getOwnerContact()->getFax() : "",
            ),
            'Technical' => array(
                'First Name' => $technical[0],
                'Last Name' => isset($technical[1]) ? $technical[1] : "",
                'Company Name' => $info->getTechnicalContact()->getOrganisation(),
                'Email Address' => $info->getTechnicalContact()->getEmailAddress(),
                'Address 1' => $info->getTechnicalContact()->getStreet1(),
                'Address 2' => $info->getTechnicalContact()->getStreet2(),
                'City' => $info->getTechnicalContact()->getCity(),
                'State' => $info->getTechnicalContact()->getCounty(),
                'Postcode' => $info->getTechnicalContact()->getPostcode(),
                'Country' => $info->getTechnicalContact()->getCountry(),
                'Phone Number' => $info->getTechnicalContact()->getTelephone() ? $info->getTechnicalContact()->getTelephoneDiallingCode() . "." . $info->getTechnicalContact()->getTelephone() : "",
                'Fax Number' => $info->getTechnicalContact()->getFax() ? $info->getTechnicalContact()->getFaxDiallingCode() . "." . $info->getTechnicalContact()->getFax() : "",
            ),
            'Billing' => array(
                'First Name' => $billing[0],
                'Last Name' => isset($billing[1]) ? $billing[1] : "",
                'Company Name' => $info->getBillingContact()->getOrganisation(),
                'Email Address' => $info->getBillingContact()->getEmailAddress(),
                'Address 1' => $info->getBillingContact()->getStreet1(),
                'Address 2' => $info->getBillingContact()->getStreet2(),
                'City' => $info->getBillingContact()->getCity(),
                'State' => $info->getBillingContact()->getCounty(),
                'Postcode' => $info->getBillingContact()->getPostcode(),
                'Country' => $info->getBillingContact()->getCountry(),
                'Phone Number' => $info->getBillingContact()->getTelephone() ? $info->getBillingContact()->getTelephoneDiallingCode() . "." . $info->getBillingContact()->getTelephone() : "",
                'Fax Number' => $info->getBillingContact()->getFax() ? $info->getBillingContact()->getFaxDiallingCode() . "." . $info->getBillingContact()->getFax() : "",
            ),
            'Admin' => array(
                'First Name' => $admin[0],
                'Last Name' => isset($admin[1]) ? $admin[1] : "",
                'Company Name' => $info->getAdminContact()->getOrganisation(),
                'Email Address' => $info->getAdminContact()->getEmailAddress(),
                'Address 1' => $info->getAdminContact()->getStreet1(),
                'Address 2' => $info->getAdminContact()->getStreet2(),
                'City' => $info->getAdminContact()->getCity(),
                'State' => $info->getAdminContact()->getCounty(),
                'Postcode' => $info->getAdminContact()->getPostcode(),
                'Country' => $info->getAdminContact()->getCountry(),
                'Phone Number' => $info->getAdminContact()->getTelephone() ? $info->getAdminContact()->getTelephoneDiallingCode() . "." . $info->getAdminContact()->getTelephone() : "",
                'Fax Number' => $info->getAdminContact()->getFax() ? $info->getAdminContact()->getFaxDiallingCode() . "." . $info->getAdminContact()->getFax() : "",
            ),
        );

    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Update the WHOIS Contact Information for a given domain.
 *
 * Called when a change of WHOIS Information is requested within WHMCS.
 * Receives an array matching the format provided via the `GetContactDetails`
 * method with the values from the users input.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_SaveContactDetails($params) {


    try {

        // whois information
        $contactDetails = $params['contactdetails'];

        // Get the API
        $api = netistrar_GetAPIInstance($params);

        // Grab the domains
        $domain = $api->domains()->get($params['sld'] . "." . $params['tld']);

        $ownerContact = $domain->getOwnerContact();
        $adminContact = $domain->getAdminContact();
        $billingContact = $domain->getBillingContact();
        $technicalContact = $domain->getTechnicalContact();


        $ownerContact->setName($contactDetails['Registrant']['First Name'] . " " . $contactDetails['Registrant']['Last Name']);
        $ownerContact->setOrganisation($contactDetails['Registrant']['Company Name']);
        $ownerContact->setEmailAddress($contactDetails['Registrant']['Email Address']);
        $ownerContact->setStreet1($contactDetails['Registrant']['Address 1']);
        $ownerContact->setStreet2($contactDetails['Registrant']['Address 2']);
        $ownerContact->setCity($contactDetails['Registrant']['City']);
        $ownerContact->setCounty($contactDetails['Registrant']['State']);
        $ownerContact->setPostcode($contactDetails['Registrant']['Postcode']);
        $ownerContact->setCountry($contactDetails['Registrant']['Country']);

        if ($contactDetails['Registrant']['Phone Number']) {
            $splitPhoneNumber = explode(".", $contactDetails['Registrant']['Phone Number']);
            $ownerContact->setTelephoneDiallingCode($splitPhoneNumber[0]);
            $ownerContact->setTelephone($splitPhoneNumber[1]);
        }

        if ($contactDetails['Registrant']['Fax Number']) {
            $splitFaxNumber = explode(".", $contactDetails['Registrant']['Fax Number']);
            $ownerContact->setFaxDiallingCode($splitFaxNumber[0]);
            $ownerContact->setFax($splitFaxNumber[1]);
        }


        $adminContact->setName($contactDetails['Admin']['First Name'] . " " . $contactDetails['Admin']['Last Name']);
        $adminContact->setOrganisation($contactDetails['Admin']['Company Name']);
        $adminContact->setEmailAddress($contactDetails['Admin']['Email Address']);
        $adminContact->setStreet1($contactDetails['Admin']['Address 1']);
        $adminContact->setStreet2($contactDetails['Admin']['Address 2']);
        $adminContact->setCity($contactDetails['Admin']['City']);
        $adminContact->setCounty($contactDetails['Admin']['State']);
        $adminContact->setPostcode($contactDetails['Admin']['Postcode']);
        $adminContact->setCountry($contactDetails['Admin']['Country']);

        if ($contactDetails['Admin']['Phone Number']) {
            $splitPhoneNumber = explode(".", $contactDetails['Admin']['Phone Number']);
            $adminContact->setTelephoneDiallingCode($splitPhoneNumber[0]);
            $adminContact->setTelephone($splitPhoneNumber[1]);
        }

        if ($contactDetails['Admin']['Fax Number']) {
            $splitFaxNumber = explode(".", $contactDetails['Admin']['Fax Number']);
            $adminContact->setFaxDiallingCode($splitFaxNumber[0]);
            $adminContact->setFax($splitFaxNumber[1]);
        }


        $technicalContact->setName($contactDetails['Technical']['First Name'] . " " . $contactDetails['Technical']['Last Name']);
        $technicalContact->setOrganisation($contactDetails['Technical']['Company Name']);
        $technicalContact->setEmailAddress($contactDetails['Technical']['Email Address']);
        $technicalContact->setStreet1($contactDetails['Technical']['Address 1']);
        $technicalContact->setStreet2($contactDetails['Technical']['Address 2']);
        $technicalContact->setCity($contactDetails['Technical']['City']);
        $technicalContact->setCounty($contactDetails['Technical']['State']);
        $technicalContact->setPostcode($contactDetails['Technical']['Postcode']);
        $technicalContact->setCountry($contactDetails['Technical']['Country']);

        if ($contactDetails['Technical']['Phone Number']) {
            $splitPhoneNumber = explode(".", $contactDetails['Technical']['Phone Number']);
            $technicalContact->setTelephoneDiallingCode($splitPhoneNumber[0]);
            $technicalContact->setTelephone($splitPhoneNumber[1]);
        }

        if ($contactDetails['Technical']['Fax Number']) {
            $splitFaxNumber = explode(".", $contactDetails['Technical']['Fax Number']);
            $technicalContact->setFaxDiallingCode($splitFaxNumber[0]);
            $technicalContact->setFax($splitFaxNumber[1]);
        }


        $billingContact->setName($contactDetails['Billing']['First Name'] . " " . $contactDetails['Billing']['Last Name']);
        $billingContact->setOrganisation($contactDetails['Billing']['Company Name']);
        $billingContact->setEmailAddress($contactDetails['Billing']['Email Address']);
        $billingContact->setStreet1($contactDetails['Billing']['Address 1']);
        $billingContact->setStreet2($contactDetails['Billing']['Address 2']);
        $billingContact->setCity($contactDetails['Billing']['City']);
        $billingContact->setCounty($contactDetails['Billing']['State']);
        $billingContact->setPostcode($contactDetails['Billing']['Postcode']);
        $billingContact->setCountry($contactDetails['Billing']['Country']);

        if ($contactDetails['Billing']['Phone Number']) {
            $splitPhoneNumber = explode(".", $contactDetails['Billing']['Phone Number']);
            $billingContact->setTelephoneDiallingCode($splitPhoneNumber[0]);
            $billingContact->setTelephone($splitPhoneNumber[1]);
        }

        if ($contactDetails['Billing']['Fax Number']) {
            $splitFaxNumber = explode(".", $contactDetails['Billing']['Fax Number']);
            $billingContact->setFaxDiallingCode($splitFaxNumber[0]);
            $billingContact->setFax($splitFaxNumber[1]);
        }


        // Update the domain with new details.
        $domainNameUpdateDescriptor = new DomainNameUpdateDescriptor(array($domain->getDomainName()), $ownerContact, $adminContact, $billingContact, $technicalContact);
        $transaction = $api->domains()->update($domainNameUpdateDescriptor);

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            $elementErrors = array_values($transaction->getTransactionElements()[$params['sld'] . "." . $params['tld']]->getElementErrors());
            throw new Exception($elementErrors[0]->getMessage());
        }

        logModuleCall("netistrar", "Save Contact Details", $domainNameUpdateDescriptor, $transaction);

        return array(
            'success' => true,
        );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "Save Contact Details", $domainNameUpdateDescriptor, $e);

        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Check Domain Availability.
 *
 * Determine if a domain or group of domains are available for
 * registration or transfer.
 *
 * @param array $params common module parameters
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @see \WHMCS\Domains\DomainLookup\SearchResult
 * @see \WHMCS\Domains\DomainLookup\ResultsList
 *
 * @throws Exception Upon domain availability check failure.
 *
 * @return \WHMCS\Domains\DomainLookup\ResultsList An ArrayObject based collection of \WHMCS\Domains\DomainLookup\SearchResult results
 */
function netistrar_CheckAvailability($params) {

    // availability check parameters
    $searchTerm = $params['searchTerm'];

    $tldsToInclude = netistrar_PrepareTLDsForAPICall($params['tldsToInclude']);


    try {

        $apiProvider = netistrar_GetAPIInstance($params);
        $availability = $apiProvider->domains()->hintedAvailability(new DomainNameAvailabilityDescriptor($searchTerm, null, $tldsToInclude));


        $results = new ResultsList();
        foreach ($availability->getTldResults() as $tld => $result) {

            // Convert an API availability result to a search result.
            $searchResult = netistrar_ConvertAPIAvailabilityResultToSearchResult($result);

            // Append to the search results list
            $results->append($searchResult);
        }

        return $results;

    } catch (\Exception $e) {

        return array(
            'error' => $e->getMessage(),
        );
    }
}


/**
 * Domain Suggestion Settings.
 *
 * Defines the settings relating to domain suggestions (optional).
 * It follows the same convention as `getConfigArray`.
 *
 * @see https://developers.whmcs.com/domain-registrars/check-availability/
 *
 * @return array of Configuration Options
 */
function netistrar_DomainSuggestionOptions() {
    return array();
}

/**
 * Get Domain Suggestions.
 *
 * Provide domain suggestions based on the domain lookup term provided.
 *
 * @param array $params common module parameters
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @see \WHMCS\Domains\DomainLookup\SearchResult
 * @see \WHMCS\Domains\DomainLookup\ResultsList
 *
 * @throws Exception Upon domain suggestions check failure.
 *
 * @return \WHMCS\Domains\DomainLookup\ResultsList An ArrayObject based collection of \WHMCS\Domains\DomainLookup\SearchResult results
 */
function netistrar_GetDomainSuggestions($params) {


    // availability check parameters
    $searchTerm = $params['searchTerm'];
    $tldsToInclude = netistrar_PrepareTLDsForAPICall($params['tldsToInclude']);


    try {

        $apiProvider = netistrar_GetAPIInstance($params);
        $availabilityDescriptor = new DomainNameAvailabilityDescriptor($searchTerm, null, $tldsToInclude, true);

        $availability = $apiProvider->domains()->hintedAvailability($availabilityDescriptor);


        $results = new ResultsList();
        foreach ($availability->getSuggestions() as $suggestion) {
            $searchResult = netistrar_ConvertAPIAvailabilityResultToSearchResult($suggestion);
            $results->append($searchResult);
        }

        return $results;

    } catch (\Exception $e) {


        file_put_contents("/var/www/ping", var_export($e, true));

        return array(
            'error' => $e->getMessage(),
        );
    }

}


/**
 * Set registrar lock status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_SaveRegistrarLock($params) {

    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {

        // id protection parameter
        $lockStatus = $params['lockenabled'];

        // Update the domain with new details.
        $domainNameUpdateDescriptor = new DomainNameUpdateDescriptor(array($params['sld'] . "." . $params['tld']), null, null, null, null, null, $lockStatus);
        $transaction = $api->domains()->update($domainNameUpdateDescriptor);

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            $elementErrors = array_values($transaction->getTransactionElements()[$params['sld'] . "." . $params['tld']]->getElementErrors());
            throw new Exception($elementErrors[0]->getMessage());
        }

        logModuleCall("netistrar", "Registrar Lock Update", $domainNameUpdateDescriptor, $transaction);

        return array(
            'success' => true,
        );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "Registrar Lock Update", $domainNameUpdateDescriptor, $e);

        return array(
            'error' => $e->getMessage(),
        );
    }


}

/**
 * Enable/Disable ID Protection.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_IDProtectToggle($params) {


    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {

        // id protection parameter
        $ppSetting = (bool)$params['protectenable'] ? 1 : 2;

        // Update the domain with new details.
        $domainNameUpdateDescriptor = new DomainNameUpdateDescriptor(array($params['sld'] . "." . $params['tld']), null, null, null, null, null, null, $ppSetting);
        $transaction = $api->domains()->update($domainNameUpdateDescriptor);

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            $elementErrors = array_values($transaction->getTransactionElements()[$params['sld'] . "." . $params['tld']]->getElementErrors());
            throw new Exception($elementErrors[0]->getMessage());
        }

        logModuleCall("netistrar", "ID Protection Toggle", $domainNameUpdateDescriptor, $transaction);

        return array(
            'success' => true,
        );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "ID Protection Toggle", $domainNameUpdateDescriptor, $e);

        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Request EEP Code.
 *
 * Supports both displaying the EPP Code directly to a user or indicating
 * that the EPP Code will be emailed to the registrant.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 *
 */
function netistrar_GetEPPCode($params) {


    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {

        $info = $api->domains()->get($params['sld'] . "." . $params['tld']);

        logModuleCall("netistrar", "Get Domain Info", $params['sld'] . "." . $params['tld'], $info);

        if ($info->getLocked()) {

            return array('error' => "This domain is currently locked for transfer");

        } else {
            return array(
                'eppcode' => $info->getAuthCode()
            );

        }

    } catch (\Exception $e) {

        logModuleCall("netistrar", "Get Domain Info", $params['sld'] . "." . $params['tld'], $e);

        return array(
            'error' => $e->getMessage(),
        );
    }


    // user defined configuration values
    $userIdentifier = $params['API Username'];
    $apiKey = $params['API Key'];
    $testMode = $params['Test Mode'];
    $accountMode = $params['Account Mode'];
    $emailPreference = $params['Email Preference'];
    $additionalInfo = $params['Additional Information'];

    // domain parameters
    $sld = $params['sld'];
    $tld = $params['tld'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
    );

    try {
        $api = new ApiClient();
        $api->call('RequestEPPCode', $postfields);

        if ($api->getFromResponse('eppcode')) {
            // If EPP Code is returned, return it for display to the end user
            return array(
                'eppcode' => $api->getFromResponse('eppcode'),
            );
        } else {
            // If EPP Code is not returned, it was sent by email, return success
            return array(
                'success' => 'success',
            );
        }

    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Release a Domain.
 *
 * Used to initiate a transfer out such as an IPSTAG change for .UK
 * domain names.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_ReleaseDomain($params) {
    // user defined configuration values
    $userIdentifier = $params['API Username'];
    $apiKey = $params['API Key'];
    $testMode = $params['Test Mode'];
    $accountMode = $params['Account Mode'];
    $emailPreference = $params['Email Preference'];
    $additionalInfo = $params['Additional Information'];

    // domain parameters
    $sld = $params['sld'];
    $tld = $params['tld'];

    // transfer tag
    $transferTag = $params['transfertag'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'newtag' => $transferTag,
    );

    try {
        $api = new ApiClient();
        $api->call('ReleaseDomain', $postfields);

        return array(
            'success' => 'success',
        );

    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}


/**
 * Register a Nameserver.
 *
 * Adds a child nameserver for the given domain name.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_RegisterNameserver($params) {

    // nameserver parameters
    $nameserver = $params['nameserver'];
    $prefix = str_replace("." . $params['sld'] . "." . $params['tld'], "", $nameserver);

    $ipAddress = $params['ipaddress'];


    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {


        $transaction = $api->domains()->glueRecordsSet($params['sld'] . "." . $params['tld'],
            array(new DomainNameGlueRecord($prefix, $ipAddress)));

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            $elementErrors = array_values($transaction->getTransactionElements()[$prefix]->getElementErrors());
            throw new Exception(str_ireplace("glue record", "private nameserver", $elementErrors[0]->getMessage()));
        }

        logModuleCall("netistrar", "Register Child Nameserver", new DomainNameGlueRecord($prefix, $ipAddress), $transaction);

        return array(
            'success' => true,
        );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "Register Child Nameserver", new DomainNameGlueRecord($prefix, $ipAddress), $e);

        return array(
            'error' => $e->getMessage(),
        );
    }


}

/**
 * Modify a Nameserver.
 *
 * Modifies the IP of a child nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_ModifyNameserver($params) {

    $params["ipaddress"] = $params["newipaddress"];
    return netistrar_RegisterNameserver($params);

}

/**
 * Delete a Nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_DeleteNameserver($params) {

    // nameserver parameters
    $nameserver = $params['nameserver'];
    $prefix = str_replace("." . $params['sld'] . "." . $params['tld'], "", $nameserver);


    // Api instance
    $api = netistrar_GetAPIInstance($params);

    try {

        $transaction = $api->domains()->glueRecordsRemove($params['sld'] . "." . $params['tld'],
            array($prefix));

        if ($transaction->getTransactionStatus() != "SUCCEEDED") {
            $elementErrors = array_values($transaction->getTransactionElements()[$prefix]->getElementErrors());
            throw new Exception(str_ireplace("glue record", "private nameserver", $elementErrors[0]->getMessage()));
        }

        logModuleCall("netistrar", "Remove Child Nameserver", new DomainNameGlueRecord($prefix), $transaction);

        return array(
            'success' => true,
        );


    } catch (\Exception $e) {

        logModuleCall("netistrar", "Remove Child Nameserver", new DomainNameGlueRecord($prefix), $e);

        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Sync Domain Status & Expiration Date.
 *
 * Domain syncing is intended to ensure domain status and expiry date
 * changes made directly at the domain registrar are synced to WHMCS.
 * It is called periodically for a domain.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_Sync($params) {


    /**
     * Get domain info for the domain passed
     */
    $domainInfo = netistrar_GetDomainInformation($params);

    $returnArray = array("expirydate" => "", "active" => true, "expired" => false, "transferredAway" => false);

    // Grab the registration status.
    if ($domainInfo instanceof Domain) {
        $status = $domainInfo->getRegistrationStatus();

        $returnArray["expirydate"] = $domainInfo->getExpiryDate();

        if ($status != "ACTIVE") {
            $returnArray["active"] = false;
        }

        if ($status == "EXPIRED" || $status == "RGP") {
            $returnArray["expired"] = true;
        }

    } else if (is_array($domainInfo)) {

        if ($domainInfo["reason"] == "DOMAIN_NOT_IN_ACCOUNT") {
            $returnArray["transferredAway"] = true;
        } else {
            return $domainInfo;
        }

    }

    return $returnArray;

}

/**
 * Incoming Domain Transfer Sync.
 *
 * Check status of incoming domain transfers and notify end-user upon
 * completion. This function is called daily for incoming domains.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_TransferSync($params) {

    /**
     * Get domain info for the domain passed
     */
    $domainInfo = netistrar_GetDomainInformation($params);

    // Grab the registration status.
    if ($domainInfo instanceof Domain) {
        $status = $domainInfo->getRegistrationStatus();

        // Return completion when active
        if ($status == "ACTIVE") {
            return array("completed" => true, "expirydate" => $domainInfo->getExpiryDate());
        } else {
            // No status change
            return array();
        }


    } else if (is_array($domainInfo)) {

        if ($domainInfo["reason"] == "DOMAIN_NOT_IN_ACCOUNT") {
            return array("failed" => true, "reason" => "Transfer was rejected / cancelled");
        } else {
            return $domainInfo;
        }
    }


}


/**
 * Get a Netistrar API Instance from params
 *
 * @param $params
 */
function netistrar_GetAPIInstance($params) {
    $apiKey = $params["apiKey"];
    $apiSecret = $params["apiSecret"];
    $environment = $params["environment"];

    $url = "";
    switch ($environment) {
        case "Development":
            $url = "http://restapi.netistrar.test";
            break;
        case "OTE":
            $url = "https://restapi.netistrar-ote.uk";
            break;
        case "Production":
            $url = "https://restapi.netistrar.com";
            break;
    }

    return new APIProvider($url, $apiKey, $apiSecret);
}

/**
 * @param $tldsToInclude
 * @return mixed
 */
function netistrar_PrepareTLDsForAPICall($tldsToInclude) {
    foreach ($tldsToInclude as $index => $tldToInclude) {
        $tldsToInclude[$index] = trim($tldsToInclude[$index], ".");
    }
    return $tldsToInclude;
}


/**
 * @param $result
 * @return SearchResult
 */
function netistrar_ConvertAPIAvailabilityResultToSearchResult($result) {
    $explodedDomainName = explode(".", $result->getDomainName());
    $prefix = array_shift($explodedDomainName);

    // Instantiate a new domain search result object
    $searchResult = new SearchResult($prefix, join(".", $explodedDomainName));

    // Determine the appropriate status to return
    if ($result->getAvailability() == 'AVAILABLE') {
        $status = SearchResult::STATUS_NOT_REGISTERED;
    } elseif ($result->getAvailability() == 'UNAVAILABLE') {
        $status = SearchResult::STATUS_REGISTERED;
    } else {
        $status = SearchResult::STATUS_TLD_NOT_SUPPORTED;

    }
    $searchResult->setStatus($status);
    return $searchResult;
}
