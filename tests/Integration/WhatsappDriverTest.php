<?php

namespace BotMan\Drivers\Whatsapp\Tests\Integration;

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
            'url' => WhatsappDriverConfig::$url,
            'token' => WhatsappDriverConfig::$token,
            'app_secret' => WhatsappDriverConfig::$app_secret,
            'verification' => WhatsappDriverConfig::$verification,
            'phone_number_id' => WhatsappDriverConfig::$phone_number_id,
            'passphrase' => WhatsappDriverConfig::$passphrase,
            'public_key' => WhatsappDriverConfig::$public_key,
            'private_key' => WhatsappDriverConfig::$private_key,
            'version' => WhatsappDriverConfig::$version,
            'throw_http_exceptions' => WhatsappDriverConfig::$throw_http_exceptions,
            'restrict_inbound_messages_to_phone_number_id' => WhatsappDriverConfig::$restrict_inbound_messages_to_phone_number_id,
            'to_phone_number' => WhatsappDriverConfig::$to_phone_number
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

    public function testSendTextMessage()
    {
        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

        $message = TextMessage::create('Hello, world!');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertSame('whatsapp',json_decode($response->getContent(),true)['messaging_product']);
        $this->assertEquals(200,$response->getStatusCode());
        

        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');
        $message = TextMessage::create('https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-messages');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testSendMediaMessage(){


        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

    

        $message = MediaMessage::create('image')
        ->url('https://images.pexels.com/photos/1266810/pexels-photo-1266810.jpeg')
        ->caption('This is a cool image!');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());



        $message = MediaMessage::create('audio')
        ->url('https://samplelib.com/lib/preview/mp3/sample-15s.mp3');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());


        $message = MediaMessage::create('document')
        ->url('https://pdfobject.com/pdf/sample.pdf')
        ->caption('This is a cool Document!');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());

        

        $message = MediaMessage::create('sticker')
        ->url('https://stickermaker.s3.eu-west-1.amazonaws.com/storage/uploads/sticker-pack/meme-pack-3/sticker_18.webp');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());


        $message =  MediaMessage::create('video')
        ->url('https://sample-videos.com/video321/mp4/480/big_buck_bunny_480p_10mb.mp4')
        ->caption('This is a cool Video!');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
      
    }

    public function testSendContactMessage(){

        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

   

        $addresses = [
            Address::create("Menlo Park", "United States"),
        ];

        $emails = [
            Email::create("test@whatsapp.com"),
            Email::create("test@fb.com", "WORK")
        ];

        $name = Name::create("John", "John Smith", "Smith");

        $org = Organization::create("WhatsApp","Manager");

        $phones = [
            Phone::create("+1 (940) 555-1234"),
            Phone::create("+1 (940) 555-1234", "HOME"),
            Phone::create("+1 (650) 555-1234", "WORK", "16505551234")
        ];

        $urls = [
            URL::create("https://botman.io"),
            URL::create("https://www.facebook.com", "WORK")
        ];

        $person = Contact::create($addresses, "2012-08-18", $emails, $name, $org, $phones, $urls);

        $message = ContactsMessage::create([
            $person
        ]);
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
    }
 

    public function testSendLocationMessage(){


        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

        $message = LocationMessage::create(-122.425332, 37.758056, "Facebook HQ", "1 Hacker Way, Menlo Park, CA 94025");
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
    }


    public function testSendLocationRequestMessage(){

        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

        $message = LocationRequestMessage::create('Please share your location');
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testTemplateMessage(){

        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

        $message = TemplateMessage::create('hello_world','en_us')
        ->addComponents(
            [
                ElementComponent::create('header',[]),
                ElementComponent::create('body',[]),
            ]
        );
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testInteractiveReplyButtonsMessage(){

        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

        $message =  InteractiveReplyButtonsMessage::create('How do you like BotMan so far?')
            ->addFooter('Powered by BotMan.io')
            ->addHeader(
                    ElementHeader::create('image',[
                        'link'=>"https://botman.io/img/botman.png",
                    ])
            )
            ->addButtons([
            ElementButton::create(1,'Quite good'),
            ElementButton::create(2,'Love it')
        ]);
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
    }


    public function testInteractiveCallToActionURLButton (){

        $responseData = '{}';
        $request = $this->getRequest($responseData);
        $http=new Curl();
        $config = [
            'whatsapp' => $this->config,
        ];
        $driver = new WhatsappDriver($request,$config,$http);
        $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

        $message =  InteractiveCallToActionURLButtonMessage::create(
            'Do you want to know more about BotMan?',
            "Visit us",
            "https://botman.io"
         )
        ->addFooter('Powered by BotMan.io')
        ->addHeader(
            ElementHeader::create('image',[
                'link'=>"https://botman.io/img/botman.png",
                ])
        );
        $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
        $this->assertEquals(200,$response->getStatusCode());
        
 }


 public function testInteractiveListMessage (){

    $responseData = '{}';
    $request = $this->getRequest($responseData);
    $http=new Curl();
    $config = [
        'whatsapp' => $this->config,
    ];
    $driver = new WhatsappDriver($request,$config,$http);
    $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');

    $sections=[];
    for ($i=0; $i <5; $i++) {
        $rows=[];
            for ($j=0; $j <2 ; $j++) {
                $text=bin2hex(random_bytes(10/ 2));
                $rows[]=ElementSectionListRow::create($text,$text.' '.$i+$j+1);
            }
        $sections[]=ElementSectionList::create('Events: Page '.$i+1,$rows);

    }
   
    $message =  InteractiveListMessage::create("Here is your  list of current Ticketbox listings",
        'Ticketbox listings',
        'The best place to buy tickets online',
        'View listings')
        ->addSections($sections);

    $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
    $this->assertEquals(200,$response->getStatusCode());

   }

   public function testSendReactionMessage()
   {
       $responseData = '{}';
       $request = $this->getRequest($responseData);
       $http=new Curl();
       $config = [
           'whatsapp' => $this->config,
       ];
       $driver = new WhatsappDriver($request,$config,$http);

   
       $message = new TextMessage('Reaction Test!');
       $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');
      
       $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
  
       $this->assertSame($this->config['to_phone_number'],json_decode($response->getContent(),true)['contacts'][0]['input']);
       $this->assertEquals(200,$response->getStatusCode());

       $message_id=json_decode($response->getContent(),true)['messages'][0]['id'];
       $message = ReactionMessage::create($message_id,'😀');
       $matchingMessage = new IncomingMessage('',$this->config['to_phone_number'], '');
       $response=$driver->sendPayload($driver->buildServicePayload($message,$matchingMessage));
       $this->assertEquals(200,$response->getStatusCode());
   }

   

}
