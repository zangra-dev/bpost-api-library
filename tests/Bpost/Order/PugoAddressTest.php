<?php

namespace Tests\Bpost\Order;

use Bpost\BpostApiClient\Bpost\Order\PugoAddress;
use DOMDocument;
use PHPUnit_Framework_TestCase;

class PugoAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Create a generic DOM Document
     *
     * @return DOMDocument
     */
    private static function createDomDocument()
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        return $document;
    }

    /**
     * Tests PugoAddress->toXML
     */
    public function testToXML()
    {
        $data = array(
            'streetName' => 'Afrikalaan',
            'number' => '2890',
            'box' => '3',
            'postalCode' => '9000',
            'locality' => 'Gent',
            'countryCode' => 'BE',
        );

        $expectedDocument = self::createDomDocument();
        $address = $expectedDocument->createElement('pugoAddress');
        foreach ($data as $key => $value) {
            $address->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($address);

        $actualDocument = self::createDomDocument();
        $address = new PugoAddress(
            $data['streetName'],
            $data['number'],
            $data['box'],
            $data['postalCode'],
            $data['locality'],
            $data['countryCode']
        );
        $actualDocument->appendChild(
            $address->toXML($actualDocument, null)
        );

        $this->assertEquals($expectedDocument, $actualDocument);
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyBoxProperties()
    {
        $address = new PugoAddress();
        $address->setBox(str_repeat('a', 9));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyCountryCodeProperties()
    {
        $address = new PugoAddress();
        $address->setCountryCode(str_repeat('a', 3));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyLocalityProperties()
    {
        $address = new PugoAddress();
        $address->setLocality(str_repeat('a', 41));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyNumberProperties()
    {
        $address = new PugoAddress();
        $address->setNumber(str_repeat('a', 9));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyPostalCodeProperties()
    {
        $address = new PugoAddress();
        $address->setPostalCode(str_repeat('a', 41));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyStreetNameProperties()
    {
        $address = new PugoAddress();
        $address->setStreetName(str_repeat('a', 41));
    }
}
