<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;


class URL implements JsonSerializable
{
    /** @var string */
    public $url;
    /** @var string */
    public $type;

    public static function create($url, $type='HOME')
    {
        return new static($url, $type);
    }

    public function __construct($url, $type='HOME')
    {
        $this->url = $url;
        $this->type = $type;
    }

    public function toArray(){

        return [
            'url' => $this->url,
            'type' => $this->type,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
