<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;

class ElementButton implements JsonSerializable
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $id;

    /** @var string */
    protected $type = self::TYPE_REPLY;

    const TYPE_REPLY = 'reply';

    /**
     * @param  string  $title
     * @return static
     */
    public static function create($id, $title)
    {
        return new static($id, $title);
    }

    /**
     * @param  string  $title
     */
    public function __construct($id, $title)
    {
        $this->title = $title;
        $this->id = $id;
    }

    /**
     * Set the button type.
     *
     * @param  string  $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $buttonArray = [
            'type' => $this->type,
            'reply' => [
                'id' => $this->id,
                'title' => $this->title,
            ],
        ];
        
        return $buttonArray;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
