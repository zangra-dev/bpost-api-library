<?php

namespace Tests\Bpost\Order;

use Bpost\BpostApiClient\Bpost\Order\Address;
use DOMDocument;
use PHPUnit_Framework_TestCase;

class AddressTest extends PHPUnit_Framework_TestCase
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
     * Tests Address->toXML
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
        $address = $expectedDocument->createElement('common:address');
        foreach ($data as $key => $value) {
            $address->appendChild(
                $expectedDocument->createElement($key, $value)
            );
        }
        $expectedDocument->appendChild($address);

        $actualDocument = self::createDomDocument();
        $address = new Address(
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

        $this->assertSame($expectedDocument->saveXML(), $actualDocument->saveXML());
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyBoxProperties()
    {
        $address = new Address();
        $address->setBox(str_repeat('a', 9));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyCountryCodeProperties()
    {
        $address = new Address();
        $address->setCountryCode(str_repeat('a', 3));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyLocalityProperties()
    {
        $address = new Address();
        $address->setLocality(str_repeat('a', 41));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyNumberProperties()
    {
        $address = new Address();
        $address->setNumber(str_repeat('a', 9));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyPostalCodeProperties()
    {
        $address = new Address();
        $address->setPostalCode(str_repeat('a', 41));
    }

    /**
     * @expectedException \Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException
     */
    public function testFaultyStreetNameProperties()
    {
        $address = new Address();
        $address->setStreetName(str_repeat('a', 41));
    }

    /**
     * Tests Address->createFromXML
     */
    public function testCreateFromXML()
    {
        $data = array(
            'streetName' => 'Afrikalaan',
            'number' => '289',
            'box' => '3',
            'postalCode' => '9000',
            'locality' => 'Gent',
            'countryCode' => 'BE',
        );

        $document = self::createDomDocument();
        $addressElement = $document->createElement('address');
        foreach ($data as $key => $value) {
            $addressElement->appendChild(
                $document->createElement($key, $value)
            );
        }
        $document->appendChild($addressElement);

        $address = Address::createFromXML(
            simplexml_load_string(
                $document->saveXML()
            )
        );

        $this->assertSame($data['streetName'], $address->getStreetName());
        $this->assertSame($data['number'], $address->getNumber());
        $this->assertSame($data['box'], $address->getBox());
        $this->assertSame($data['postalCode'], $address->getPostalCode());
        $this->assertSame($data['locality'], $address->getLocality());
        $this->assertSame($data['countryCode'], $address->getCountryCode());
    }
}
