<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\BotMan\Interfaces\WebAccess;

class  LocationMessage implements JsonSerializable,WebAccess
{
    /** @var float */
    public $longitude;
    /** @var float */
    public $latitude;
    /** @var string */
    public $name;
    /** @var string */
    public $address;

     /** @var string */
     public $context_message_id;

    public static function create($longitude, $latitude, $name='', $address='')
    {
        return new static($longitude, $latitude, $name, $address);
    }

    public function __construct($longitude, $latitude, $name='', $address='')
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->name = $name;
        $this->address = $address;
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
            "type"=>'location',
            'location'=>[
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'name' => $this->name,
                'address' => $this->address,
            ]
        ];

        if(isset($this->context_message_id)){
            $array['context']['message_id'] = $this->context_message_id;
        }

        return $array;

    }

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
        return[
            "type"=>'location',
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'name' => $this->name,
            'address' => $this->address,
        ];
    }
}
