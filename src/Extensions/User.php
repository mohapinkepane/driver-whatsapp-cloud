<?php

namespace BotMan\Drivers\Whatsapp\Extensions;

use BotMan\BotMan\Interfaces\UserInterface;
use BotMan\BotMan\Users\User as BotManUser;

class User extends BotManUser implements UserInterface
{
    /**
     * @var array
     */
    protected $user_info;

    public function __construct(
        $id = null,
        $first_name = null,
        $last_name = null,
        $username = null,
        array $user_info = []
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->username = $username;
        $this->user_info = (array) $user_info;
    }


    /**
     * @return string
     */
    public function getWA_ID()
    {
        return $this->getPhoneNumber();

    }

     /**
     * @return string
     */
    public function getPhoneNumber()
    {
        if (isset($this->user_info['wa_id']) && $this->user_info['wa_id'] !== '') {
            return $this->user_info['wa_id'];
        }

        return isset($this->user_info['from']) && $this->user_info['from'] !== '' ? $this->user_info['from'] : null;

    }

    /**
     * @return string|null
     */
    public function getUserId()
    {
        if (isset($this->user_info['user_id']) && $this->user_info['user_id'] !== '') {
            return $this->user_info['user_id'];
        }

        return isset($this->user_info['from_user_id']) && $this->user_info['from_user_id'] !== '' ? $this->user_info['from_user_id'] : null;
    }

    /**
     * @return string|null
     */
    public function getParentUserId()
    {
        if (isset($this->user_info['parent_user_id']) && $this->user_info['parent_user_id'] !== '') {
            return $this->user_info['parent_user_id'];
        }

        return isset($this->user_info['from_parent_user_id']) && $this->user_info['from_parent_user_id'] !== '' ? $this->user_info['from_parent_user_id'] : null;
    }

    /**
     * @return string
     */
    public function getWhatsAppName()
    {   
        return isset($this->user_info['profile']['name']) ? $this->user_info['profile']['name'] : null;

    }

    /**
     * @return string|null
     */
    public function getWhatsAppUsername()
    {
        return isset($this->user_info['profile']['username']) ? $this->user_info['profile']['username'] : null;

    }

  
}
