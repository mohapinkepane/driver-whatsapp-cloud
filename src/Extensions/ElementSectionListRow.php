<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;

class ElementSectionListRow implements JsonSerializable
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $id;

    /** @var string */
    protected $description;


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
     * Set the button URL.
     *
     * @param  string  $url
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }



    /**
     * @return array
     */
    public function toArray()
    {
        $row=[
            'title' => $this->title,
            'id' => $this->id,
        ];

        if(!empty($this->description)){
            $row['description'] = $this->description;
        }
        return $row;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
