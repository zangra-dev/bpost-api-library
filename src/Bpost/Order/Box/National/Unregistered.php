<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Common\BasicAttribute\EmailAddressCharacteristic;
use Bpost\BpostApiClient\Common\BasicAttribute\Language;
use Bpost\BpostApiClient\Common\BasicAttribute\PhoneNumber;
use Bpost\BpostApiClient\Common\ComplexAttribute;
use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

class Unregistered extends ComplexAttribute
{
    private ?Language $language = null;
    private ?PhoneNumber $mobilePhone = null;
    private ?EmailAddressCharacteristic $emailAddress = null;
    private ?ParcelLockerReducedMobilityZone $parcelLockerReducedMobilityZone = null;

    public function hasLanguage(): bool
    {
        return $this->language !== null;
    }

    public function getLanguage(): ?string
    {
        return $this->language?->getValue();
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language !== null ? new Language($language) : null;
    }

    public function hasMobilePhone(): bool
    {
        return $this->mobilePhone !== null;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone?->getValue();
    }

    public function setMobilePhone(?string $mobilePhone): void
    {
        $this->mobilePhone = $mobilePhone !== null ? new PhoneNumber($mobilePhone) : null;
    }

    public function hasEmailAddress(): bool
    {
        return $this->emailAddress !== null;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress?->getValue();
    }

    public function setEmailAddress(?string $emailAddress): void
    {
        $this->emailAddress = $emailAddress !== null ? new EmailAddressCharacteristic($emailAddress) : null;
    }

    public function hasParcelLockerReducedMobilityZone(): bool
    {
        return $this->parcelLockerReducedMobilityZone !== null;
    }

    public function getParcelLockerReducedMobilityZone(): ?ParcelLockerReducedMobilityZone
    {
        return $this->parcelLockerReducedMobilityZone;
    }

    public function setParcelLockerReducedMobilityZone(?ParcelLockerReducedMobilityZone $zone): void
    {
        $this->parcelLockerReducedMobilityZone = $zone;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        $node = $document->createElement(XmlHelper::getPrefixedTagName('unregistered', $prefix));

        if ($this->language !== null) {
            $node->appendChild($document->createElement('language', (string) $this->language->getValue()));
        }
        if ($this->mobilePhone !== null) {
            $node->appendChild($document->createElement('mobilePhone', (string) $this->mobilePhone->getValue()));
        }
        if ($this->emailAddress !== null) {
            $node->appendChild($document->createElement('emailAddress', (string) $this->emailAddress->getValue()));
        }
        if ($this->parcelLockerReducedMobilityZone !== null) {
            $node->appendChild($this->parcelLockerReducedMobilityZone->toXML($document));
        }

        return $node;
    }

    public static function createFromXml(SimpleXMLElement $xml): self
    {
        $self = new self();

        if (isset($xml->language) && (string)$xml->language !== '') {
            $self->setLanguage((string)$xml->language);
        }

        if (isset($xml->mobilePhone) && (string)$xml->mobilePhone !== '') {
            $self->setMobilePhone((string)$xml->mobilePhone);
        }

        if (isset($xml->emailAddress) && (string)$xml->emailAddress !== '') {
            $self->setEmailAddress((string)$xml->emailAddress);
        }

        if (isset($xml->parcelLockerReducedMobilityZone)) {
            $self->setParcelLockerReducedMobilityZone(
                ParcelLockerReducedMobilityZone::createFromXml($xml->parcelLockerReducedMobilityZone)
            );
        }

        return $self;
    }
}
