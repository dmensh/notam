<?php

namespace Rocket\Task\Tests\Service;

use PHPUnit_Framework_TestCase;
use Rocket\Task\Service\RocketRoute;
use SoapClient;
use DOMDocument;

class RocketRouteTest extends PHPUnit_Framework_TestCase
{
    protected $config = [
        'notamWsdl' => 'https://wsdl/',
        'user'      => 'user@email.com',
        'password'  => 'pass'
    ];
    
    protected $notamRequest = '
        <?xml version="1.0"?>
        <REQNOTAM>
            <USR>user@email.com</USR>
            <PASSWD>1a1dc91c907325c69271ddf0c944bc72</PASSWD>
            <ICAO>UKKK</ICAO>
        </REQNOTAM>
    ';
    
    protected $notamResponse = '
        <?xml version="1.0" encoding="UTF-8"?>
        <REQNOTAM>
            <RESULT>0</RESULT>
            <NOTAMSET ICAO="UKKK">
                <NOTAM id="A2389/16">
                    <ItemQ>UKBV/QOBCE/IV/M /A /000/999/5024N03027E</ItemQ>
                    <ItemA>UKKK</ItemA>
                    <ItemB>1609261120</ItemB>
                    <ItemC>1612252359</ItemC>
                    <ItemD />
                    <ItemE>OBST ERECTED CRANE.</ItemE>
                </NOTAM>
            </NOTAMSET>
        </REQNOTAM>
    ';
    
    protected $rocketRoute;
    
    public function setUp()
    {
        $this->rocketRoute = $this->getMockBuilder(RocketRoute::class)
            ->setMethods(['buildSoapClient'])
            ->setConstructorArgs([$this->config])
            ->getMock();
    }
    
    public function testSearchCode()
    {
        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->setMethods(['getNotam'])
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->rocketRoute
            ->expects($this->once())
            ->method('buildSoapClient')
            ->with($this->config['notamWsdl'])
            ->willReturn($soapClient);
        
        $soapClient
            ->expects($this->once())
            ->method('getNotam')
            ->with($this->normalizeXml($this->notamRequest))
            ->willReturn($this->normalizeXml($this->notamResponse));
        
        $locations = $this->rocketRoute->searchNotam('UKKK');
        $this->assertEquals([
            [
                'lat' => 50.24,
                'lng' => 30.27,
                'description' => 'OBST ERECTED CRANE.'
            ]
        ], $locations);
    }
    
    protected function normalizeXml($str)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXml(trim($str));
        
        return $dom->saveXml();
    }
}