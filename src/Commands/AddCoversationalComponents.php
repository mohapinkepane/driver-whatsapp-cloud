<?php

namespace BotMan\Drivers\Whatsapp\Commands;

use BotMan\BotMan\Http\Curl;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AddCoversationalComponents extends Command
{
     /** @var Collection */
     protected $config;

     /**
      * @var Curl
      */
     private $http;



    protected $endpoint = 'conversational_automation';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'botman:whatsapp:add-conversational-components';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add whatsapp messager conversational components.';

     /**
      * Create a new command instance.
      *
      * @param  Curl  $http
      */
     public function __construct(Curl $http)
     {
         parent::__construct();
         $this->http = $http;
         $this->config = Collection::make(config('botman.whatsapp'));
     }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        $payload = $this->config->get('conversational_components');

        if (!$payload) {
            $this->error('You need to add whatsapp conversational_components payload data to your BotMan Whatsapp config in whatsapp.php.');
            exit;
        }

        $response=$this->http->post($this->buildApiUrl($this->endpoint),$payload,[], $this->buildAuthHeader(), true);
        $responseObject = json_decode($response->getContent());

        if ($response->getStatusCode() == 200) {
            $this->info('Conversational components added successfully.');
        } else {
            $this->error('Something went wrong: '.$responseObject->error->message);
        }


    }


    public function buildAuthHeader()
    {
        $token = $this->config->get('token');
        return [
            "Authorization: Bearer " . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }


    protected function buildApiUrl($endpoint)
    {
        return $this->config->get('url') . '/' . $this->config->get('version') . '/' . $this->config->get('phone_number_id') . '/' . $endpoint;
    }


}
