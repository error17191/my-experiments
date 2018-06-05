<?php

namespace App\RequestBuilders;


class TBOXMLRequestBuilder extends XMLRequestBuilder
{
    /**
     * @var string
     */
    private $prefix = 'hot:';

    /**
     * get the prefix
     *
     * @return string
     */
    public function getPrefix(){
        return $this->prefix;
    }

    /**
     * set the value of the prefix
     *
     * @param string $prefix
     */
    public function setPrefix(string $prefix){
        $this->prefix = $prefix;
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
        if($this->prefix)$name = $this->prefix . $name;
        $element = new \DOMElement($name,$text);
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