<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ParseXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse';

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
        $xml = Storage::get('response.xml');
        $search = ['s:Envelope','s:Header','a:Action','s:Body'];
        $replace = ['Envelope','Header','Action','Body'];
        $xml = str_replace($search,$replace,$xml);
//        Storage::put('modified.xml',$xml);
        $data = simplexml_load_string($xml);
        $hotels = $data->Body->HotelSearchResponse->HotelResultList->HotelResult;
        $hotel = $hotels[0];
        print_r($hotel);
        exit;
        $this->info(count($hotels));
        $this->table($hotels);
    }
}
