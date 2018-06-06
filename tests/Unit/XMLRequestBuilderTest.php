<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\RequestBuilders\XMLRequestBuilder;

class XMLRequestBuilderTest extends TestCase
{

    public function test_XMLRequestBuilder_can_be_instantiated(){
        $request = new XMLRequestBuilder();
        $this->assertInstanceOf(XMLRequestBuilder::class,$request);
    }

    public function test_composeField_method_with_no_optional_parameters_inserted_return_a_correct_DOMElement(){
        $request = new XMLRequestBuilder();
        /** @var \DOMElement $element */
        $element = $this->invokeMethod($request,'composeField',[
            'name' => 'test',
        ]);
        $this->assertInstanceOf(\DOMElement::class,$element);
        $this->assertEquals($element->tagName,'test');
    }

    public function test_composeField_method_with_text_parameter_passed_returns_a_correct_DOMElement(){
        $request = new XMLRequestBuilder();
        /** @var \DOMElement $element */
        $element = $this->invokeMethod($request,'composeField',[
            'name' => 'test',
            'text' => 'some text'
        ]);
        $this->assertInstanceOf(\DOMElement::class,$element);
        $this->assertEquals($element->tagName,'test');
        $this->assertEquals($element->textContent,'some text');
    }

    public function test_composeField_method_with_attributes_parameter_passed_returns_a_correct_DOMElement(){
        $request = new XMLRequestBuilder();
        /** @var \DOMElement $element */
        $element = $this->invokeMethod($request,'composeField',[
            'name' => 'test',
            'text' => 'some text',
            'parent' => null,
            'attributes' =>[
                'attr1' => 'attr1 value',
                'attr2' => 'attr2 value'
            ]
        ]);
        $this->assertInstanceOf(\DOMElement::class,$element);
        $this->assertEquals($element->tagName,'test');
        $this->assertEquals($element->textContent,'some text');
        $this->assertTrue($element->hasAttribute('attr1'));
        $this->assertEquals($element->getAttribute('attr1'),'attr1 value');
        $this->assertTrue($element->hasAttribute('attr2'));
        $this->assertEquals($element->getAttribute('attr2'),'attr2 value');
    }

    public function test_composeBody_method_with_no_children_input_creates_the_correct_requsetBody(){
        $request = new XMLRequestBuilder();
        $data = [
            'name' => 'test',
            'text' => 'some text',
            'attributes' => [
                'attr1' => 'attr1 value',
                'attr2' => 'attr2 value'
            ]
        ];
        $this->invokeMethod($request,'composeBody',[
            'data' => $data
        ]);
        /** @var \DOMElement $element */
        $element = $request->getRequestDOM();
        $this->assertInstanceOf(\DOMElement::class,$element);
        $this->assertEquals($element->tagName,'test');
        $this->assertEquals($element->textContent,'some text');
        $this->assertTrue($element->hasAttribute('attr1'));
        $this->assertEquals($element->getAttribute('attr1'),'attr1 value');
        $this->assertTrue($element->hasAttribute('attr2'));
        $this->assertEquals($element->getAttribute('attr2'),'attr2 value');
    }

    public function test_composeBody_method_with_input_array_has_children_creates_correct_requestBody(){
        $request = new XMLRequestBuilder();
        $data = [
            'name' => 'firstLevel',
            'attributes' => [
                'firstLevelAttr1' => 'firstLevelAttr1 value',
                'firstLevelAttr2' => 'firstLevelAttr2 value'
            ],
            'children' => [
                [
                    'name' => 'secondLevelElement1',
                    'text' => 'some text for second level element 1',
                    'attributes' => [
                        'secondLevelAttr' => 'secondLevelAttr value'
                    ]
                ],
                [
                    'name' => 'secondLevelElement2',
                    'children' => [
                        [
                            'name' => 'thirdLevelElement',
                            'text' => 'some text for third level element',
                            'attributes' => [
                                'thirdLevelAttr' => 'thirdLevelAttr value'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->invokeMethod($request,'composeBody',[
            'data' => $data
        ]);
        /** @var \DOMElement $element */
        $element = $request->getRequestDOM();
        $this->assertInstanceOf(\DOMElement::class,$element);
        $this->assertEquals('firstLevel',$element->tagName);
        $this->assertTrue($element->hasAttribute('firstLevelAttr1'));
        $this->assertEquals('firstLevelAttr1 value',$element->getAttribute('firstLevelAttr1'));
        $this->assertTrue($element->hasAttribute('firstLevelAttr2'));
        $this->assertEquals('firstLevelAttr2 value',$element->getAttribute('firstLevelAttr2'));
        /** @var \DOMNodeList $secondLevelElement */
        $secondLevelElement = $element->getElementsByTagName('secondLevelElement1');
        $this->assertEquals(1,$secondLevelElement->length);
        $secondLevelElement = $secondLevelElement->item(0);
        /** @var \DOMElement $secondLevelElement */
        $this->assertEquals('secondLevelElement1',$secondLevelElement->tagName);
        $this->assertEquals('some text for second level element 1',$secondLevelElement->textContent);
        $this->assertTrue($secondLevelElement->hasAttribute('secondLevelAttr'));
        $this->assertEquals('secondLevelAttr value',$secondLevelElement->getAttribute('secondLevelAttr'));
        /** @var \DOMNodeList $thirdLevelElement */
        $thirdLevelElement = $element->getElementsByTagName('thirdLevelElement');
        $this->assertEquals(1,$thirdLevelElement->length);
        $thirdLevelElement = $thirdLevelElement->item(0);
        /** @var \DOMElement $thirdLevelElement */
        $this->assertEquals('thirdLevelElement',$thirdLevelElement->tagName);
        $this->assertEquals('some text for third level element',$thirdLevelElement->textContent);
        $this->assertTrue($thirdLevelElement->hasAttribute('thirdLevelAttr'));
        $this->assertEquals('thirdLevelAttr value',$thirdLevelElement->getAttribute('thirdLevelAttr'));
    }

    public function test_getRequestDOM_method_returns_correct_DOMElement(){
        $request = new XMLRequestBuilder();
        $data = [
            'name' => 'test',
            'text' => 'some text'
        ];
        $request->buildRequest($data);
        /** @var \DOMElement $element */
        $element = $request->getRequestDOM();
        $this->assertInstanceOf(\DOMElement::class,$element);
        $this->assertEquals('test',$element->tagName);
        $this->assertEquals('some text',$element->textContent);
    }

    public function test_getRequestXML_method_returns_the_correct_xml_string_for_the_request(){
        $request = new XMLRequestBuilder();
        $data = [
            'name' => 'test',
            'text' => 'some text'
        ];
        $request->buildRequest($data);
        /** @var string $xml */
        $xml = $request->getRequestXML();
        $this->assertInternalType('string',$xml);
        $document = new \DOMDocument();
        $testElement = $document->createElement('test','some text');
        $testXml = $document->saveXML($testElement);
        $this->assertEquals($testXml,$xml);
    }
}
