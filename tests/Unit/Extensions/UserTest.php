<?php

namespace BotMan\Drivers\Whatsapp\Tests\Unit\Extensions;

use PHPUnit\Framework\TestCase;
use BotMan\Drivers\Whatsapp\Extensions\User;

class UserTest extends TestCase
{
    public function testConstructor()
    {
        $user = new User(
            '12345',
            'John',
            'Doe',
            'johndoe',
            ['wa_id' => 'whatsapp_id_123', 'profile' => ['name' => 'John Doe']]
        );

        $this->assertEquals('12345', $user->getId());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals('johndoe', $user->getUsername());
        $this->assertEquals('whatsapp_id_123', $user->getWA_ID());
        $this->assertEquals('John Doe', $user->getWhatsAppName());
    }

    public function testGetWA_ID()
    {
        $user = new User(null, null, null, null, ['wa_id' => 'whatsapp_id_123']);
        
        $this->assertEquals('whatsapp_id_123', $user->getWA_ID());
    }

    public function testGetPhoneNumber()
    {
        $user = new User(null, null, null, null, ['wa_id' => 'whatsapp_id_123']);

        $this->assertEquals('whatsapp_id_123', $user->getPhoneNumber());
    }

    public function testGetWhatsAppName()
    {
        $user = new User(null, null, null, null, ['profile' => ['name' => 'Jane Doe']]);

        $this->assertEquals('Jane Doe', $user->getWhatsAppName());
    }

    public function testGetBusinessScopedIdentifiers()
    {
        $user = new User(null, null, null, null, [
            'user_id' => 'US.13491208655302741918',
            'parent_user_id' => 'US.ENT.11815799212886844830',
            'profile' => ['username' => 'janedoe'],
        ]);

        $this->assertEquals('US.13491208655302741918', $user->getUserId());
        $this->assertEquals('US.ENT.11815799212886844830', $user->getParentUserId());
        $this->assertEquals('janedoe', $user->getWhatsAppUsername());
        $this->assertNull($user->getPhoneNumber());
    }

    public function testMissingWA_ID()
    {
        $user = new User();

        $this->assertNull($user->getWA_ID());
    }

    public function testMissingPhoneNumber()
    {
        $user = new User();

        $this->assertNull($user->getPhoneNumber());
    }

    public function testMissingWhatsAppName()
    {
        $user = new User();

        $this->assertNull($user->getWhatsAppName());
    }

    public function testUserInfoArray()
    {
        $user = new User(null, null, null, null, ['profile' => ['name' => 'Alice Smith']]);

        $this->assertIsArray($user->getInfo());
        $this->assertArrayHasKey('profile', $user->getInfo());
        $this->assertEquals('Alice Smith', $user->getWhatsAppName());
    }
}
