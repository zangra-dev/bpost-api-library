<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\CustomsInfo;

use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use DOMDocument;
use DOMElement;
use DOMException;
use SimpleXMLElement;

/**
 * bPost CustomsInfo class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class CustomsInfo
{
    public const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTA       = 'RTA';
    public const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTS       = 'RTS';
    public const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_ABANDONED = 'ABANDONED';

    public const CUSTOM_INFO_SHIPMENT_TYPE_SAMPLE    = 'SAMPLE';
    public const CUSTOM_INFO_SHIPMENT_TYPE_GIFT      = 'GIFT';
    public const CUSTOM_INFO_SHIPMENT_TYPE_GOODS     = 'GOODS';
    public const CUSTOM_INFO_SHIPMENT_TYPE_DOCUMENTS = 'DOCUMENTS';
    public const CUSTOM_INFO_SHIPMENT_TYPE_OTHER     = 'OTHER';

    public const CUSTOM_INFO_CURRENCY_EUR = 'EUR';
    public const CUSTOM_INFO_CURRENCY_GBP = 'GBP';
    public const CUSTOM_INFO_CURRENCY_USD = 'USD';
    public const CUSTOM_INFO_CURRENCY_CNY = 'CNY';

    private ?int $parcelValue = null;
    private ?string $contentDescription = null;
    private ?string $shipmentType = null;
    private ?string $parcelReturnInstructions = null;
    private ?bool $privateAddress = null;

    /**
     * this is the currency used for field parcelValue.In case of shipment to non-European country,
     * this is also the currency used for all parcel contents value (field valueOfitem) in 3 letters format.
     *
     * Possible values are: EUR=Euro    GBP=Pound   Sterling    USD=US Dollar   CNY=Yuan Renminbi
     *
     */
    private ?string $currency = null;

    /**
     * Amount paid by the sender for the sending of this shipment. See contract pricing with bpost.
     * Decimal format field (3.2)
     * Minimum value : 0
     * Maximum value : 999.99
     * Currency for field amtPostagePaidByAddresse is always EUR !
     *
     */
    private ?float $amtPostagePaidByAddresse = null;


    /**
     * @throws BpostInvalidLengthException
     */
    public function setContentDescription(?string $contentDescription): void
    {
        if ($contentDescription === null) {
            $this->contentDescription = null;
            return;
        }
        $length = 50;
        if (mb_strlen($contentDescription) > $length) {
            throw new BpostInvalidLengthException('contentDescription', mb_strlen($contentDescription), $length);
        }
        $this->contentDescription = $contentDescription;
    }

    public function getContentDescription(): ?string
    {
        return $this->contentDescription;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setParcelReturnInstructions(?string $parcelReturnInstructions): void
    {
        if ($parcelReturnInstructions === null) {
            $this->parcelReturnInstructions = null;
            return;
        }

        $normalized = strtoupper($parcelReturnInstructions);
        if (!in_array($normalized, self::getPossibleParcelReturnInstructionValues(), true)) {
            throw new BpostInvalidValueException(
                'parcelReturnInstructions',
                $normalized,
                self::getPossibleParcelReturnInstructionValues()
            );
        }
        $this->parcelReturnInstructions = $normalized;
    }

    public function getParcelReturnInstructions(): ?string
    {
        return $this->parcelReturnInstructions;
    }

    public static function getPossibleParcelReturnInstructionValues(): array
    {
        return [
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTA,
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTS,
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_ABANDONED,
        ];
    }

    public function setParcelValue(?int $parcelValue): void
    {
        $this->parcelValue = $parcelValue;
    }

    public function getParcelValue(): ?int
    {
        return $this->parcelValue;
    }

    public function setPrivateAddress(?bool $privateAddress): void
    {
        $this->privateAddress = $privateAddress;
    }

    public function getPrivateAddress(): ?bool
    {
        return $this->privateAddress;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setShipmentType(?string $shipmentType): void
    {
        if ($shipmentType === null) {
            $this->shipmentType = null;
            return;
        }

        $normalized = strtoupper($shipmentType);
        if (!in_array($normalized, self::getPossibleShipmentTypeValues(), true)) {
            throw new BpostInvalidValueException('shipmentType', $normalized, self::getPossibleShipmentTypeValues());
        }
        $this->shipmentType = $normalized;
    }

    public function getShipmentType(): ?string
    {
        return $this->shipmentType;
    }


    /**
     * @return array
     */
    public static function getPossibleShipmentTypeValues(): array
    {
        return [
            self::CUSTOM_INFO_SHIPMENT_TYPE_SAMPLE,
            self::CUSTOM_INFO_SHIPMENT_TYPE_GIFT,
            self::CUSTOM_INFO_SHIPMENT_TYPE_GOODS,
            self::CUSTOM_INFO_SHIPMENT_TYPE_DOCUMENTS,
            self::CUSTOM_INFO_SHIPMENT_TYPE_OTHER,
        ];
    }

    public function getAmtPostagePaidByAddresse(): ?float
    {
        return $this->amtPostagePaidByAddresse;
    }

    public function setAmtPostagePaidByAddresse(?float $amtPostagePaidByAddresse): void
    {
        $this->amtPostagePaidByAddresse = $amtPostagePaidByAddresse;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }


    /**
     * @throws BpostInvalidValueException
     */
    public function setCurrency(?string $currency): void
    {
        if ($currency === null) {
            $this->currency = null;
            return;
        }
        if (!in_array($currency, self::getPossibleCurrencyValues(), true)) {
            throw new BpostInvalidValueException('currency', $currency, self::getPossibleCurrencyValues());
        }
        $this->currency = $currency;
    }

    public static function getPossibleCurrencyValues(): array
    {
        return [
            self::CUSTOM_INFO_CURRENCY_EUR,
            self::CUSTOM_INFO_CURRENCY_GBP,
            self::CUSTOM_INFO_CURRENCY_USD,
            self::CUSTOM_INFO_CURRENCY_CNY,
        ];
    }

    /**
     * @throws DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        $customsInfo = $document->createElement(XmlHelper::getPrefixedTagName('customsInfo', $prefix));

        $this->parcelValueToXML($document, $prefix, $customsInfo);
        $this->contentDescriptionToXML($document, $prefix, $customsInfo);
        $this->shipmentTypeToXML($document, $prefix, $customsInfo);
        $this->parcelReturnInstructionValuesToXML($document, $prefix, $customsInfo);
        $this->privateAddressToXML($document, $prefix, $customsInfo);
        $this->currencyToXML($document, $prefix, $customsInfo);
        $this->amtPostagePaidByAddresseToXML($document, $prefix, $customsInfo);

        return $customsInfo;
    }

    /**
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $customsInfo = new self();

        if (isset($xml->parcelValue) && (string)$xml->parcelValue !== '') {
            $customsInfo->setParcelValue((int)$xml->parcelValue);
        }
        if (isset($xml->contentDescription) && (string)$xml->contentDescription !== '') {
            $customsInfo->setContentDescription((string)$xml->contentDescription);
        }
        if (isset($xml->shipmentType) && (string)$xml->shipmentType !== '') {
            $customsInfo->setShipmentType((string)$xml->shipmentType);
        }
        if (isset($xml->parcelReturnInstructions) && (string)$xml->parcelReturnInstructions !== '') {
            $customsInfo->setParcelReturnInstructions((string)$xml->parcelReturnInstructions);
        }
        if (isset($xml->privateAddress) && (string)$xml->privateAddress !== '') {
            $customsInfo->setPrivateAddress(in_array((string)$xml->privateAddress, ['true', '1'], true));
        }
        if (isset($xml->currency) && (string)$xml->currency !== '') {
            $customsInfo->setCurrency((string)$xml->currency);
        }
        if (isset($xml->amtPostagePaidByAddresse) && (string)$xml->amtPostagePaidByAddresse !== '') {
            $customsInfo->setAmtPostagePaidByAddresse((float)$xml->amtPostagePaidByAddresse);
        }

        return $customsInfo;
    }

    /** @throws DOMException */
    private function parcelValueToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getParcelValue() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('parcelValue', $prefix),
                    (string)$this->getParcelValue()
                )
            );
        }
    }

    /** @throws DOMException */
    private function currencyToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getCurrency() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('currency', $prefix),
                    $this->getCurrency()
                )
            );
        }
    }

    /** @throws DOMException */
    private function amtPostagePaidByAddresseToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getAmtPostagePaidByAddresse() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('amtPostagePaidByAddresse', $prefix),
                    sprintf('%0.2f', $this->getAmtPostagePaidByAddresse())
                )
            );
        }
    }

    /** @throws DOMException */
    private function contentDescriptionToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getContentDescription() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('contentDescription', $prefix),
                    $this->getContentDescription()
                )
            );
        }
    }

    /** @throws DOMException */
    private function shipmentTypeToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getShipmentType() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('shipmentType', $prefix),
                    $this->getShipmentType()
                )
            );
        }
    }

    /** @throws DOMException */
    private function parcelReturnInstructionValuesToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getParcelReturnInstructions() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('parcelReturnInstructions', $prefix),
                    $this->getParcelReturnInstructions()
                )
            );
        }
    }

    /** @throws DOMException */
    private function privateAddressToXML(DOMDocument $document, ?string $prefix, DOMElement $customsInfo): void
    {
        if ($this->getPrivateAddress() !== null) {
            $value = $this->getPrivateAddress() ? 'true' : 'false';
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('privateAddress', $prefix),
                    $value
                )
            );
        }
    }
}
