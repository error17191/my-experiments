<?php

namespace App\RequestBuilders;


class XMLRequestBuilder implements RequestBuilder
{
    /**
     * @var \DOMElement
     */
    protected $requestBody;

    /**
     * @var \DOMDocument
     * */
    protected $document;

    /**
     * class constructor
     *
     */
    public function __construct()
    {
        $this->document = new \DOMDocument();
    }

    /**
     * build the request
     *
     * @param array $data
     */
    public function buildRequest(array $data)
    {
        $this->composeBody($data);
    }

    /**
     * gets the request body as DOMElement
     *
     * @return \DOMElement
     */
    public function getRequestDOM(): \DOMElement
    {
        return $this->requestBody;
    }

    /**
     * gets the request body as XML string
     *
     * @return string
     */
    public function getRequestXML(): string
    {
        $document = new \DOMDocument();
        return $document->saveHTML($this->requestBody);
    }

    /**
     * create requestBody
     *
     * @param array $data
     * @param \DOMElement|null $parent
     */
    protected function composeBody(array $data,\DOMElement $parent = null){
        if(is_numeric_array($data)){
            foreach ($data as $datum) {
                $this->composeBody($datum,$parent);
            }
        }elseif(is_assoc_array($data)) {
            if (empty($data['children']) && !empty($data['text'])) {
                !empty($data['attributes']) ?
                    $this->composeField($data['name'], $data['text'], $parent, $data['attributes']) :
                    $this->composeField($data['name'], $data['text'], $parent);
            } elseif (!empty($data['children']) && empty($data['text'])) {
                if (!empty($data['attributes'])) {
                    $parentElement = $this->composeField($data['name'], null, $parent, $data['attributes']);
                } else {
                    $parentElement = $this->composeField($data['name'], null, $parent);
                }
                $this->composeBody($data['children'], $parentElement);
            }
        }
    }

    /**
     * create request field
     *
     * @param string $name
     * @param string|null $text
     * @param \DOMElement|null $parent
     * @param array|null $attributes
     *
     * @return \DOMElement
     */
    protected function composeField(string $name,string $text = null,\DOMElement $parent = null,array $attributes = null): \DOMElement
    {
        $element = $this->document->createElement($name,$text);
        if(count($attributes) > 0){
            foreach ($attributes as $name => $value) {
                $element->setAttribute($name,$value);
            }
        }
        if($parent){
            $parent->appendChild($element);
            return $element;
        }
        $this->requestBody = $element;
        return $element;
    }
}