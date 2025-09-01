<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Customer class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Customer
{
    public const TAG_NAME = 'customer';

    private ?string $name = null;
    private ?string $company = null;
    private ?Address $address = null;
    private ?string $emailAddress = null;
    private ?string $phoneNumber = null;

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $length = 50;
        if (mb_strlen($emailAddress) > $length) {
            throw new BpostInvalidLengthException('emailAddress', mb_strlen($emailAddress), $length);
        }
        $this->emailAddress = $emailAddress;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $length = 20;
        if (mb_strlen($phoneNumber) > $length) {
            throw new BpostInvalidLengthException('phoneNumber', mb_strlen($phoneNumber), $length);
        }
        $this->phoneNumber = $phoneNumber;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        $customer = $document->createElement(XmlHelper::getPrefixedTagName(self::TAG_NAME, $prefix));

        if ($this->name !== null) {
            $customer->appendChild($document->createElement('common:name', $this->name));
        }
        if ($this->company !== null) {
            $customer->appendChild($document->createElement('common:company', $this->company));
        }
        if ($this->address !== null) {
            $customer->appendChild($this->address->toXML($document));
        }
        if ($this->emailAddress !== null) {
            $customer->appendChild($document->createElement('common:emailAddress', $this->emailAddress));
        }
        if ($this->phoneNumber !== null) {
            $customer->appendChild($document->createElement('common:phoneNumber', $this->phoneNumber));
        }

        return $customer;
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public static function createFromXMLHelper(SimpleXMLElement $xml, Customer $instance): Customer
    {
        if (isset($xml->name) && (string) $xml->name !== '') {
            $instance->setName((string) $xml->name);
        }
        if (isset($xml->company) && (string) $xml->company !== '') {
            $instance->setCompany((string) $xml->company);
        }
        if (isset($xml->address)) {
            $instance->setAddress(Address::createFromXML($xml->address));
        }
        if (isset($xml->emailAddress) && (string) $xml->emailAddress !== '') {
            $instance->setEmailAddress((string) $xml->emailAddress);
        }
        if (isset($xml->phoneNumber) && (string) $xml->phoneNumber !== '') {
            $instance->setPhoneNumber((string) $xml->phoneNumber);
        }

        return $instance;
    }
}
