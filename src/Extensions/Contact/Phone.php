<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;

class Phone implements JsonSerializable
{
    /** @var string */
    public $phone;
    /** @var string */
    public $type;
    /** @var string */
    public $wa_id;

    public static function create($phone, $type="HOME", $wa_id = null)
    {
        return new static($phone, $type, $wa_id);
    }

    public function __construct($phone, $type='HOME', $wa_id = null)
    {
        $this->phone = $phone;
        $this->type = $type;
        $this->wa_id = $wa_id;
    }

    public function toArray(){

        return [
            'phone' => $this->phone,
            'type' => $this->type,
            'wa_id' => $this->wa_id,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
