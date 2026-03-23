<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;

class Name implements JsonSerializable
{
    /** @var string */
    public $first_name;
    /** @var string */
    public $formatted_name;
    /** @var string */
    public $last_name;

    public static function create($first_name, $formatted_name, $last_name)
    {
        return new static($first_name, $formatted_name, $last_name);
    }

    public function __construct($first_name, $formatted_name, $last_name)
    {
        $this->first_name = $first_name;
        $this->formatted_name = $formatted_name;
        $this->last_name = $last_name;
    }

    public function toArray(){

        return [
            'first_name' => $this->first_name,
            'formatted_name' => $this->formatted_name,
            'last_name' => $this->last_name,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
