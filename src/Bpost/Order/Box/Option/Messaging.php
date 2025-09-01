<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\Option;

use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Messaging class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Messaging extends Option
{
    public const MESSAGING_LANGUAGE_EN = 'EN';
    public const MESSAGING_LANGUAGE_NL = 'NL';
    public const MESSAGING_LANGUAGE_FR = 'FR';
    public const MESSAGING_LANGUAGE_DE = 'DE';

    public const MESSAGING_TYPE_INFO_DISTRIBUTED  = 'infoDistributed';
    public const MESSAGING_TYPE_INFO_NEXT_DAY     = 'infoNextDay';
    public const MESSAGING_TYPE_INFO_REMINDER     = 'infoReminder';
    public const MESSAGING_TYPE_KEEP_ME_INFORMED  = 'keepMeInformed';

    private string $type;
    private string $language;
    private ?string $emailAddress = null;
    private ?string $mobilePhone  = null;

    /**
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidValueException
     */
    public function __construct(string $type, string $language, ?string $emailAddress = null, ?string $mobilePhone = null)
    {
        $this->setType($type);
        $this->setLanguage($language);

        if ($emailAddress !== null) {
            $this->setEmailAddress($emailAddress);
        }
        if ($mobilePhone !== null) {
            $this->setMobilePhone($mobilePhone);
        }
    }

    public static function getPossibleLanguageValues(): array
    {
        return [
            self::MESSAGING_LANGUAGE_EN,
            self::MESSAGING_LANGUAGE_NL,
            self::MESSAGING_LANGUAGE_FR,
            self::MESSAGING_LANGUAGE_DE,
        ];
    }

    public static function getPossibleTypeValues(): array
    {
        return [
            self::MESSAGING_TYPE_INFO_DISTRIBUTED,
            self::MESSAGING_TYPE_INFO_NEXT_DAY,
            self::MESSAGING_TYPE_INFO_REMINDER,
            self::MESSAGING_TYPE_KEEP_ME_INFORMED,
        ];
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::getPossibleTypeValues(), true)) {
            throw new BpostInvalidValueException('type', $type, self::getPossibleTypeValues());
        }
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setLanguage(string $language): void
    {
        $language = strtoupper($language);
        if (!in_array($language, self::getPossibleLanguageValues(), true)) {
            throw new BpostInvalidValueException('language', $language, self::getPossibleLanguageValues());
        }
        $this->language = $language;
    }

    public function getLanguage(): string
    {
        return $this->language;
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

    /**
     * @throws BpostInvalidLengthException
     */
    public function setMobilePhone(string $mobilePhone): void
    {
        $length = 20;
        if (mb_strlen($mobilePhone) > $length) {
            throw new BpostInvalidLengthException('mobilePhone', mb_strlen($mobilePhone), $length);
        }
        $this->mobilePhone = $mobilePhone;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = 'common'): DOMElement
    {
        $messaging = $document->createElement(XmlHelper::getPrefixedTagName($this->getType(), $prefix));
        $messaging->setAttribute('language', $this->getLanguage());

        if ($this->emailAddress !== null) {
            $messaging->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('emailAddress', $prefix), $this->emailAddress)
            );
        }
        if ($this->mobilePhone !== null) {
            $messaging->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('mobilePhone', $prefix), $this->mobilePhone)
            );
        }

        return $messaging;
    }

    /**
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): static
    {
        $messaging = new static($xml->getName(), (string)$xml->attributes()->language);

        if ((string)$xml->emailAddress !== '') {
            $messaging->setEmailAddress((string)$xml->emailAddress);
        }
        if ((string)$xml->mobilePhone !== '') {
            $messaging->setMobilePhone((string)$xml->mobilePhone);
        }

        return $messaging;
    }
}