<?php

namespace App\RequestBuilders;

interface RequestBuilder
{
    /**
     * build the request
     *
     * @param array $data
     */
    public function buildRequest(array $data);

    /**
     * get the request body as a DOMElement
     *
     * @return \DOMElement
     */
    public function getRequestDOM(): \DOMElement ;

    /**
     * get the request body as a string
     *
     * @return string
     */
    public function getRequestXML(): string ;
}