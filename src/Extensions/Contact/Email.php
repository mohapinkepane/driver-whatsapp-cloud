<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;


class Email implements JsonSerializable
{
    /** @var string */
    public $email;
    /** @var string */
    public $type;

    public static function create($email, $type='HOME')
    {
        return new static($email, $type);
    }

    public function __construct($email, $type='HOME')
    {
        $this->email = $email;
        $this->type = $type;
    }

    public function toArray(){

        return [
            'email' => $this->email,
            'type' => $this->type,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
