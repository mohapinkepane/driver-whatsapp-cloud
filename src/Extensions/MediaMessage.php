<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use BotMan\BotMan\Interfaces\WebAccess;
use JsonSerializable;

class MediaMessage implements JsonSerializable, WebAccess
{
    /** @var string */
    protected $id;

    /** @var string */
    public $url;

    /** @var string */
    public $caption;

    /** @var string */
    public $context_message_id;

    /** @var string */
    public $filename;

     /** @var string */
    public $type;

    /** @var array */
    protected $media_types = [
        'video','image','document','sticker','audio'
    ];

    /** @var array */
    protected $have_caption = [
        'video','image','document'
    ];

    /**
     * @param $url
     * @return static
     */
    public static function create($type)
    {
        return new static($type);
    }

    public function __construct($type)
    {
        if (!in_array($type, $this->media_types)) {
            throw new \UnexpectedValueException('Unknown media type');
        }
        $this->type=$type;
    }

     /**
     * Get the type.
     *
     * @return string
     */
    public function getType()
    {
        if (empty($this->type)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain a type');
        }
        return $this->type;
    }

    /**
     * Get the context_message_id.
     *
     * @return string
     */
    public function getContextMessageId()
    {
        if (empty($this->context_message_id)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain a context_message_id');
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
     * Set the Link.
     * @param  string  $url
     * @return $this
     */
    public function url($url){

        if (!empty($this->id)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' already contains an ID');
        }
        $this->url = $url;
        return $this;
    }

    /**
     * Get the Link.
     *
     * @return string
     */
    public function getUrl(){

        if (empty($this->url)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain a url');
        }
        return $this->url;
    }

     /**
     * Set the ID.
     * @param  string  $id
     * @return $this
     */
    public function id($id){
        if (!empty($this->url)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' already contains a url');
        }
        $this->id =$id;
        return $this;
    }

    /**
     * Get the ID.
     *
     * @return string
     */
    public function getId(){

        if (empty($this->id)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain an ID');
        }
        return $this->id;
    }

      /**
     * Get the Caption.
     *
     * @return string
     */
    public function getCaption(){

        if (empty($this->caption)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain a caption');
        }

        return $this->caption;
    }

    /**
     * Set the Caption.
     * @param  string  $caption
     * @return $this
     */
    public function caption($caption){

        if (!in_array($this->type, $this->have_caption)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not support caption');
        }

        $this->caption=$caption;

        return $this;
    }



      /**
     * Get the Filename.
     *
     * @return string
     */
    public function getFileName(){

        if (empty($this->filename)) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain a filename');
        }

        return $this->filename;
    }

    /**
     * Set the Filename.
     * @param  string  $filename
     * @return $this
     */
    public function fileName($filename){

        if (!in_array($this->type,['document'])) {
            throw new \UnexpectedValueException(ucfirst($this->type).' does not support filename');
        }

        $this->filename=$filename;

        return $this;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $array=[
            'type' => $this->type,
             $this->type => [],
        ];

        if(!isset($this->id)&&!isset($this->url)){
            throw new \UnexpectedValueException(ucfirst($this->type).' does not contain an ID or URL');
        }

        if(isset($this->id)){
            $array[$this->type]['id'] = $this->id;
        }

        if(isset($this->url)){
            $array[$this->type]['link'] = $this->url;
        }

        if(isset($this->context_message_id)){
            $array['context']['message_id'] = $this->context_message_id;
        }

        if(isset($this->caption)){
            $array[$this->type]['caption'] = $this->caption;
        }

        if(isset($this->filename)){
            $array[$this->type]['filename'] = $this->filename;
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
            'message_id'=>isset($this->context_message_id)?$this->context_message_id:null,
            'type' => $this->type,
            'link'=>$this->url,
            'id'=>$this->id,
            'caption'=> $this->caption
        ];
    }
}
