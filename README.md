## BotMan WhatsApp Business Cloud API Driver

BotMan driver to connect WhatsApp Business Cloud API with [BotMan](https://github.com/botman/botman)


## WhatsApp Business Cloud API

Please read the official documentation at [Meta for Developer](https://developers.facebook.com/docs/whatsapp/cloud-api)

## Installation & Setup
First you need to pull in the Whatsapp Driver:

    composer require mohapinkepane/driver-whatsapp-cloud

Next you need to add to your .env file the following entries:

    WHATSAPP_ACCESS_TOKEN=your-whatsapp-access-token
    WHATSAPP_VERIFICATION=your-whatsapp-verification-token
    WHATSAPP_APP_SECRET=your-whatsapp-app-secret
    WHATSAPP_PHONE_NUMBER_ID=your-whatsapp-phone-number-id

This driver requires a valid and secure URL in order to set up webhooks and receive events and information from the chat users. This means your application should be accessible through an HTTPS URL.

>[ngrok](https://ngrok.com/) is a great tool to create such a public HTTPS URL for your local application. If you use Laravel Valet, you can create it with "valet share".If you use Laravel Herd, you can create it with "herd share" as well.[Serveo](https://serveo.net/) is also an excellent and headache free alternative - it is also entirely free.

To connect BotMan with WhatsApp Business, you first need to follow the official quick [start guide](https://developers.facebook.com/docs/whatsapp/cloud-api/get-started) to create your WhatsApp Business application and retrieve an access token as well as an app secret. Switch both of them with the dummy values in your BotMan .env file.

After that you can setup the webhook, which connects the Whatsapp application with your BotMan application. This is covered in the above mentioned Quick Start Guide.

## Configuring the package
You can publish the config file with:

    php artisan vendor:publish --provider="Botman\Drivers\Whatsapp\Providers\WhatsappServiceProvider"

This is the contents of the file that will be published at config/botman/whatsapp.php:

    <?php

    return [
        /*
        |--------------------------------------------------------------------------
        | Whatsapp url
        |--------------------------------------------------------------------------
        | Your whatsapp cloud api base url
        */
        'url' => env('WHATSAPP_PARTNER', 'https://graph.facebook.com'),

        /*
        |--------------------------------------------------------------------------
        | Whatsapp Token
        |--------------------------------------------------------------------------
        | Your Whatsapp access token  you received after creating
        | the application on Whatsapp(Facebook Portal).
        */
        'token' => env('WHATSAPP_ACCESS_TOKEN'),


        /*
        |--------------------------------------------------------------------------
        | Whatsapp App Secret
        |--------------------------------------------------------------------------
        |
        | Your Whatsapp application secret, which is used to verify
        | incoming requests from Whatsapp.
        |
        */
        'app_secret' => env('WHATSAPP_APP_SECRET'),

        /*
        |--------------------------------------------------------------------------
        | Whatsapp Verification
        |--------------------------------------------------------------------------
        | Your Whatsapp verification token, used to validate the webhooks.
        */
        'verification' => env('WHATSAPP_VERIFICATION'),

        /*
        |--------------------------------------------------------------------------
        | Whatsapp Phone Number ID
        |--------------------------------------------------------------------------
        | Your Whatsapp phone_number_id
        */
        'phone_number_id'=>env('WHATSAPP_PHONE_NUMBER_ID'),


        /*
        |--------------------------------------------------------------------------
        | Passphrase for whatsapp key pair
        |--------------------------------------------------------------------------
        | Only required if flows with end point are used,otherwise leave as is.
        */
        'passphrase'=>env('WHATSAPP_KEYS_PASSPHRASE'),
        

        /*
        |--------------------------------------------------------------------------
        | Whatsapp  Public Key
        |--------------------------------------------------------------------------
        | Public key uploaded to  whatsapp for encryption of flow end point data.
        | Only required if flows with end point are used,otherwise leave as is.
        */
        'public_key'=>env('WHATSAPP_PUBLIC_KEY'),


        /*
        |--------------------------------------------------------------------------
        | Whatsapp  Private Key
        |--------------------------------------------------------------------------
        | Private key used for decryption of flow end point data.
        | Only required if flows with end point are used,otherwise leave as is.
        */
        'private_key'=>env('WHATSAPP_PRIVATE_KEY'),


        /*
        |--------------------------------------------------------------------------
        | Whatsapp Cloud API Version
        |--------------------------------------------------------------------------
        */
        'version' => 'v20.0',

        /*
        |--------------------------------------------------------------------------
        | throw_http_exceptions
        |--------------------------------------------------------------------------
        | Do you want the driver to throw custom(driver) exceptions or the default exceptions
        */
        'throw_http_exceptions' => true,

        /*
        |--------------------------------------------------------------------------
        | restrict_inbound_messages_to_phone_number_id
        |--------------------------------------------------------------------------
        | Restrict inbound messages to this phone number id - ingnore all others
        */
        'restrict_inbound_messages_to_phone_number_id' => true,


        /*
        |--------------------------------------------------------------------------
        | Conversational Components
        |--------------------------------------------------------------------------
        | Configure whatsapp conversational components
        | See https://developers.facebook.com/docs/whatsapp/cloud-api/phone-numbers/conversational-components
        */
        'conversational_components' => [
            /*
            | Enable or disable whatsapp welcome messages
            */
            'enable_welcome_message' => false,

            /*
            | Whatsapp commands list
            */
        "commands"=> [
                [
                "command_name"=> "hello",
                    "command_description"=> "Say hello",
                ],
                [
                "command_name"=> "help",
                    "command_description"=> "Request help",
                ]
            ],

            /*
            | Whatsapp prompts (Ice breakers) list
            */
        "prompts"=> ["Book a flight","plan a vacation"],
        ]

    ];




## Business-scoped user IDs

WhatsApp can now send business-scoped user IDs (BSUIDs) in webhook payloads.

This driver supports both legacy phone-number-based identifiers and the newer BSUID fields.

- `IncomingMessage::getSender()` may return a BSUID, parent BSUID, or a phone number depending on what WhatsApp provides.
- `wa_id` and `from` should be treated as optional legacy phone-number fields.
- The driver prefers `user_id` / `from_user_id` and falls back to `parent_user_id` / `from_parent_user_id`, then to phone-number-based identifiers.
- Outbound replies remain backward-compatible: the driver uses `to` for phone numbers and `recipient` for BSUIDs automatically.

If you need user details from the driver, you can access them from the WhatsApp user object:

    $user = $bot->getUser();

    $user->getId();                // Canonical identifier used by the driver
    $user->getUserId();            // BSUID when available
    $user->getParentUserId();      // Parent BSUID when available
    $user->getPhoneNumber();       // Phone number when available
    $user->getWA_ID();             // Legacy alias for phone number
    $user->getWhatsAppName();
    $user->getWhatsAppUsername();  // Username when available

Incoming message extras also expose the resolved identifiers:

    $message->getSender();
    $message->getExtras('user_id');
    $message->getExtras('parent_user_id');
    $message->getExtras('phone_number');
    $message->getExtras('wa_id');

## Supported Features

- [x] Text Message
- [x] Contact Message
- [x] Location Message
- [x] Reaction Message
- [x] Template Message
- [x] Image Attachment
- [x] Document Attachment
- [x] Location Attachment
- [x] Video Attachment
- [x] Audio Attachment
- [x] Sticker Attachment
- [x] Call To Action
- [x] Interactive Messages
    - [x] List
    - [x] Reply Button
    - [x] Location Request
    - [x] Flows
    

<!-- ### TODO:
- [ ] Interactive Message
    - [ ] Product
    - [ ] Product List -->

## Sending Whatsapp Messages

>Facebook is still experimenting a lot with its Whatsapp features. This is why some of them behave differently on certain platforms.In general it is easy to say that all of them work within the native Whatsapp App on your phones. But e.g. the List message is not working inside the Whatsapp Desktop App.

### Text

You can send text as follows

    $bot->reply(
        TextMessage::create('Please visit https://youtu.be/hpltvTEiRrY to inspire your day!')
        ->previewUrl(true)//Allows whatsapp to show the preview of the url(video in this case)
     );

OR more powerfully in a conversation like this

    $this->ask('Hello! What is your firstname?', function(Answer $answer) {
        $this->firstname = $answer->getText();
        $this->say(
                TextMessage::create('Nice to meet you '.$this->firstname)
                ->contextMessageId($answer->getMessage()->getExtras('id'))
            );
    });

<!-- This is to add message context to achieve something like this -->

<!-- ![Message Context](/assets/images/message-context.png) -->


### Media

You can still attach media to messages like the docs say [here](https://botman.io/2.0/sending#attachments)
,but this will limit you to images,videos,audio and files.

ALTERNATIVELY there is a MediaMessage class.it supports video,image,document,sticker and audio.The cool thing about it is that you can add caption and filename where applicable.You can also chain the contextMessageId() method to provide context.

It can be used in two ways

    1. MediaMessage::create('media-type-here')
        ->url('media-url-here')

    2. MediaMessage::create('media-type-here')
        ->id('media-id-here')//Whatsapp media id

Examples below

    $bot->reply(
        MediaMessage::create('image')
    ->url('https://images.pexels.com/photos/1266810/pexels-photo-1266810.jpeg')
    ->caption('This is a cool image!')
    );

    $bot->reply(
        MediaMessage::create('audio')
    ->url('https://samplelib.com/lib/preview/mp3/sample-15s.mp3')
    );

    $bot->reply(
        MediaMessage::create('document')
    ->url('https://pdfobject.com/pdf/sample.pdf')
    ->caption('This is a cool Document!')
    );

    $bot->reply(
        MediaMessage::create('sticker')
    ->url('https://stickermaker.s3.eu-west-1.amazonaws.com/storage/uploads/sticker-pack/meme-pack-3/ sticker_18.webp')
    );

    $bot->reply(
        MediaMessage::create('video')
    ->url('https://sample-videos.com/video321/mp4/480/big_buck_bunny_480p_10mb.mp4')
    ->caption('This is a cool Video!')
    );



### List

You can send a list message (in a conversation) as follows

     $this->ask(InteractiveListMessage::create("Here is your  list of current Ticketbox listings",'View listings')
            ->addHeader('Ticketbox listings')
            ->addFooter('Powered by Ticketbox.co.ls')
            ->addSection(ElementSectionList::create('Events',[
                        ElementSectionListRow::create(
                        1,//List item id
                        'Selemo Sa Basotho'////List item title
                        )
                        ->description('In 2024, we commemorate 200 years since the Basotho nation arrived..')
                        ,
                        ElementSectionListRow::create(2,'Winterfest')
                        ->description('vibrant cultural performances, music, food and a colossal Bonfire '),
                ])
            )
            ->addSection(ElementSectionList::create('Vouchers',[
                    ElementSectionListRow::create(3,'Kobo ea Seanamarena'),
                ])
     ),function(Answer $answer) {
        $payload = $answer->getMessage()->getPayload();//Get Payload
        $choice_id=$answer->getMessage()->getExtras('choice_id');//You can the select choice ID like this
        $choice_text=$answer->getMessage()->getExtras('choice_text');
        $choice=$answer->getText();
        $this->say(
                TextMessage::create('Nice.You choose '.$choice)
                ->contextMessageId($answer->getMessage()->getExtras('id'))
            );
     });


<!-- ![List](/assets/images/list.png) -->


### Reply Button

You can send a reply button message (in a conversation) as follows

    $this->ask(InteractiveReplyButtonsMessage::create('How do you like BotMan so far?')
            ->addFooter('Powered by BotMan.io')
            ->addHeader(
                    ElementHeader::create('image',[
                        'link'=>"https://botman.io/img/botman.png",
                    ])
            )
            ->addButtons([
            ElementButton::create(1,'Quite good'),
            ElementButton::create(2,'Love it')
        ]),function(Answer $answer) {
            $payload = $answer->getMessage()->getPayload();//Get Payload
            $choice_id=$answer->getMessage()->getExtras('choice_id');//You can the get choice ID like this
            $choice_text=$answer->getMessage()->getExtras('choice_text');
            $choice=$answer->getText();
            $this->say(
                    TextMessage::create('Nice.You choose '.$choice)
                    ->contextMessageId($answer->getMessage()->getExtras('id'))
                );
        });

The header can be of type text,image,video or document


<!-- ![Reply Buttons](/assets/images/reply-buttons.png) -->

### Flows

You can send a flow message (in a conversation) as follows

    $this->ask(FlowMessage::create(
            'FLOW_ID',//Unique ID of the Flow provided by WhatsApp
            'FLOW_TOKEN',//Generated by the business to serve as an identifier
            'Take a quick survey',//Text on the CTA button.
            'How do you like BotMan so far?',//flow body text
            'draft'// flow mode -> published is default
            'navigate'// Flow action -> navigate is default
        )
        ->addFooter('Powered by BotMan.io')
        ->addHeader(
                    ElementHeader::create('image',[
                        'link'=>"https://botman.io/img/botman.png",
                    ])
            )
        ->addActionPayload(
            ElementFlowActionPayload::create('RECOMMEND' //First screen name
            ,[
                'title' => 'hello',
            ]//Payload)
        )
        ,function(Answer $answer) {
         $payload = $answer->getMessage()->getPayload();
         $this->say('Thanks!');
    });

The header can be of type text,image,video or document

### Flows with endpoint

(1) Generate RSA key pair

    php artisan botman:whatsapp:generate:keypair {passphrase}

(2) Copy the keys and passphrase to .env file

    WHATSAPP_KEYS_PASSPHRASE=passpharase_here
    WHATSAPP_PUBLIC_KEY=public_key_here
    WHATSAPP_PRIVATE_KEY=private_key_here


You may need to cache configs

    php artisan config:cache

(3) Add key to whatsapp

    php artisan botman:whatsapp:add-public-key


(4)  Implement flow data handling logic - Using a custom controller

   1. Add custom route in web.php 

            Route::post('/custom-url', [CustomController::class, 'handleFlow']);//The method must be handleFlow
        
   2. Exclude the route from CSRF protection

   3. Make a custom controller that extents Flowprocessor
            
            <?php

            namespace App\Http\Controllers;
            use Botman\Drivers\Whatsapp\Http\FlowProcessor;

            class CustomController extends FlowProcessor
            {
            
                private const SCREEN_RESPONSES = [];

                /**
                * @param array $decrypted_body
                * @return array
                */
                public function getNextScreen($decrypted_body) {

                        $screen = $decrypted_body['screen'] ?? null;
                        $data = $decrypted_body['data'] ?? [];
                        $version = $decrypted_body['version'] ?? null;
                        $action = $decrypted_body['action'] ?? null;
                        $flow_token = $decrypted_body['flow_token'] ?? null;

                        //Custom code here
                        if ($action === 'INIT') {
                            return [];
                        }


                        if ($action === 'data_exchange') {
                            return [];
                        }
                        //Custom code here

                    \Log::error('Unhandled request body:', $decrypted_body);
                    throw new \Exception('Unhandled endpoint request. Make sure you handle the request action & screen logged above.');

                }
            }
                    

(5)  Implement flow data handling logic - using [Spatie webhook client](https://github.com/spatie/laravel-webhook-client)

   1. Follow and read package documentation [here](https://github.com/spatie/laravel-webhook-client)

   2. Implement custom RespondsToWebhook class

            <?php

            namespace App\WebHooks;

            use Illuminate\Http\Request;
            use Spatie\WebhookClient\WebhookConfig;
            use Symfony\Component\HttpFoundation\Response;
            use Botman\Drivers\Whatsapp\Http\FlowProcessor;
            use Spatie\WebhookClient\WebhookResponse\RespondsToWebhook;

            class CustomFlowRespondsTo  extends FlowProcessor implements RespondsToWebhook
            {

                private const SCREEN_RESPONSES = [];

            
                public function respondToValidWebhook(Request $request, WebhookConfig $config): Response
                {
                    return $this->handleFlow($request);//Leave as it is
                }

                /**
                * @param array $decrypted_body
                * @return array
                */
                public function getNextScreen($decrypted_body) {

                    $screen = $decrypted_body['screen'] ?? null;
                    $data = $decrypted_body['data'] ?? [];
                    $version = $decrypted_body['version'] ?? null;
                    $action = $decrypted_body['action'] ?? null;
                    $flow_token = $decrypted_body['flow_token'] ?? null;

                    //Custom code here
                    if ($action === 'INIT') {
                        return [];
                    }

                    if ($action === 'data_exchange') {
                        return [];
                    }
                    //Custom code here

                \Log::error('Unhandled request body:', $decrypted_body);
                throw new \Exception('Unhandled endpoint request. Make sure you handle the request action & screen logged above.');

                }
            }

   3. Implement custom WebhookProfile class
                
            <?php
                namespace App\WebHooks;
                use Log;
                use Illuminate\Http\Request;
                use Botman\Drivers\Whatsapp\Traits\MatchesFlowProfile;
                use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

                class WhatsappFlowWebhookProfile implements WebhookProfile
                {
                    use MatchesFlowProfile;
                    
                    public function shouldProcess(Request $request): bool
                    {
                        return $this->matchesFlowProfile($request);
                    }
                }

   4. Implement custom SignatureValidator class


                <?php
                    namespace App\WebHooks;

                    use Illuminate\Http\Request;
                    use Spatie\WebhookClient\WebhookConfig;
                    use Spatie\WebhookClient\Exceptions\InvalidConfig;
                    use Botman\Drivers\Whatsapp\Traits\ValidatesFlowSignature;
                    use Spatie\WebhookClient\SignatureValidator\SignatureValidator;

                    class WhatsappSignatureValidator implements SignatureValidator
                    {
                        use ValidatesFlowSignature;
                        public function isValid(Request $request, WebhookConfig $config): bool
                        {
                            return $this->validatesSignature($request);
                        }
                    }

        



### Template

You can send a template message as shown in the examples below.
These are just examples of course,but you can implement pretty much anything you want.

Example (A) (using the default hello_world template) 

     $this->say(TemplateMessage::create('hello_world','en_us')
     ->addComponents(
         [
             ElementComponent::create('header',[]),
             ElementComponent::create('body',[]),
         ]
     ));
     
Example (B) (using the default purchase_receipt_1 template) 

    $this->say(TemplateMessage::create('purchase_receipt','en_us')
    ->addComponents(
        [
            ElementComponent::create('header',[
                    [
                    'type'=>'document',
                    'document'=>[
                        "link"=>"https://pdfobject.com/pdf/sample.pdf"
                    ]
                ]
            ]),
            ElementComponent::create('body',[
                [
                    "type"=> "currency",
                    "currency"=>[
                        "fallback_value"=> "$100.99",
                        "code"=> "USD",
                        "amount_1000"=> 100990
                    ]
                ],
                [
                    "type"=>"text",
                    "text"=>"Ticketbox-Thetsane Office Park,Maseru,Lesotho.",
                ],
                [
                    "type"=>"text",
                    "text"=>"ticket",
                ]
            ]),
        ]
    ));


Example (C) (using the default fraud_alert template -in a conversation) 

    $this->ask(TemplateMessage::create('fraud_alert','en_us')
    ->addComponents(
        [
            ElementComponent::create('header',[]),
            ElementComponent::create('body',[
                [
                    "type"=>"text",
                    "text"=>"John Miller Doe",
                ],
                [
                    "type"=>"text",
                    "text"=>"Dummy Company",
                ],
                [
                    "type"=>"text",
                    "text"=>"Spooky",
                ],
                [
                    "type"=>"text",
                    "text"=>"Dummy Company",
                ],
                [
                    "type"=>"text",
                    "text"=>"D4SRT",
                ],
                [
                    "type"=> "date_time",
                    "date_time" => [
                        "fallback_value"=> "February 25, 1977",
                    ]
                ],
                [
                    "type"=>"text",
                    "text"=>"Dummy Merchant",
                ],
                [
                    "type"=> "currency",
                    "currency"=>[
                        "fallback_value"=> "$100.99",
                        "code"=> "USD",
                        "amount_1000"=> 100990
                    ]
                ]
            ]),
        ]
    ),
    function(Answer $answer) {
        $payload = $answer->getMessage()->getPayload();
        \Log::info('PAYLOAD'.\json_encode($payload));
        $this->say('Thanks!');
    });


### Call To Action

You can send a call to action as follows

    $bot->reply(InteractiveCallToActionURLButtonMessage::create(
        'Do you want to know more about BotMan?',//Call to action body
        "Visit us", //Call to action button text
        "https://botman.io"//Call to action url
    )
    ->addFooter('Powered by BotMan.io')
    ->addHeader(
        ElementHeader::create('image',[
            'link'=>"https://botman.io/img/botman.png",
        ])
    ));

The header can be of type text,image,video or document

<!-- ![Call To Action](/assets/images/call-to-action.png) -->


### Reaction 

You can react to messages as follows

    $this->ask('Hello! Do you read me?', function(Answer $answer) {
        $message_id=$answer->getMessage()->getExtras('id');
        $this->say(
            ReactionMessage::create($message_id,'😀')
        );
    });


### Contacts

You can send contacts as follows


    $addresses = [
        Address::create("Menlo Park", "United States"),
        // Address::create("Menlo Park", "United States", "us", "CA", "1 Hacker Way", "HOME", "94025"),
        // Address::create("Menlo Park", "United States", "us", "CA", "200 Jefferson Dr", "WORK", "94025")
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
        URL::create("https://www.google.com"),
        URL::create("https://www.facebook.com", "WORK")
    ];

    $person = Contact::create($addresses, "2012-08-18", $emails, $name, $org, $phones, $urls);

    $bot->reply(
        ContactsMessage::create([
            $person
        ])
    );


### Location

You can send location as follows

    $bot->reply(
        LocationMessage::create(-122.425332, 37.758056, "Facebook HQ", "1 Hacker Way, Menlo Park, CA 94025")
    );

### Location Request 

You can  send a location request as follows

    $this->ask(LocationRequestMessage::create('Please share your location'), function(Answer $answer) {
            $payload = $answer->getMessage()->getPayload();
            \Log::info('PAYLOAD'.\json_encode($payload));
            $this->say('Thanks!');
    });


## Message Context

You can send any type of message as a reply to a previous message. The previous message will appear at the top of the new message, quoted within a contextual bubble.The limitations are discussed [here](https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-messages).

You can achieve this by chaining  the method: contextMessageId('message-id-here').Example provided below.

    $this->say(
            TextMessage::create('reply-here')
            ->contextMessageId('message-id-here')
        );

## Message ID

The IncomingMessage class contains the message ID in its extras.
You can get it by calling the method: getExtras('id') on an instance of this class.

When WhatsApp sends business-scoped user IDs, you can also inspect these extras on the same message instance:

    $message->getSender();               // BSUID, parent BSUID, or phone number
    $message->getExtras('user_id');
    $message->getExtras('parent_user_id');
    $message->getExtras('phone_number');
    $message->getExtras('wa_id');


## Mark seen

The markSeen() method takes a parameter of type IncomingMessage and can be use in serveral ways:

In receiving(recieved) Middleware

    public function received(IncomingMessage $message,$next, BotMan $bot)
        {
            if($bot->getDriver()->getName()=='Whatsapp'){
                $bot->markSeen($message);
            }
            return $next($message);
        }


In a coversation

    $this->ask('Hello! What is your firstname?', function(Answer $answer) {
        $this->bot->markSeen($answer->getMessage());
        $this->firstname = $answer->getText();
        $this->say('Nice to meet you '.$this->firstname);
    });

## Conversational Components

>[Conversational components](https://developers.facebook.com/docs/whatsapp/cloud-api/phone-numbers/conversational-components/) are in-chat features that you can enable on business phone numbers. They make it easier for WhatsApp users to interact with your business. You can configure easy-to-use commands, provide pre-written ice breakers that users can tap, and greet first time users with a welcome message.

### Welcome messages

If you enable this [feature](https://developers.facebook.com/docs/whatsapp/cloud-api/phone-numbers/conversational-components/) and a user messages you, the WhatsApp client checks for an existing message thread between the user and your business phone number. If there is none, the client triggers a messages webhook with type set to request_welcome.

To enable/disable [welcome messages](https://developers.facebook.com/docs/whatsapp/cloud-api/phone-numbers/conversational-components/) in your bot. First edit the variable enable_welcome_message in your config/botman/whatsapp.php file to suit your need.

Then use the Artisan command:

    php artisan botman:whatsapp:add-conversational-components


You can read and act on the request_welcome message as follows

In receiving(recieved) Middleware

    public function received(IncomingMessage $message,$next, BotMan $bot)
    {
        if($bot->getDriver()->getName()=='Whatsapp'){
            
              if($message->getExtras('type') == 'request_welcome'){
                $bot->say('Hello ! Welcome to my bot',[$message->getSender()]);
              }
        }
        return $next($message);
    }

The sender returned by `$message->getSender()` can be a BSUID instead of a phone number. The driver will still route replies correctly.

In a coversation

    if($this->bot->getMessage()->getExtras('type') == 'request_welcome'){
        $this->say('Hello ! Welcome to my bot');
    }

If you do not handle it, the message will be handled by the botman global fallback route - if it available that is.


### Commands

To add [commands](https://developers.facebook.com/docs/whatsapp/cloud-api/phone-numbers/conversational-components) to your bot. First define the structure of your commands in your config/botman/whatsapp.php file. There you will find a commands demo payload. Just edit it to your needs.

Then use the Artisan command:

    php artisan botman:whatsapp:add-conversational-components

### Prompts - Icebreakers

To add [prompts](https://developers.facebook.com/docs/whatsapp/cloud-api/phone-numbers/conversational-components) to your bot. First define your prompts in your config/botman/whatsapp.php file. There you will find a prompts demo payload. Just edit it to your needs.

Then use the Artisan command:

    php artisan botman:whatsapp:add-conversational-components

## Contributing
Please see [CONTRIBUTING](https://github.com/mohapinkepane/driver-whatsapp-cloud//blob/master/CONTRIBUTING.md) for details.


## Testing

    composer unit-test


You can also run tests making real calls to the WhastApp Cloud API. Please put your testing credentials in WhatsappDriverConfig file.

    composer integration-test
 
If you are not recieving any massges but all the tests have passed. Check [Customer service window](https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-messages).

## Credits

- [irwan-runtuwene](https://github.com/irwan-runtuwene/driver-whatsapp)
- [rivaisali](https://github.com/rivaisali/driver-whatsapp)


## Security Vulnerabilities

If you discover a security vulnerability within BotMan, please send an e-mail to Marcel Pociot at m.pociot@gmail.com. All security vulnerabilities will be promptly addressed.

## License

BotMan is free software distributed under the terms of the [MIT license](https://github.com/mohapinkepane/driver-whatsapp-cloud//blob/README.md).
