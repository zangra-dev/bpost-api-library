<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\Option;

use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use DOMDocument;
use DOMElement;
use DOMException;
use SimpleXMLElement;

/**
 * bPost Insurance class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Insured extends Option
{
    public const INSURANCE_TYPE_BASIC_INSURANCE      = 'basicInsurance';
    public const INSURANCE_TYPE_ADDITIONAL_INSURANCE = 'additionalInsurance';

    public const INSURANCE_AMOUNT_UP_TO_2500_EUROS  = 2;
    public const INSURANCE_AMOUNT_UP_TO_5000_EUROS  = 3;
    public const INSURANCE_AMOUNT_UP_TO_7500_EUROS  = 4;
    public const INSURANCE_AMOUNT_UP_TO_10000_EUROS = 5;
    public const INSURANCE_AMOUNT_UP_TO_12500_EUROS = 6;
    public const INSURANCE_AMOUNT_UP_TO_15000_EUROS = 7;
    public const INSURANCE_AMOUNT_UP_TO_17500_EUROS = 8;
    public const INSURANCE_AMOUNT_UP_TO_20000_EUROS = 9;
    public const INSURANCE_AMOUNT_UP_TO_22500_EUROS = 10;
    public const INSURANCE_AMOUNT_UP_TO_25000_EUROS = 11;

    private string $type;
    private ?int $value = null;

    /**
     * @throws BpostInvalidValueException
     */
    public function __construct(string $type, ?int $value = null)
    {
        $this->setType($type);
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    public static function getPossibleTypeValues(): array
    {
        return [
            self::INSURANCE_TYPE_BASIC_INSURANCE,
            self::INSURANCE_TYPE_ADDITIONAL_INSURANCE,
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

    public static function getPossibleValueValues(): array
    {
        return [
            self::INSURANCE_AMOUNT_UP_TO_2500_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_5000_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_7500_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_10000_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_12500_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_15000_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_17500_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_20000_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_22500_EUROS,
            self::INSURANCE_AMOUNT_UP_TO_25000_EUROS,
        ];
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setValue(int $value): void
    {
        if (!in_array($value, self::getPossibleValueValues(), true)) {
            throw new BpostInvalidValueException('value', (string)$value, self::getPossibleValueValues());
        }
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }


    /**
     * @throws DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = 'common'): DOMElement
    {
        $insured = $document->createElement(XmlHelper::getPrefixedTagName('insured', $prefix));

        $insurance = $document->createElement(XmlHelper::getPrefixedTagName($this->getType(), $prefix));
        $insured->appendChild($insurance);

        if ($this->getValue() !== null) {
            $insurance->setAttribute('value', (string)$this->getValue());
        }

        return $insured;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): static
    {
        $insuranceDetail = $xml->children('http://schema.post.be/shm/deepintegration/v3/common');
        $type = $insuranceDetail->getName();
        $valueAttr = $insuranceDetail->attributes()->value ?? null;
        $value = $valueAttr !== null ? (int)$valueAttr : null;

        // Compat héritée : additionalInsurance avec value=1 => basicInsurance sans value
        if ($type === static::INSURANCE_TYPE_ADDITIONAL_INSURANCE && $value === 1) {
            $type = static::INSURANCE_TYPE_BASIC_INSURANCE;
            $value = null;
        }

        return new static($type, $value);
    }
}