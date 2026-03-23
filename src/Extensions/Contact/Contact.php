<?php

namespace BotMan\Drivers\Whatsapp\Extensions\Contact;

use JsonSerializable;
use BotMan\Drivers\Whatsapp\Extensions\Contact\URL;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Name;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Email;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Phone;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Address;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Organization;

class Contact implements JsonSerializable
{
    public $addresses;
    public $birthday;
    public $emails;
    public $name;
    public $org;
    public $phones;
    public $urls;

    public static function create($addresses, $birthday, $emails, Name $name, ?Organization $org, $phones, $urls)
    {
        return new static($addresses, $birthday, $emails, $name, $org, $phones, $urls);
    }

    public function __construct($addresses, $birthday, $emails, Name $name, ?Organization $org, $phones, $urls)
    {
        $this->birthday = $birthday;
        $this->name = $name;
        $this->org = $org;
  
        foreach ($addresses as $address) {
            if ($address instanceof Address) {
                $this->addresses[] = $address->toArray();
            }
        }
        foreach ($emails as $email) {
            if ($email instanceof Email) {
                $this->emails[] = $email->toArray();
            }
        }

        foreach ($phones as $phone) {
            if ($phone instanceof Phone) {
                $this->phones[] = $phone->toArray();
            }
        }

        foreach ($urls as $url) {
            if ($url instanceof URL) {
                $this->urls[] = $url->toArray();
            }
        }
    }

    public function toArray(){

        return [
            'addresses' => $this->addresses,
            'birthday' => $this->birthday,
            'emails' => $this->emails,
            'name' => $this->name,
            'org' => $this->org,
            'phones' => $this->phones,
            'urls' => $this->urls,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
