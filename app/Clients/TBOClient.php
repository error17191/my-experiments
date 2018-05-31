<?php

namespace App\Clients;

use App\Exceptions\Clients\TBO\InvalidRequestStructure;

class TBOClient
{
    //Request Parts

    /**
     * @var \DOMDocument
     */
    private $requestXMLDocument;
    /**
     * @var \DOMElement
     */
    private $requestXMLEnv;
    /**
     * @var \DOMElement
     */
    private $requestXMLHead;
    /**
     * @var \DOMElement
     */
    private $requestXMLBody;
    /**
     * @var \DOMElement
     */
    private $requestXMLInnerBody;

    // End of Request Parts

    /**
     * @var string
     */
    private $action;

    // Action Constants
    const ACTION_HOTEL_SEARCH = 'HotelSearch';

    /**
     * TBOClient constructor.
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;

        $this->requestXMLDocument = new \DOMDocument("1.0", "UTF-8");
        //Base Env Element
        $this->requestXMLEnv = $this->requestXMLDocument->createElement("soap:Envelope");
        $this->requestXMLEnv->setAttribute("xmlns:soap", "http://www.w3.org/2003/05/soap-envelope");
        $this->requestXMLEnv->setAttribute("xmlns:hot", "http://TekTravel/HotelBookingApi");
        //Head
        $this->requestXMLHead = $this->requestXMLDocument->createElement("soap:Header");
        $this->requestXMLHead->setAttribute("xmlns:wsa", "http://www.w3.org/2005/08/addressing");
        //Credentials
        $xml_cred = $this->requestXMLDocument->createElement("hot:Credentials");
        $xml_cred->setAttribute("UserName", "syal");
        $xml_cred->setAttribute("Password", "Syal@878");
        $this->requestXMLHead->appendChild($xml_cred);
        //Action
        $xml_wsaa = $this->requestXMLDocument->createElement("wsa:Action", "http://TekTravel/HotelBookingApi/{$action}");
        $xml_wsat = $this->requestXMLDocument->createElement("wsa:To", "http://api.tbotechnology.in/hotelapi_v7/hotelservice.svc");
        //Adding credentials and Action to head
        $this->requestXMLHead->appendChild($xml_wsaa);
        $this->requestXMLHead->appendChild($xml_wsat);
        // Adding Head to Base Env
        $this->requestXMLEnv->appendChild($this->requestXMLHead);
        //Body
        $this->requestXMLBody = $this->requestXMLDocument->createElement("soap:Body");
        $this->requestXMLInnerBody = $this->requestXMLDocument->createElement("hot:{$action}Request");
        $this->requestXMLBody->appendChild($this->requestXMLInnerBody);
        $this->requestXMLEnv->appendChild($this->requestXMLBody);
        $this->requestXMLDocument->appendChild($this->requestXMLEnv);
    }

    /**
     * Sets the fields of the body request
     *
     * @param array $data
     * @return TBOClient
     */
    public function body(array $data): TBOClient
    {
        $this->composeBody($data);
        return $this;
    }


    /**
     * Composes the inner body details of the request given array of data
     *
     * @param array $data
     * @param \DOMElement|null $parent
     * @param string|null $elementKey
     * @param bool|false $parentIsNumeric
     */
    private function composeBody(array $data,\DOMElement $parent = null, string $elementKey = null,bool $parentIsNumeric = false)
    {
//        if(null === $parent && is_numeric_array($data)){
//            throw new InvalidRequestStructure();
//        }
        if(null === $parent && is_numeric_array($data)){
            throw new InvalidRequestStructure();
        }

        if(is_numeric_array($data)){
            $parentIsNumeric = true;
            foreach ($data as $datum) {
                if(gettype($datum) !== 'array'){
                    $this->composeField($elementKey,$datum,$parent);
                }else{
                    if($parentIsNumeric && is_numeric_array($datum)){
                        throw new InvalidRequestStructure();
                    }
                    $parentElement = $this->composeField($elementKey,'',$parent);
                    $this->composeBody($datum,$parentElement,null,$parentIsNumeric);
                }
            }
        }else{
            foreach ($data as $key => $value){
                if(gettype($value) !== 'array'){
                    $this->composeField($key,$value,$parent);
                }elseif (is_numeric_array($value)){
                    $parentElement = $this->composeField($key,'',$parent);
                    $singularKey = substr($key,0,strlen($key)-1);
                    $this->composeBody($value,$parentElement,$singularKey);
                }else{
                    $parentElement = $this->composeField($key,'',$parent);
                    $this->composeBody($value,$parentElement);
                }
            }
        }
    }

    /**
     * Creates a DOMElement in the required format from a key,value pair
     *
     * @param string $key
     * @param string|null $value
     * @param \DOMElement|null $parent
     */

    private function composeField(string $key, string $value = null, \DOMElement $parent = null)
    {
        $element = $this->requestXMLDocument->createElement("hot:{$key}", $value);
        if ($parent) {
            $parent->appendChild($element);
            return $element;
        }
        $this->requestXMLInnerBody->appendChild($element);
        return $element;
    }

}

