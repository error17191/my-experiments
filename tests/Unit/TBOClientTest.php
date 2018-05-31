<?php

namespace Tests\Unit;

use App\Clients\TBOClient;
use App\Exceptions\Clients\TBO\InvalidRequestStructure;
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
        $expectedHTML = "<hot:HotelSearchRequest><hot:Guests>3</hot:Guests><hot:rooms>4</hot:rooms><hot:persons><hot:person><hot:name>Mohamed Ahmed</hot:name><hot:age>27</hot:age></hot:person><hot:person><hot:name>Ibrahim</hot:name><hot:age>34</hot:age></hot:person></hot:persons><hot:employee><hot:name>Ibrahim Ahmed</hot:name><hot:job>Software Engineer</hot:job></hot:employee></hot:HotelSearchRequest>";
        $client = new TBOClient(TBOClient::ACTION_HOTEL_SEARCH);
        $this->invokeMethod($client, 'composeBody', [
            'data' => [
                'Guests' => '3',
                'rooms' => '4',
                'persons' => [
                    ['name' => 'Mohamed Ahmed', 'age' => '27'],
                    ['name' => 'Ibrahim', 'age' => '34']
                ],
                'employee' => [
                    'name' => 'Ibrahim Ahmed',
                    'job' => 'Software Engineer'
                ]
            ]
        ]);
        /** @var \DOMElement $innerBody */
        $innerBody = $this->accessProp($client, 'requestXMLInnerBody');
        $doc = $this->accessProp($client, 'requestXMLDocument');
        $html = trim($doc->saveHTML($innerBody));
        $this->assertEquals($expectedHTML, $html);
        /** @var \DOMNodeList $name */
        $name = $innerBody->getElementsByTagName('hot:name');
        $this->assertEquals(3, $name->length);
        $person = $innerBody->getElementsByTagName('hot:person');
        $this->assertEquals(2, $person->length);
        /** @var \DOMElement $name1 */
        $name1 = $name->item(0);
        $this->assertEquals('Mohamed Ahmed', $name1->textContent);
        $name2 = $name->item(1);
        $this->assertEquals('Ibrahim', $name2->textContent);
    }

    public function test_composeBody_input_array_data_must_be_associative()
    {
        $this->expectException(InvalidRequestStructure::class);
        $client = new TBOClient(TBOClient::ACTION_HOTEL_SEARCH);
        $this->invokeMethod($client, 'composeBody', [[]]);
    }

    public function test_composeBody_numeric_array_cannot_contain_non_array_values()
    {
        $this->expectException(InvalidRequestStructure::class);
        $client = new TBOClient(TBOClient::ACTION_HOTEL_SEARCH);
        $this->invokeMethod($client, 'composeBody', [
            'data' => [
                'ones' => [['two'], ['three']]
            ]
        ]);
    }

    public function test_composeBody_dont_accept_objects_at_any_level()
    {
        $this->expectException(\TypeError::class);
        $obj = new \stdClass();
        $obj->key = 'value';
        $client = new TBOClient(TBOClient::ACTION_HOTEL_SEARCH);
        $this->invokeMethod($client, 'composeBody', [
            'data' => [
                'ones' => [
                    'two' => $obj
                ]
            ]
        ]);
    }
}
