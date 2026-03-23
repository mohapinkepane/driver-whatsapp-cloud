<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\Drivers\Whatsapp\Extensions\ElementHeader;
use BotMan\Drivers\Whatsapp\Extensions\ElementFlowActionPayload;

class FlowMessage implements JsonSerializable, WebAccess
{
    /** @var string */
    protected $id;

    /** @var string */
    public $text;

    /** @var string */
    public $token;

    /** @var string */
    public $action='navigate';

    /** @var string */
    public $cta_text;

    /** @var string */
    public $mode='published';

    /** @var array */
    public $action_payload=[];

     /** @var string */
     public $footer;

    /** @var array */
      public $header;

      /** @var string */
    public $context_message_id;


    /** @var int */
    public $version;

    /**
     * @param $text
     * @return static
     */
    public static function create($id,$token,$cta_text,$text,$mode='published',$action='navigate',$version=3)
    {
        return new static($id,$token,$cta_text,$text,$mode,$action,$version);
    }


    public function __construct($id,$token,$cta_text,$text,$mode='published',$action='navigate',$version=3)
    {
        $this->text = $text;
        $this->id = $id;
        $this->token = $token;
        $this->cta_text = $cta_text;
        $this->version=$version;
        $this->mode=$mode;
        $this->action = $action;
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
     * @param  ElementFlowActionPayload  $action_payload
     * @return $this
     */
    public function addActionPayload(ElementFlowActionPayload $action_payload)
    {
        if($this->action != 'navigate') {
            throw new \UnexpectedValueException('Action payload is required if flow_action is navigate. Should be omitted otherwise.');
        }

        $this->action_payload = $action_payload->toArray();

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
        $array=[
            'type' => 'interactive',
            'interactive' => [
                'type'=>"flow",
                'header' => $this->header,
                'body' => [
                    "text"=>$this->text
                ],
                'footer' => [
                    "text"=>$this->footer
                ],
                "action"=>[
                    "name"=> "flow",
                    "parameters"=>[
                      "flow_message_version"=>$this->version,
                      "flow_token"=> $this->token,
                      "flow_id"=> $this->id,
                      "flow_cta"=>$this->cta_text,
                      "mode"=>$this->mode,
                      "flow_action"=> $this->action
                   ]
                ]
            ],
        ];

        if(!empty($this->action_payload)) {
            $array['interactive']['action']['parameters']['flow_action_payload'] = $this->action_payload;
        }
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
            'header'=>$this->header,
            'footer'=>$this->footer,
            "message_version"=>$this->version,
            "token"=> $this->token,
            "id"=> $this->id,
            "cta_text"=>$this->cta_text,
            "action"=> $this->action
        ];
    }
}
