<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\Drivers\Whatsapp\Extensions\ElementButton;
use BotMan\Drivers\Whatsapp\Extensions\ElementHeader;

class InteractiveReplyButtonsMessage implements JsonSerializable, WebAccess
{


    /** @var string */
    protected $id;

    /** @var string */
    public $text;

    /** @var array */
    public $buttons = [];

     /** @var string */
     public $footer;

     /** @var array */
     public $header=[
        "type"=>"text",
        "text"=>""
     ];

      /** @var string */
    public $context_message_id;

    /**
     * @param $text
     * @return static
     */
    public static function create($text)
    {
        return new static($text);
    }

    public function __construct($text)
    {
        $this->text = $text;
    }

     /**
     * Get the Footer.
     *
     * @return string
     */
    public function getFooter(){

        if (empty($this->footer)) {
            throw new \UnexpectedValueException('This message does not contain a footer');
        }
        return $this->footer;
    }

    /**
     * Set the Footer.
     * @param  string  $footer
     * @return $this
     */
    public function addFooter($footer){
        $this->footer=$footer;
        return $this;
    }

    /**
     * @param  array $header
     * @return $this
     */
    public function addHeader(ElementHeader $header)
    {
        $this->header = $header->toArray();
        return $this;
    }

    /**
     * @param  ElementButton  $button
     * @return $this
     */
    public function addButton(ElementButton $button)
    {
        $this->buttons[] = $button->toArray();

        return $this;
    }

    /**
     * @param  array  $buttons
     * @return $this
     */
    public function addButtons(array $buttons)
    {
        foreach ($buttons as $button) {
            if ($button instanceof ElementButton) {
                $this->buttons[] = $button->toArray();
            }
        }

        return $this;
    }

      /**
     * Get the context_message_id.
     *
     * @return string
     */
    public function getContextMessageId()
    {
        if (empty($this->context_message_id)) {
            throw new \UnexpectedValueException('This message does not contain a context_message_id');
        }
        return $this->context_message_id;
    }


    /**
     * Set the context_message_id.
     * @param  string  $context_message_id
     * @return $this
     */
    public function contextMessageId($context_message_id)
    {
        $this->context_message_id = $context_message_id;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [
            'type' => 'interactive',
            'interactive' => [
                'type'=>"button",
                'header' => $this->header,
                'body' => [
                    "text"=>$this->text
                ],
                'footer' => [
                    "text"=>$this->footer
                ],
                'action' => [
                    'buttons' => $this->buttons,
                ]
            ],
        ];

        if(isset($this->context_message_id)){
            $array['context']['message_id'] = $this->context_message_id;
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

    /**
     * Get the instance as a web accessible array.
     * This will be used within the WebDriver.
     *
     * @return array
     */
    public function toWebDriver()
    {
        return [
            'type' => 'buttons',
            'text' => $this->text,
            'buttons' => $this->buttons,
            'header'=>$this->header,
            'footer'=>$this->footer
        ];
    }
}
