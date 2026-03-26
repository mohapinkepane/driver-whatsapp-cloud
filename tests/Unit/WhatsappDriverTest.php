<?php

namespace BotMan\Drivers\Whatsapp\Tests\Unit;

use Mockery;
use BotMan\BotMan\Http\Curl;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use BotMan\BotMan\Interfaces\UserInterface;
use Botman\Drivers\Whatsapp\WhatsappDriver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BotMan\Drivers\Whatsapp\Extensions\Contact\URL;
use BotMan\Drivers\Whatsapp\Extensions\TextMessage;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Name;
use BotMan\Drivers\Whatsapp\Extensions\MediaMessage;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Email;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Phone;
use BotMan\Drivers\Whatsapp\Extensions\ElementButton;
use BotMan\Drivers\Whatsapp\Extensions\ElementHeader;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Address;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Contact;
use BotMan\Drivers\Whatsapp\Extensions\ContactsMessage;
use BotMan\Drivers\Whatsapp\Extensions\LocationMessage;
use BotMan\Drivers\Whatsapp\Extensions\ReactionMessage;
use BotMan\Drivers\Whatsapp\Extensions\TemplateMessage;
use BotMan\Drivers\Whatsapp\Tests\WhatsappDriverConfig;
use BotMan\Drivers\Whatsapp\Extensions\ElementComponent;
use BotMan\Drivers\Whatsapp\Exceptions\WhatsappException;
use BotMan\Drivers\Whatsapp\Extensions\ElementSectionList;
use BotMan\Drivers\Whatsapp\Extensions\Contact\Organization;
use BotMan\Drivers\Whatsapp\Extensions\ElementSectionListRow;
use BotMan\Drivers\Whatsapp\Extensions\InteractiveListMessage;
use BotMan\Drivers\Whatsapp\Extensions\LocationRequestMessage;
use BotMan\Drivers\Whatsapp\Extensions\InteractiveReplyButtonsMessage;
use BotMan\Drivers\Whatsapp\Extensions\InteractiveCallToActionURLButtonMessage;

class WhatsappDriverTest extends TestCase
{
    protected $driver;
    protected $config;
    protected $request;
    protected $http;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config =[
            'url' => 'https://graph.facebook.com',
            'token' => 'test_token',
            'app_secret' => null,
            'verification' =>'@test!23$',
            'phone_number_id' => '27681414235104944',
            'passphrase' => 'passphrase',
            'public_key' => 'public_key',
            'private_key' =>  'private_key',
            'version' => 'v20.0',
            'throw_http_exceptions' => true,
            'restrict_inbound_messages_to_phone_number_id' => true,
            'to_phone_number' => '1234567890'
        ];

        $this->http = $this->createMock(\BotMan\BotMan\Interfaces\HttpInterface::class);
        $this->request = $this->createMock(Request::class);
        $this->request->headers = new \Symfony\Component\HttpFoundation\HeaderBag([
            'X_HUB_SIGNATURE_256' => ''
        ]);
        $this->driver = new WhatsappDriver($this->request,$this->config, $this->http);
    }

    private function getRequest($responseData)
    {
        $request = \Mockery::mock(\Symfony\Component\HttpFoundation\Request::class.'[getContent]');
        $request->shouldReceive('getContent')->andReturn($responseData);

        return $request;
    }

    private function getDriver($responseData, ?array $config = null, $signature = '', $httpInterface = null)
    {
        if (is_null($config)) {
            $config = [
                'whatsapp' => $this->config,
            ];
        }

        $request = $this->getRequest($responseData);       
        $request->headers = new \Symfony\Component\HttpFoundation\HeaderBag([
            'X_HUB_SIGNATURE_256' => $signature
        ]);

        if ($httpInterface === null) {
            $httpInterface = $this->createMock(\BotMan\BotMan\Interfaces\HttpInterface::class);
        }
        return new WhatsappDriver($request,$config,$httpInterface);
    }

    private function buildApiUrl()
    {
        return $this->config['url'] . '/' . $this->config['version'] . '/' . $this->config['phone_number_id'] . '/' . 'messages';
    }

    private function inboundPayload(?array $contact = null, ?array $message = null, array $valueOverrides = []): string
    {
        $value = [
            'messaging_product' => 'whatsapp',
            'metadata' => [
                'display_phone_number' => '16505553333',
                'phone_number_id' => '27681414235104944',
            ],
        ];

        if (!is_null($contact)) {
            $value['contacts'] = [$contact];
        }

        if (!is_null($message)) {
            $value['messages'] = [$message];
        }

        $value = array_replace_recursive($value, $valueOverrides);

        return json_encode([
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => '8856996819413533',
                'changes' => [[
                    'value' => $value,
                    'field' => 'messages',
                ]],
            ]],
        ]);
    }

    private function buildAuthHeader()
    {
        return [
            "Authorization: Bearer " . $this->config['token'],
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    private function successfulMessageNodeResponse(): string
    {
        return '{"messaging_product": "whatsapp", "contacts": [{"input": "PHONE_NUMBER", "wa_id": "WHATSAPP_ID"}], "messages": [{"id": "wamid.ID"}]}';
    }

    private function failedMessageResponse(): string
    {
        return '{"error":{"message":"Invalid OAuth access token - Cannot parse access token","type":"OAuthException","code":190,"fbtrace_id":"AbJuG-rMVv36mjw-r78mKwg"}}';
    }


    public function testReturnsTheDriverName()
     {
         $driver = $this->getDriver('');
         $this->assertSame('Whatsapp', $driver->getName());
     }

    public function testMatchesTheRequest()
    {
        $request = '{}';
        $driver = $this->getDriver($request);
        $this->assertFalse($driver->matchesRequest());
        
        $config = [
            'whatsapp' => [
                'token' => 'test_token',
                'app_secret' => null,
                'phone_number_id' => '27681414235104944',
            ],
        ];
        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request, $config);
        $this->assertTrue($driver->matchesRequest());

        $config = [
            'whatsapp' => [
                'token' => 'test_token',
                'app_secret' => 'test_app_secret',
            ],
        ];
        $request = '{}';
        $driver = $this->getDriver($request, $config);
        $this->assertFalse($driver->matchesRequest());

        $signature = 'Foo';
        $config = [
            'whatsapp' => [
                'token' => 'test_token',
                'app_secret' => 'test_app_secret',
            ],
        ];
        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request,$config,$signature);
        $this->assertFalse($driver->matchesRequest());

        $signature = 'sha256=55aac047e58de7479ddb9791eef3391b83830bbfd003fe29a185a6d3b8591db1';
        $config = [
            'whatsapp' => [
                'token' => 'test_token',
                'app_secret' => 'test_app_secret',
            ],
        ];
        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request,$config,$signature);
        $this->assertTrue($driver->matchesRequest());

        $config = [
            'whatsapp' => [
                'token' => 'test_token',
                'app_secret' => null,
                'phone_number_id' => '27681414235104944',
            ],
        ];
        $request = $this->inboundPayload(
            [
                'profile' => [
                    'name' => 'Kerry Fisher',
                    'username' => 'kerry',
                ],
                'user_id' => 'US.13491208655302741918',
                'parent_user_id' => 'US.ENT.11815799212886844830',
            ],
            [
                'id' => 'wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W',
                'timestamp' => '1603059201',
                'text' => ['body' => 'Hello this is an answer'],
                'type' => 'text',
                'from_user_id' => 'US.13491208655302741918',
                'from_parent_user_id' => 'US.ENT.11815799212886844830',
            ]
        );
        $driver = $this->getDriver($request, $config);
        $this->assertTrue($driver->matchesRequest());
    }


     public function testReturnsTheMessage()
     {
       
         $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
         $driver = $this->getDriver($request);
 
         $this->assertSame('Hello this is an answer', $driver->getMessages()[0]->getText());
 
         $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":""},"type":"text"}]},"field":"messages"}]}]}';
         $driver = $this->getDriver($request);
 
         $this->assertSame('', $driver->getMessages()[0]->getText());
     }
 
     public function testReturnsTheMessageAsReference()
     {
         $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
         $driver = $this->getDriver($request);
 
         $hash = spl_object_hash($driver->getMessages()[0]);
 
         $this->assertSame($hash, spl_object_hash($driver->getMessages()[0]));
     }

     public function testReturnsAnEmptyMessageIfNothingMatches()
     {
         $request = '';
         $driver = $this->getDriver($request);
 
         $this->assertSame('', $driver->getMessages()[0]->getText());
     }
 

     public function testDetectsBots()
     {
         $driver = $this->getDriver('');
         $this->assertFalse($driver->isBot());
     }

    public function testReturnsTheUserId()
    {
        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request);

        $this->assertSame('16315551234', $driver->getMessages()[0]->getSender());
    }

    
    public function testReturnsUser()
    {
        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request);

        $user = $driver->getUser(new IncomingMessage('', '16315551234', ''));

        $this->assertEquals("16315551234", $user->getId());
        $this->assertEquals("Kerry Fisher", $user->getFirstName());


        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request);

        $user = $driver->getUser(new IncomingMessage('', '16315551234', ''));

        $this->assertEquals(null, $user->getId());
        $this->assertEquals(null, $user->getFirstName());
      
    }

    public function testReturnsBusinessScopedUser()
    {
        $request = $this->inboundPayload(
            [
                'profile' => [
                    'name' => 'Kerry Fisher',
                    'username' => 'kerry',
                ],
                'user_id' => 'US.13491208655302741918',
                'parent_user_id' => 'US.ENT.11815799212886844830',
            ],
            [
                'id' => 'wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W',
                'timestamp' => '1603059201',
                'text' => ['body' => 'Hello this is an answer'],
                'type' => 'text',
                'from_user_id' => 'US.13491208655302741918',
                'from_parent_user_id' => 'US.ENT.11815799212886844830',
            ]
        );
        $driver = $this->getDriver($request);

        $message = $driver->getMessages()[0];
        $user = $driver->getUser($message);

        $this->assertInstanceOf(\BotMan\Drivers\Whatsapp\Extensions\User::class, $user);
        /** @var \BotMan\Drivers\Whatsapp\Extensions\User $user */

        $this->assertSame('US.13491208655302741918', $message->getSender());
        $this->assertSame('US.13491208655302741918', $message->getExtras('user_id'));
        $this->assertSame('US.ENT.11815799212886844830', $message->getExtras('parent_user_id'));
        $this->assertNull($message->getExtras('phone_number'));
        $this->assertEquals('US.13491208655302741918', $user->getId());
        $this->assertEquals('US.13491208655302741918', $user->getUserId());
        $this->assertEquals('US.ENT.11815799212886844830', $user->getParentUserId());
        $this->assertEquals('Kerry Fisher', $user->getFirstName());
        $this->assertEquals('kerry', $user->getUsername());
        $this->assertEquals('kerry', $user->getWhatsAppUsername());
        $this->assertNull($user->getPhoneNumber());
        $this->assertNull($user->getWA_ID());
    }

    public function testReturnsTheRecipientId()
    {
        $request = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Hello this is an answer"},"type":"text"}]},"field":"messages"}]}]}';
        $driver = $this->getDriver($request);

        $this->assertSame('16505553333', $driver->getMessages()[0]->getRecipient());
     }

  
      public function testReturnsAnswerFromRegularMessages()
    {

        $config = [
            'whatsapp' => [
                'token' => 'test_token',
            ],
        ];
      
       $responseData = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","text":{"body":"Red"},"type":"text"}]},"field":"messages"}]}]}';
      
       $request = $this->getRequest($responseData);      
       $httpInterface = $this->createMock(\BotMan\BotMan\Interfaces\HttpInterface::class);
       $driver=new WhatsappDriver($request,$config,$httpInterface);
    
       $message = new IncomingMessage('Red','16315551234', '16505553333');
       $this->assertSame('Red',$driver->getConversationAnswer($message)->getText());
       $this->assertSame($message, $driver->getConversationAnswer($message)->getMessage());
       $this->assertFalse($driver->getConversationAnswer($message)->isInteractiveMessageReply());
    }

     public function testReturnsAnswerFromInteractiveMessages()
     {
    
         $config = [
             'whatsapp' => [
                 'token' => 'test_token',
             ],
         ];
       
        $responseData = '{"object":"whatsapp_business_account","entry":[{"id":"8856996819413533","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"16505553333","phone_number_id":"27681414235104944"},"contacts":[{"profile":{"name":"Kerry Fisher"},"wa_id":"16315551234"}],"messages":[{"from":"16315551234","id":"wamid.ABGGFlCGg0cvAgo-sJQh43L5Pe4W","timestamp":"1603059201","interactive":{"button_reply":{"id":1,"title":"Red"}},"type":"interactive"}]},"field":"messages"}]}]}';
       
        $request = $this->getRequest($responseData);       
        $httpInterface = $this->createMock(\BotMan\BotMan\Interfaces\HttpInterface::class);
        $driver=new WhatsappDriver($request,$config,$httpInterface);
        $driver->buildPayload($request);
       
        $message = new IncomingMessage('','16315551234', '16505553333');
        $this->assertSame('Red',$driver->getConversationAnswer($message)->getText());
        $this->assertSame($message, $driver->getConversationAnswer($message)->getMessage());
        $this->assertSame(1,$driver->getConversationAnswer($message)->getValue());
        $this->assertTrue($driver->getConversationAnswer($message)->isInteractiveMessageReply());

     }

    public function testIsConfigured()
    {
        $config = [
            'whatsapp' => [
                'token' => 'test_token',
                'app_secret' => 'test_app_secret',
            ],
        ];
        $request = '{}';
        $driver = $this->getDriver($request, $config);
        $this->assertFalse($driver->matchesRequest());


        $config = [
            'whatsapp' => [
                'token' => null,
                'app_secret' => 'test_app_secret',
            ],
        ];

        $driver = $this->getDriver($request, $config);
        $this->assertFalse($driver->isConfigured());

        $driver = $this->getDriver($request,[]);
        $this->assertFalse($driver->isConfigured());
    }

    public function testBuildServicePayloadUsesRecipientForBusinessScopedUsers()
    {
        $driver = $this->getDriver('{}');
        $matchingMessage = new IncomingMessage('', 'US.13491208655302741918', '');

        $payload = $driver->buildServicePayload('Hello there', $matchingMessage);

        $this->assertArrayHasKey('recipient', $payload);
        $this->assertSame('US.13491208655302741918', $payload['recipient']);
        $this->assertArrayNotHasKey('to', $payload);
    }

    public function testBuildServicePayloadKeepsToForPhoneNumbers()
    {
        $driver = $this->getDriver('{}');
        $matchingMessage = new IncomingMessage('', '16315551234', '');

        $payload = $driver->buildServicePayload('Hello there', $matchingMessage);

        $this->assertArrayHasKey('to', $payload);
        $this->assertSame('16315551234', $payload['to']);
        $this->assertArrayNotHasKey('recipient', $payload);
    }
    
}
