<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use JsonSerializable;

class ElementComponent implements JsonSerializable
{

    /** @var array */
    protected $parameters = [];

    /** @var string */
    protected $type_component;

    /**
     * @param  array   $parameters
     * @return static
     */
    public static function create($type_component, array $parameters)
    {
        return new static($type_component, $parameters);
    }
  
    

    /**
     * @param  array   $parameters
     */
    public function __construct($type_component, array $parameters)
    { 
        $this->type_component = $type_component;
        $this->parameters = $parameters;
    }

 
    /**
     * @return array
     */
    public function toArray()
    {
        
        $componentArray = [  
                'type' => $this->type_component,
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => $this->parameters
    ];

    return $componentArray;
    
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
