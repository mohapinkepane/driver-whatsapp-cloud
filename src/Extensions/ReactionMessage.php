<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use BotMan\BotMan\Interfaces\WebAccess;
use JsonSerializable;

class ReactionMessage implements JsonSerializable,WebAccess
{
    /** @var string */
    protected $id;

    /** @var string */
    public $emoji;

    /** @var string */
    public $message_id;


    /**
     * @param $message_id
     * @param $emoji
     * @return static
     */
    public static function create($message_id,$emoji)
    {
        return new static($message_id,$emoji);
    }

    public function __construct($message_id,$emoji)
    {
        $this->emoji = $emoji;
        $this->message_id = $message_id;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'reaction',
            'reaction' => [
                 'message_id'=>$this->message_id,
                 'emoji'=>$this->emoji
            ],
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

      /**
     * Get the instance as a web accessible array.
     * This will be used within the WebDriver.
     *
     * @return array
     */
    public function toWebDriver()
    {
        return [
            'type' => 'reaction',
            'message_id'=>$this->message_id,
            'emoji'=>$this->emoji
        ];
    }

}
