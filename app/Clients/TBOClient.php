<?php

namespace App\Clients;

use App\Exceptions\Clients\TBO\InvalidRequestStructure;
use SoapClient;

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

    private $location = 'http://api.tbotechnology.in/hotelapi_v7/hotelservice.svc';

    private $baseAction = 'http://TekTravel/HotelBookingApi/';

    /**
     * The xml response as a string
     *
     * @var string
     */
    private $responseXML;

    /**
     * The xml encoded into array|stdClass
     *
     * @var array|\stdClass
     */
    private $responseData;


    // Action Constants
    const ACTION_HOTEL_SEARCH = 'HotelSearch';
    const ACTION_COUNTRY_LIST = 'CountryList';


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
        $xml_wsaa = $this->requestXMLDocument->createElement("wsa:Action", $this->baseAction . $action);
        $xml_wsat = $this->requestXMLDocument->createElement("wsa:To", $this->location);
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

    public function setAction($action)
    {
        $this->action = $action;
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
     * Returns the request xml as a string
     *
     * @return string
     */
    public function getRequestXML(): string
    {
        return $this->requestXMLDocument->saveXML();
    }

    /**
     * Returns the request xml as a DOMDocument Object
     *
     * @return \DOMDocument
     */
    public function getRequestDOM(): \DOMDocument
    {
        return $this->requestXMLDocument;
    }

    /**
     * Performs the request
     *
     * @return array|\stdClass
     */

    public function makeRequest()
    {
        $client = new SoapClient($this->location . '?wsdl');
        $this->responseXML = $client->__doRequest($this->requestXMLDocument->saveXML(),
            $this->location,
            $this->baseAction . $this->action, 2);

        $this->decodeResponse();
    }

    /**
     * returns the xml response as a string
     *
     * @return string
     */
    public function responseXML(): string
    {
        return $this->responseXML;
    }

    /**
     * returns the xml response as an array|stdClass
     *
     * @return array|\stdClass
     */
    public function responseData()
    {
        return $this->responseData;
    }

    /**
     * Composes the inner body details of the request given array of data
     *
     * @param array $data
     * @param \DOMElement|null $parent
     * @param string|null $elementKey
     */
    private function composeBody(array $data, \DOMElement $parent = null, string $elementKey = null)
    {
        if (null === $parent && is_numeric_array($data)) {
            throw new InvalidRequestStructure();
        }

        if (is_numeric_array($data)) {
            foreach ($data as $datum) {
                if (gettype($datum) !== 'array') {
                    $this->composeField($elementKey, $datum, $parent);
                } else {
                    if ($elementKey && is_numeric_array($datum)) {
                        throw new InvalidRequestStructure();
                    }
                    $parentElement = $this->composeField($elementKey, '', $parent);
                    $this->composeBody($datum, $parentElement);
                }
            }
        } else {
            foreach ($data as $key => $value) {
                if (gettype($value) !== 'array') {
                    $this->composeField($key, $value, $parent);
                } elseif (is_numeric_array($value)) {
                    $parentElement = $this->composeField($key, '', $parent);
                    $singularKey = substr($key, 0, strlen($key) - 1);
                    $this->composeBody($value, $parentElement, $singularKey);
                } else {
                    $parentElement = $this->composeField($key, '', $parent);
                    $this->composeBody($value, $parentElement);
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

    /**
     * Converts the response XML into a usable data structure
     *
     */
    private function decodeResponse()
    {
        $search = ['s:Envelope', 's:Header', 'a:Action', 's:Body'];
        $replace = ['Envelope', 'Header', 'Action', 'Body'];
        $realXML = str_replace($search, $replace, $this->responseXML);

        $this->responseData = simplexml_load_string($realXML);
    }
}

