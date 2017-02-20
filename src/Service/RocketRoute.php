<?php

namespace Rocket\Task\Service;

use DOMDocument;
use DOMNode;
use SoapClient;
use Exception;
use Rocket\Task\Helpers;

/**
 * RocketRoute API
 */
class RocketRoute
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param array $config Configuration options
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Search NOTAM data for an ICAO code
     * @param string $icaoCode
     * @return array Array of NOTAM locations
     * @throws Exception
     */
    public function searchNotam($icaoCode)
    {
        $client = $this->buildSoapClient($this->config['notamWsdl']);
        $request = $this->buildXmlRequest([
            'reqnotam' => [
                'usr' => $this->config['user'],
                'passwd' => md5($this->config['password']),
                'icao' => $icaoCode
            ]
        ]);

        $data = $client->getNotam($request);
        $document = new DOMDocument();
        $document->loadXML($data);

        $resultCode = $document->getElementsByTagName('RESULT')->item(0)->textContent;
        if($resultCode != 0) {
            $message = $document->getElementsByTagName('MESSAGE')->item(0)->textContent;
            throw new Exception("API error: $message");
        }

        $locations = [];
        foreach($document->getElementsByTagName('NOTAM') as $notam) {
            $description = $notam->getElementsByTagName('ItemE')->item(0)->textContent;
            $location = $notam->getElementsByTagName('ItemQ')->item(0)->textContent;
            $location = explode('/', $location);
            $location = end($location);
            if(empty(trim($location))) {
                continue;
            }

            $location = substr($location, 0, 11);

            list($lat, $lng) = Helpers::decodeCoordinate($location);
            $lat = round($lat, 3);
            $lng = round($lng, 3);

            if (isset($locations[$lat . '*' . $lng])) {
                $locations[$lat . '*' . $lng]['description'] .= "\n\n". $description;
            } else {
                $locations[$lat . '*' . $lng] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'description' => $description
                ];
            }

        }

        sort($locations);

        return $locations;
    }

    /**
     * Convert data array to XML string
     *
     * @param array $data
     * @return string
     */
    public function buildXmlRequest($data)
    {
        $document = new DOMDocument();


        $this->appendNodes($document, $document, $data);
        $xml = $document->saveXML();
        return $xml;
    }

    /**
     * Create and append child nodes from data array
     * @param DOMDocument $document
     * @param DOMNode $parent Parent Node
     * @param $data Data array
     */
    public function appendNodes($document, $parent, $data)
    {
        foreach($data as $tag => $value) {
            $node = $document->createElement(strtoupper($tag));
            if(is_array($value)) {
                $this->appendNodes($document, $node, $value);
            } else {
                $text = $document->createTextNode($value);
                $node->appendChild($text);
            }

            $parent->appendChild($node);
        }
    }

    /**
     * Build SOAP Client for specified WSDL
     * @param string $wsdl WSDL URL
     * @return SoapClient
     */
    protected function buildSoapClient($wsdl)
    {
        return new SoapClient($wsdl);
    }
}