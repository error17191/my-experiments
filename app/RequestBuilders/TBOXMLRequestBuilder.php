<?php

namespace App\RequestBuilders;


class TBOXMLRequestBuilder extends XMLRequestBuilder
{

    /**
     * overrides method App/XMLRequestBuilder::composeField()
     * create request field
     *
     * @param string $name
     * @param string|null $text
     * @param \DOMElement|null $parent
     * @param array|null $attributes
     * @param string|'hot:' $prefix
     *
     * @return \DOMElement
     */
    protected function composeField(string $name,string $text = null,\DOMElement $parent = null,array $attributes = null,$prefix = 'hot:'): \DOMElement
    {
        return parent::composeField($name,$text,$parent,$attributes,$prefix);
    }
}