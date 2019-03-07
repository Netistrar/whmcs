<?php

namespace Netistrar\ClientAPI\Controllers;

use Kinikit\Core\Util\HTTP\WebServiceProxy;

/**
 * The Account API provides access to account functions including balances and notifications.
*/
class account extends WebServiceProxy {

    /**
     * Get current account balance for your reseller account in current working currency as defined in the control panel.
     *
     * @return float
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function balance(){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("balance", "GET", array(),null,"float",$expectedExceptions);
    }

    /**
     * List account notifications ordered by the latest first.  This can be optionally limited to a number of notifications
     * and one or more notification types (supplied as an array) for filtering.
     *
     * @param integer $maxResults
     * @param string $types
     * @param string $subTypes
     * @return \Netistrar\ClientAPI\Objects\Account\AccountNotification[]
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function listNotifications($maxResults = 50, $types = "", $subTypes = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("notification", "GET", array("maxResults" => $maxResults, "types" => $types, "subTypes" => $subTypes),null,"\Netistrar\ClientAPI\Objects\Account\AccountNotification[]",$expectedExceptions);
    }

    /**
     * Get an account notification by id.
     *
     * @param int $notificationId
     * @return \Netistrar\ClientAPI\Objects\Account\AccountNotification
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function getNotification($notificationId){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("notification/$notificationId", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Account\AccountNotification",$expectedExceptions);
    }


}

