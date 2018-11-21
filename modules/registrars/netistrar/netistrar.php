<?php
/**
 * Netistrar WHMCS Reseller Registry Module.
 *
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Netistrar\ClientAPI\APIProvider;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameAvailabilityDescriptor;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameCreateDescriptor;
use Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameUpdateDescriptor;
use Netistrar\ClientAPI\Objects\Domain\DomainNameContact;
use WHMCS\Domain\Registrar\Domain;
use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Module\Registrar\Registrarmodule\ApiClient;

include __DIR__ . "/lib/autoloader.php";


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
            "Options" => "Development,OTE,Production",
            "Description" => "Which environment to connect to",
            "Default" => "Development",
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
    // user defined configuration values
    $userIdentifier = $params['API Username'];
    $apiKey = $params['API Key'];
    $testMode = $params['Test Mode'];
    $accountMode = $params['Account Mode'];
    $emailPreference = $params['Email Preference'];
    $additionalInfo = $params['Additional Information'];

    // registration parameters
    $sld = $params['sld'];
    $tld = $params['tld'];
    $registrationPeriod = $params['regperiod'];
    $eppCode = $params['eppcode'];

    /**
     * Nameservers.
     *
     * If purchased with web hosting, values will be taken from the
     * assigned web hosting server. Otherwise uses the values specified
     * during the order process.
     */
    $nameserver1 = $params['ns1'];
    $nameserver2 = $params['ns2'];
    $nameserver3 = $params['ns3'];
    $nameserver4 = $params['ns4'];
    $nameserver5 = $params['ns5'];

    // registrant information
    $firstName = $params["firstname"];
    $lastName = $params["lastname"];
    $fullName = $params["fullname"]; // First name and last name combined
    $companyName = $params["companyname"];
    $email = $params["email"];
    $address1 = $params["address1"];
    $address2 = $params["address2"];
    $city = $params["city"];
    $state = $params["state"]; // eg. TX
    $stateFullName = $params["fullstate"]; // eg. Texas
    $postcode = $params["postcode"]; // Postcode/Zip code
    $countryCode = $params["countrycode"]; // eg. GB
    $countryName = $params["countryname"]; // eg. United Kingdom
    $phoneNumber = $params["phonenumber"]; // Phone number as the user provided it
    $phoneCountryCode = $params["phonecc"]; // Country code determined based on country
    $phoneNumberFormatted = $params["fullphonenumber"]; // Format: +CC.xxxxxxxxxxxx

    /**
     * Admin contact information.
     *
     * Defaults to the same as the client information. Can be configured
     * to use the web hosts details if the `Use Clients Details` option
     * is disabled in Setup > General Settings > Domains.
     */
    $adminFirstName = $params["adminfirstname"];
    $adminLastName = $params["adminlastname"];
    $adminCompanyName = $params["admincompanyname"];
    $adminEmail = $params["adminemail"];
    $adminAddress1 = $params["adminaddress1"];
    $adminAddress2 = $params["adminaddress2"];
    $adminCity = $params["admincity"];
    $adminState = $params["adminstate"]; // eg. TX
    $adminStateFull = $params["adminfullstate"]; // eg. Texas
    $adminPostcode = $params["adminpostcode"]; // Postcode/Zip code
    $adminCountry = $params["admincountry"]; // eg. GB
    $adminPhoneNumber = $params["adminphonenumber"]; // Phone number as the user provided it
    $adminPhoneNumberFormatted = $params["adminfullphonenumber"]; // Format: +CC.xxxxxxxxxxxx

    // domain addon purchase status
    $enableDnsManagement = (bool)$params['dnsmanagement'];
    $enableEmailForwarding = (bool)$params['emailforwarding'];
    $enableIdProtection = (bool)$params['idprotection'];

    /**
     * Premium domain parameters.
     *
     * Premium domains enabled informs you if the admin user has enabled
     * the selling of premium domain names. If this domain is a premium name,
     * `premiumCost` will contain the cost price retrieved at the time of
     * the order being placed. The premium order should only be processed
     * if the cost price now matches that previously fetched amount.
     */
    $premiumDomainsEnabled = (bool)$params['premiumEnabled'];
    $premiumDomainsCost = $params['premiumCost'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'eppcode' => $eppCode,
        'nameservers' => array(
            'ns1' => $nameserver1,
            'ns2' => $nameserver2,
            'ns3' => $nameserver3,
            'ns4' => $nameserver4,
            'ns5' => $nameserver5,
        ),
        'years' => $registrationPeriod,
        'contacts' => array(
            'registrant' => array(
                'firstname' => $firstName,
                'lastname' => $lastName,
                'companyname' => $companyName,
                'email' => $email,
                'address1' => $address1,
                'address2' => $address2,
                'city' => $city,
                'state' => $state,
                'zipcode' => $postcode,
                'country' => $countryCode,
                'phonenumber' => $phoneNumberFormatted,
            ),
            'tech' => array(
                'firstname' => $adminFirstName,
                'lastname' => $adminLastName,
                'companyname' => $adminCompanyName,
                'email' => $adminEmail,
                'address1' => $adminAddress1,
                'address2' => $adminAddress2,
                'city' => $adminCity,
                'state' => $adminState,
                'zipcode' => $adminPostcode,
                'country' => $adminCountry,
                'phonenumber' => $adminPhoneNumberFormatted,
            ),
        ),
        'dnsmanagement' => $enableDnsManagement,
        'emailforwarding' => $enableEmailForwarding,
        'idprotection' => $enableIdProtection,
    );

    try {
        $api = new ApiClient();
        $api->call('Transfer', $postfields);

        return array(
            'success' => true,
        );

    } catch (\Exception $e) {
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
    // user defined configuration values
    $userIdentifier = $params['API Username'];
    $apiKey = $params['API Key'];
    $testMode = $params['Test Mode'];
    $accountMode = $params['Account Mode'];
    $emailPreference = $params['Email Preference'];
    $additionalInfo = $params['Additional Information'];

    // registration parameters
    $sld = $params['sld'];
    $tld = $params['tld'];
    $registrationPeriod = $params['regperiod'];

    // domain addon purchase status
    $enableDnsManagement = (bool)$params['dnsmanagement'];
    $enableEmailForwarding = (bool)$params['emailforwarding'];
    $enableIdProtection = (bool)$params['idprotection'];

    /**
     * Premium domain parameters.
     *
     * Premium domains enabled informs you if the admin user has enabled
     * the selling of premium domain names. If this domain is a premium name,
     * `premiumCost` will contain the cost price retrieved at the time of
     * the order being placed. A premium renewal should only be processed
     * if the cost price now matches that previously fetched amount.
     */
    $premiumDomainsEnabled = (bool)$params['premiumEnabled'];
    $premiumDomainsCost = $params['premiumCost'];

    // Build post data.
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'years' => $registrationPeriod,
        'dnsmanagement' => $enableDnsManagement,
        'emailforwarding' => $enableEmailForwarding,
        'idprotection' => $enableIdProtection,
    );

    try {
        $api = new ApiClient();
        $api->call('Renew', $postfields);

        return array(
            'success' => true,
        );

    } catch (\Exception $e) {
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

    // Api instance
    $api = netistrar_GetAPIInstance($params);

    $info = $api->domains()->get($params['sld'] . "." . $params['tld']);


    $expiryDate = date_create_from_format("d/m/Y H:i:s", $info->getExpiryDate());
    $expiryDate = WHMCS\Carbon::createFromDate($expiryDate->format("Y"), $expiryDate->format("m"), $expiryDate->format("d"));

    $lockedUntil = null;
    if ($info->getLockedUntil()) {
        $lockedUntil = date_create_from_format("d/m/Y H:i:s", $info->getLockedUntil());
        $lockedUntil = WHMCS\Carbon::createFromDate($lockedUntil->format("Y"), $lockedUntil->format("m"), $lockedUntil->format("d"));

    }

    return (new Domain)
        ->setDomain($info->getDomainName())
        ->setNameservers($info->getNameservers())
        ->setRegistrationStatus($info->getStatus())
        ->setTransferLock($info->getLocked())
        ->setTransferLockExpiryDate($lockedUntil)
        ->setExpiryDate($expiryDate)
        ->setIdProtectionStatus($info->getPrivacyProxy() == 1)
        ->setDomainContactChangePending($info->getOwnerContact()->getPendingContact() ? true : false)
        ->setDomainContactChangeExpiryDate(WHMCS\Carbon::now())
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
            throw new Exception("An unexpected error occurred processing this contact update.");
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
        $availability = $apiProvider->domains()->hintedAvailability(new DomainNameAvailabilityDescriptor($searchTerm, null, $tldsToInclude, true));

        $results = new ResultsList();
        foreach ($tldsToInclude as $tld) {

            $suggestions = isset($availability->getSuggestions()[$tld]) ? $availability->getSuggestions()[$tld] : array();

            // Convert an API availability result to a search result.
            if (sizeof($suggestions) > 0) {
                $searchResult = netistrar_ConvertAPIAvailabilityResultToSearchResult($suggestions[0]);
                $results->append($searchResult);
            }

            if (sizeof($suggestions) > 1) {
                $searchResult = netistrar_ConvertAPIAvailabilityResultToSearchResult($suggestions[1]);
                $results->append($searchResult);
            }


        }

        return $results;

    } catch (\Exception $e) {

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

    // lock status
    $lockStatus = $params['lockenabled'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'registrarlock' => ($lockStatus == 'locked') ? 1 : 0,
    );

    try {
        $api = new ApiClient();
        $api->call('SetLockStatus', $postfields);

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
 * Get DNS Records for DNS Host Record Management.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array DNS Host Records
 */
function netistrar_GetDNS($params) {
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
        $api->call('GetDNSHostRecords', $postfields);

        $hostRecords = array();
        foreach ($api->getFromResponse('records') as $record) {
            $hostRecords[] = array(
                "hostname" => $record['name'], // eg. www
                "type" => $record['type'], // eg. A
                "address" => $record['address'], // eg. 10.0.0.1
                "priority" => $record['mxpref'], // eg. 10 (N/A for non-MX records)
            );
        }
        return $hostRecords;

    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
}

/**
 * Update DNS Host Records.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_SaveDNS($params) {
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

    // dns record parameters
    $dnsrecords = $params['dnsrecords'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'records' => $dnsrecords,
    );

    try {
        $api = new ApiClient();
        $api->call('GetDNSHostRecords', $postfields);

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
 * Enable/Disable ID Protection.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_IDProtectToggle($params) {
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

    // id protection parameter
    $protectEnable = (bool)$params['protectenable'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
    );

    try {
        $api = new ApiClient();

        if ($protectEnable) {
            $api->call('EnableIDProtection', $postfields);
        } else {
            $api->call('DisableIDProtection', $postfields);
        }

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

    // nameserver parameters
    $nameserver = $params['nameserver'];
    $ipAddress = $params['ipaddress'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'nameserver' => $nameserver,
        'ip' => $ipAddress,
    );

    try {
        $api = new ApiClient();
        $api->call('RegisterNameserver', $postfields);

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

    // nameserver parameters
    $nameserver = $params['nameserver'];
    $currentIpAddress = $params['currentipaddress'];
    $newIpAddress = $params['newipaddress'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'nameserver' => $nameserver,
        'currentip' => $currentIpAddress,
        'newip' => $newIpAddress,
    );

    try {
        $api = new ApiClient();
        $api->call('ModifyNameserver', $postfields);

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
 * Delete a Nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function netistrar_DeleteNameserver($params) {
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

    // nameserver parameters
    $nameserver = $params['nameserver'];

    // Build post data
    $postfields = array(
        'username' => $userIdentifier,
        'password' => $apiKey,
        'testmode' => $testMode,
        'domain' => $sld . '.' . $tld,
        'nameserver' => $nameserver,
    );

    try {
        $api = new ApiClient();
        $api->call('DeleteNameserver', $postfields);

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
        $api->call('GetDomainInfo', $postfields);

        return array(
            'expirydate' => $api->getFromResponse('expirydate'), // Format: YYYY-MM-DD
            'active' => (bool)$api->getFromResponse('active'), // Return true if the domain is active
            'expired' => (bool)$api->getFromResponse('expired'), // Return true if the domain has expired
            'transferredAway' => (bool)$api->getFromResponse('transferredaway'), // Return true if the domain is transferred out
        );

    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
    }
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
        $api->call('CheckDomainTransfer', $postfields);

        if ($api->getFromResponse('transfercomplete')) {
            return array(
                'completed' => true,
                'expirydate' => $api->getFromResponse('expirydate'), // Format: YYYY-MM-DD
            );
        } elseif ($api->getFromResponse('transferfailed')) {
            return array(
                'failed' => true,
                'reason' => $api->getFromResponse('failurereason'), // Reason for the transfer failure if available
            );
        } else {
            // No status change, return empty array
            return array();
        }

    } catch (\Exception $e) {
        return array(
            'error' => $e->getMessage(),
        );
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
            $url = "https://ote-restapi.netistrar.com";
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
