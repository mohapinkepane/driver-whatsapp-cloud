<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;

class ElementHeader implements JsonSerializable
{
    protected $type;

    /** @var string|array */
    protected $content;


    /** @var array */
    protected $header_types = [
         "text","image","video","document"
    ];

    public static function create($type, $content)
    {
        return new static($type, $content);
    }

    public function __construct($type, $content)
    {
        $this->type = $type;
        if (!in_array($type, $this->header_types)) {
            throw new \UnexpectedValueException('Unknown header type');
        }
        $this->content = $content;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }


    public function toArray()
    {
        $result = [
            'type' => $this->type
        ];
        $result[$this->type] = $this->content;
        return $result;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
