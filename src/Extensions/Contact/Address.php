<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;

class Address implements JsonSerializable
{
    /** @var string */
    public $city;
    /** @var string */
    public $country;
    /** @var string */
    public $country_code;
    /** @var string */
    public $state;
     /** @var string */
    public $street;
    /** @var string */
    public $type;
    /** @var string */
    public $zip;

    public static function create($city, $country, $country_code='', $state='', $street='', $type='HOME', $zip='')
    {
        return new static($city, $country, $country_code, $state, $street, $type, $zip);
    }

    public function __construct($city, $country, $country_code='', $state='', $street='', $type='HOME', $zip='')
    {
        $this->city = $city;
        $this->country = $country;
        $this->country_code = $country_code;
        $this->state = $state;
        $this->street = $street;
        $this->type = $type;
        $this->zip = $zip;
    }

     /**
     * @return array
     */
    public function toArray()
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'state' => $this->state,
            'street' => $this->street,
            'type' => $this->type,
            'zip' => $this->zip,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
