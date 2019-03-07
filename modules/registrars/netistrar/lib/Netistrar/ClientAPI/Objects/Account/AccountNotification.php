<?php

namespace Netistrar\ClientAPI\Objects\Account;

use Kinikit\Core\Object\SerialisableObject;
/**
 * A generic account notification.  This are used to encode events described by the <a href="#notificationType">notificationType</a> and <a href="#notificationSubType">notificationSubType</a> properties.
 * A human readable message is also returned along with the description of an associated object contained in the <a href="#refersTo">refersTo</a> property if relevant to this notification.
*/
class AccountNotification extends SerialisableObject {

    /**
     * The id for this notification.
     *
     * @var integer 
     */
    protected $id;

    /**
     * The date/time that this notification was created.
     *
     * @var string 
     */
    protected $dateTime;

    /**
     * The general type for this notification
     *
     * @var string 
     */
    protected $notificationType;

    /**
     * A more specific sub type for this notification
     *
     * @var string 
     */
    protected $notificationSubType;

    /**
     * A description for an item that this notification refers to if applicable.
     *
     * @var string 
     */
    protected $refersTo;

    /**
     * A descriptive message giving more detail about this notification.
     *
     * @var string 
     */
    protected $message;



    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }

    /**
     * Get the id
     *
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Get the dateTime
     *
     * @return string
     */
    public function getDateTime(){
        return $this->dateTime;
    }

    /**
     * Get the notificationType
     *
     * @return string
     */
    public function getNotificationType(){
        return $this->notificationType;
    }

    /**
     * Get the notificationSubType
     *
     * @return string
     */
    public function getNotificationSubType(){
        return $this->notificationSubType;
    }

    /**
     * Get the refersTo
     *
     * @return string
     */
    public function getRefersTo(){
        return $this->refersTo;
    }

    /**
     * Get the message
     *
     * @return string
     */
    public function getMessage(){
        return $this->message;
    }


}