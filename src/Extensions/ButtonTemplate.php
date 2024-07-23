<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\Drivers\Whatsapp\Extensions\ElementButtonHeader;

class ButtonTemplate implements JsonSerializable, WebAccess
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
     public $header=[];

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
    public function addHeader(ElementButtonHeader $header)
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
     * @return array
     */
    public function toArray()
    {
        return [
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
    }

    /**
     * @return array
     */
    public function jsonSerialize()
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
