<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Contact;

class ContactsMessage implements JsonSerializable, WebAccess
{

    /** @var string */
    public $context_message_id;

    /** @var array */
    protected $contacts = [];

    /**
     * @param  array   $contacts
     * @return static
     */
    public static function create(array $contacts)
    {

        return new static($contacts);
    }

    /**
     * @param  array   $contacts
     */
    public function __construct(array $contacts)
    {
        foreach ($contacts as $contact) {
            if ($contact instanceof Contact) {
                $this->contacts[] = $contact->toArray();
            }
        }

    }

      /**
     * Get the context_message_id.
     *
     * @return string
     */
    public function getContextMessageId()
    {
        if (empty($this->context_message_id)) {
            throw new \UnexpectedValueException('This message does not contain a context_message_id');
        }
        return $this->context_message_id;
    }


    /**
     * Set the context_message_id.
     * @param  string  $context_message_id
     * @return $this
     */
    public function contextMessageId($context_message_id)
    {
        $this->context_message_id = $context_message_id;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array=[
            "type"=>'contacts',
            "contacts"=>$this->contacts
        ];

        if(isset($this->context_message_id)){
            $array['context']['message_id'] = $this->context_message_id;
        }

        return $array;

    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Get the instance as a web accessible array.
     * This will be used within the WebDriver.
     *
     * @return array
     */
    public function toWebDriver()
    {
        return [
            "type"=>'contacts',
            "contacts"=>$this->contacts
        ];
    }

}
