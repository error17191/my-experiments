<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Clients\TBOClient;
use Illuminate\Support\Facades\Storage;

class testClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testClient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new TBOClient(TBOClient::ACTION_HOTEL_SEARCH);
        $client->makeRequest();
        echo $client->responseXML();
    }
}
