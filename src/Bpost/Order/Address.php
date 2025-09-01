<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Address class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Address
{
    public const TAG_NAME = 'common:address';

    private ?string $streetName   = null;
    private ?string $number       = null;
    private ?string $box          = null;
    private ?string $postalCode   = null;
    private ?string $locality     = null;
    private ?string $countryCode  = 'BE';

    /**
     * @throws BpostInvalidLengthException
     */
    public function __construct(
        ?string $streetName = null,
        ?string $number = null,
        ?string $box = null,
        ?string $postalCode = null,
        ?string $locality = null,
        ?string $countryCode = null
    ) {
        if ($streetName   !== null) $this->setStreetName($streetName);
        if ($number       !== null) $this->setNumber($number);
        if ($box          !== null) $this->setBox($box);
        if ($postalCode   !== null) $this->setPostalCode($postalCode);
        if ($locality     !== null) $this->setLocality($locality);
        if ($countryCode  !== null) $this->setCountryCode($countryCode);
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setBox(string $box): void
    {
        $max = 8;
        if (mb_strlen($box) > $max) {
            throw new BpostInvalidLengthException('box', mb_strlen($box), $max);
        }
        $this->box = $box;
    }
    public function getBox(): ?string
    {
        return $this->box;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setCountryCode(string $countryCode): void
    {
        $max = 2;
        if (mb_strlen($countryCode) > $max) {
            throw new BpostInvalidLengthException('countryCode', mb_strlen($countryCode), $max);
        }
        $this->countryCode = strtoupper($countryCode);
    }
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setLocality(string $locality): void
    {
        $max = 40;
        if (mb_strlen($locality) > $max) {
            throw new BpostInvalidLengthException('locality', mb_strlen($locality), $max);
        }
        $this->locality = $locality;
    }
    public function getLocality(): ?string
    {
        return $this->locality;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setNumber(string $number): void
    {
        $max = 8;
        if (mb_strlen($number) > $max) {
            throw new BpostInvalidLengthException('number', mb_strlen($number), $max);
        }
        $this->number = $number;
    }
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setPostalCode(string $postalCode): void
    {
        $max = 40;
        if (mb_strlen($postalCode) > $max) {
            throw new BpostInvalidLengthException('postalCode', mb_strlen($postalCode), $max);
        }
        $this->postalCode = $postalCode;
    }
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setStreetName(string $streetName): void
    {
        $max = 40;
        if (mb_strlen($streetName) > $max) {
            throw new BpostInvalidLengthException('streetName', mb_strlen($streetName), $max);
        }
        $this->streetName = $streetName;
    }
    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, string $prefix = 'common'): DOMElement
    {
        $address = $document->createElement(self::TAG_NAME);

        $this->streetToXML($document, $prefix, $address);
        $this->streetNumbersToXML($document, $prefix, $address);
        $this->localityToXML($document, $prefix, $address);
        $this->countryToXML($document, $prefix, $address);

        return $address;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public static function createFromXML(SimpleXMLElement $xml): Address
    {
        $address = new static();

        if (isset($xml->streetName) && $xml->streetName != '') {
            $address->setStreetName((string) $xml->streetName);
        }
        if (isset($xml->number) && $xml->number != '') {
            $address->setNumber((string) $xml->number);
        }
        if (isset($xml->box) && $xml->box != '') {
            $address->setBox((string) $xml->box);
        }
        if (isset($xml->postalCode) && $xml->postalCode != '') {
            $address->setPostalCode((string) $xml->postalCode);
        }
        if (isset($xml->locality) && $xml->locality != '') {
            $address->setLocality((string) $xml->locality);
        }
        if (isset($xml->countryCode) && $xml->countryCode != '') {
            $address->setCountryCode((string) $xml->countryCode);
        }

        return $address;
    }

    /**
     * @throws \DOMException
     */
    private function streetToXML(DOMDocument $document, string $prefix, DOMElement $address): void
    {
        if ($this->streetName !== null) {
            $address->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('streetName', $prefix), $this->streetName)
            );
        }
    }

    /**
     * @throws \DOMException
     */
    private function localityToXML(DOMDocument $document, string $prefix, DOMElement $address): void
    {
        if ($this->postalCode !== null) {
            $address->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('postalCode', $prefix), $this->postalCode)
            );
        }
        if ($this->locality !== null) {
            $address->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('locality', $prefix), $this->locality)
            );
        }
    }

    /**
     * @throws \DOMException
     */
    private function countryToXML(DOMDocument $document, string $prefix, DOMElement $address): void
    {
        if ($this->countryCode !== null) {
            $address->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('countryCode', $prefix), $this->countryCode)
            );
        }
    }

    /**
     * @throws \DOMException
     */
    private function streetNumbersToXML(DOMDocument $document, string $prefix, DOMElement $address): void
    {
        if ($this->number !== null) {
            $address->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('number', $prefix), $this->number)
            );
        }
        if ($this->box !== null) {
            $address->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('box', $prefix), $this->box)
            );
        }
    }
}