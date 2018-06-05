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
        $element = $request->getRequestDOM();
        $this->assertInstanceOf(\DOMElement::class,$element);
    }




}
