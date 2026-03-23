<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;

class Organization implements JsonSerializable
{
    /** @var string */
    public $company;
    /** @var string */
    public $department;
    /** @var string */
    public $title;

    public static function create($company,$title,$department='')
    {
        return new static($company,$title,$department);
    }

    public function __construct($company,$title,$department='')
    {
        $this->company = $company;
        $this->department = $department;
        $this->title = $title;
    }


    public function toArray(){

        return [
            'company' => $this->company,
            'department' => $this->department,
            'title' => $this->title,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
