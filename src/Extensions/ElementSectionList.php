<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\Drivers\Whatsapp\Extensions\ElementSectionListRow;

class ElementSectionList implements JsonSerializable
{

    /** @var string */
    protected $title;

    /** @var array */
    protected $rows = [];

    /**
     * @param  string  $title
     * @param  array   $rows
     * @return static
     */
    public static function create($title, array $rows)
    {

        return new static($title, $rows);
    }

    /**
     * @param  string  $title
     * @param  array   $rows
     */
    public function __construct($title, array $rows)
    {
        $this->title = $title;

        foreach ($rows as $row) {
            if ($row instanceof ElementSectionListRow) {
                $this->rows[] = $row->toArray();
            }
        }

    }

    /**
     * @return array
     */
    public function toArray()
    {
        $listArray = [
                'title' => $this->title,
                'rows' => $this->rows
        ];

        return $listArray;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
