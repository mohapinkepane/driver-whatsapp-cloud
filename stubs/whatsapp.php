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
     'version' => 'v25.0',

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
