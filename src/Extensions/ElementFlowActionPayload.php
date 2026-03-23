<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;

class ElementFlowActionPayload implements JsonSerializable
{

    /** @var string */
    protected $id;

    /** @var string */
    protected $screen;

    /** @var array|null */
    protected $data;

    /**
     * @param  string  $screen
     * @return static
     */
    public static function create($screen,$data=null)
    {
        return new static($screen,$data);
    }

    /**
     * @param  string  $screen
     */
    public function __construct($screen,$data=null)
    {
        $this->screen = $screen;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
            $array=["screen"=>$this->screen];

            if(!empty($this->data)){
                $array['data'] = $this->data;
            }

            return $array;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
