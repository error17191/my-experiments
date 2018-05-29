<?php

namespace Tests\Unit;

use App\Clients\TBOClient;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TBOClientTest extends TestCase
{

    public function test_TBOClient_is_instantiable()
    {
        $client = new TBOClient('SomeAction');
        $this->assertInstanceOf(TBOClient::class, $client);
    }

    public function test_TBOClient_constructor_generates_well_structured_xml_request()
    {
        $action = 'SomeAction';
        $client = new TBOClient($action);
        /** @var \DOMDocument $xml */
        $xml = $this->accessProp($client, 'requestXMLDocument');
        $this->assertInstanceOf(\DOMDocument::class, $xml);

        $env = $xml->getElementsByTagName('soap:Envelope');
        $this->assertEquals(1, $env->length);
        /** @var \DOMElement $envElement */
        $envElement = array_first($env);
        $body = $envElement->getElementsByTagName("soap:Body");
        $this->assertEquals(1, $body->length);
        /** @var \DOMElement $bodyElement */
        $bodyElement = array_first($body);

        $innerBody = $bodyElement->getElementsByTagName("hot:{$action}Request");
        $this->assertEquals(1, $innerBody->length);
    }

    public function test_composeBody_method_can_create_well_structured_xml_for_flat_arrays()
    {
        $client = new TBOClient('Action');
        $this->invokeMethod($client, 'composeBody', [
            'data' => [
                'Guests' => '3',
                'rooms' => '4',
                'name' => 'Mohamed Ahmed'
            ]
        ]);
        /** @var \DOMElement $innerBody */
        $innerBody = $this->accessProp($client, 'requestXMLInnerBody');
        $guests = $innerBody->getElementsByTagName("hot:Guests");
        $this->assertEquals(1, $guests->length);
        /** @var \DOMElement $guestsElement */
        $guestsElement = array_first($guests);
        $this->assertEquals('3', $guestsElement->textContent);
        $this->assertEquals('hot:Guests', $guestsElement->tagName);

        $rooms = $innerBody->getElementsByTagName("hot:rooms");
        $this->assertEquals(1, $guests->length);
        /** @var \DOMElement $guestsElement */
        $roomsElement = array_first($rooms);
        $this->assertEquals('4', $roomsElement->textContent);
        $this->assertEquals('hot:rooms', $roomsElement->tagName);

        $name = $innerBody->getElementsByTagName("hot:name");
        $this->assertEquals(1, $guests->length);
        /** @var \DOMElement $guestsElement */
        $nameElement = array_first($name);
        $this->assertEquals('Mohamed Ahmed', $nameElement->textContent);
        $this->assertEquals('hot:name', $nameElement->tagName);


    }

    public function test_compose_body()
    {
        $client = new TBOClient(TBOClient::ACTION_HOTEL_SEARCH);
        $this->invokeMethod($client, 'composeBody', [
            'data' => [
                'Guests' => '3',
                'rooms' => '4',
                'person' => [
                    'name' => 'Mohamed Ahmed'
                ]
            ]
        ]);
        /** @var \DOMElement $innerBody */
        $innerBody = $this->accessProp($client, 'requestXMLInnerBody');
        die((string)$innerBody);
    }
}
