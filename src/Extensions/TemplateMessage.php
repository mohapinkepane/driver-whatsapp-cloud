<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use BotMan\BotMan\Interfaces\WebAccess;
use Illuminate\Support\Arr;
use JsonSerializable;

class TemplateMessage implements JsonSerializable, WebAccess
{

     /** @var string */
     public $templateId;

    /** @var array */
    public $components = [];

    /** @var string */
    public $language_code;

     /** @var string */
     public $context_message_id;

     /** @var string */
     public $category;



    /**
     * @param $text
     * @return static
     */
    public static function create($templateId,$language_code = 'en')
    {
        return new static($templateId,$language_code);
    }


    public function __construct($templateId,$language_code = 'en')
    {
        $this->templateId = $templateId;
        $this->language_code = $language_code;
    }

    /**
     * @param  ElementComponent  $component
     * @return $this
     */
    public function addComponent(ElementComponent $component)
    {
        $this->components[] = $component->toArray();

        return $this;
    }

    /**
     * @param  array  $component
     * @return $this
     */
    public function addComponents(array $components)
    {
        foreach ($components as $component) {
            if ($component instanceof ElementComponent) {
                $this->components[] = $component->toArray();
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
     * Set the category.
     * @param  string  $category
     * @return $this
     */
    public function category($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [
            'type' => 'template',
            'template' => [
                'name' => $this->templateId,
                'language' => ['code' =>$this->language_code],
                "components" => $this->components
            ],
        ];

        if(isset($this->context_message_id)){
            $array['context']['message_id'] = $this->context_message_id;
        }

        if(isset($this->category)){
            $array['template']['category'] = $this->category;
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
            'type' => 'template',
            'template' => [
                'name' => $this->templateId,
                'language' => ['code' => $this->language_code],
                "components" => $this->components
            ],
        ];
    }

}
