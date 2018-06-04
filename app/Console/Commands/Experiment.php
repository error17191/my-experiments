<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Experiment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'experiment';

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
        $time = time();
        $xml = new \DOMDocument("1.0", "UTF-8");

        $xml_env = $xml->createElement("soap:Envelope");
        $xml_env->setAttribute("xmlns:soap", "http://www.w3.org/2003/05/soap-envelope");
        $xml_env->setAttribute("xmlns:hot", "http://TekTravel/HotelBookingApi");

        /*create header*/
        $xml_hed = $xml->createElement("soap:Header");
        $xml_hed->setAttribute("xmlns:wsa", "http://www.w3.org/2005/08/addressing");

        $xml_cred = $xml->createElement("hot:Credentials");
        $xml_cred->setAttribute("UserName", "syal");
        $xml_cred->setAttribute("Password", "Syal@878");

        $xml_wsaa = $xml->createElement("wsa:Action", "http://TekTravel/HotelBookingApi/HotelSearch");
        $xml_wsat = $xml->createElement("wsa:To", "http://api.tbotechnology.in/hotelapi_v7/hotelservice.svc");

        $xml_hed->appendChild($xml_cred);
        $xml_hed->appendChild($xml_wsaa);
        $xml_hed->appendChild($xml_wsat);

        $xml_env->appendChild($xml_hed);

        /*create body*/
        $xml_bdy = $xml->createElement("soap:Body");
        $xml_bdyreq = $xml->createElement("hot:HotelSearchRequest");
//        $xml_bdyreqele = $xml->createElement("hot:CheckInDate", "2018-07-25");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:CheckOutDate", "2018-07-26");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:CountryName", "United Arab Emirates");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:CityName", "Dubai");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:CityId", "25921");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:NoOfRooms", "1");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:GuestNationality", "AE");
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:RoomGuests");
//        $xml_bdyreqinnerele = $xml->createElement("hot:RoomGuest");
//        $xml_bdyreqinnerele->setAttribute("AdultCount", "1");
//        $xml_bdyreqele->appendChild($xml_bdyreqinnerele);
//        $xml_bdyreq->appendChild($xml_bdyreqele);
//
//        $xml_bdyreqele = $xml->createElement("hot:Filters");
//        $xml_bdyreqinnerele = $xml->createElement("hot:StarRating", "All");
//        $xml_bdyreqele->appendChild($xml_bdyreqinnerele);
//        $xml_bdyreq->appendChild($xml_bdyreqele);

        $xml_bdy->appendChild($xml_bdyreq);
        $xml_env->appendChild($xml_bdy);

        $xml->appendChild($xml_env);
        $request = $xml->saveXML();

        $location = "http://api.tbotechnology.in/hotelapi_v7/hotelservice.svc";
        $action = "http://TekTravel/HotelBookingApi/HotelSearch";
        $client = new \SoapClient("http://api.tbotechnology.in/hotelapi_v7/hotelservice.svc?wsdl");
        $resp = $client->__doRequest($request, $location, $action, 2);
        echo $resp;

        exit;
        $xml = simplexml_load_string($resp, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $data = json_decode($json,TRUE);
        print_r($data);
        echo time() - $time;

    }
}
