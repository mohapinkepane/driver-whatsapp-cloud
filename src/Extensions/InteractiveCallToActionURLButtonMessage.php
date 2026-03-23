<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\Drivers\Whatsapp\Extensions\ElementHeader;

class InteractiveCallToActionURLButtonMessage implements JsonSerializable, WebAccess
{

    /** @var string */
    protected $id;

    /** @var string */
    public $text;

     /** @var string */
     public $footer;

       /** @var string */
    public $action;

    /** @var string */
    public $url;


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
    public static function create($text,$action,$url)
    {
        return new static($text,$action,$url);
    }

    public function __construct($text,$action,$url)
    {
        $this->text = $text;
        $this->action = $action;
        $this->url = $url;
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
                "type"=>"cta_url",
                'header' => $this->header,
                'body' => [
                    "text"=>$this->text
                ],
                'footer' => [
                    "text"=>$this->footer
                ],
                'action' => [
                        "name"=>"cta_url",
                        "parameters"=>[
                            "display_text"=>$this->action,
                            "url"=>$this->url
                        ]
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
            'type' => 'cta_url',
            'text' => $this->text,
            'header'=>$this->header,
            'footer'=>$this->footer,
            "display_text"=>$this->action,
            "url"=>$this->url
        ];
    }

}
