<?php

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
    const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTA = 'RTA';
    const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTS = 'RTS';
    const CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_ABANDONED = 'ABANDONED';

    const CUSTOM_INFO_SHIPMENT_TYPE_SAMPLE = 'SAMPLE';
    const CUSTOM_INFO_SHIPMENT_TYPE_GIFT = 'GIFT';
    const CUSTOM_INFO_SHIPMENT_TYPE_GOODS = 'GOODS';
    const CUSTOM_INFO_SHIPMENT_TYPE_DOCUMENTS = 'DOCUMENTS';
    const CUSTOM_INFO_SHIPMENT_TYPE_OTHER = 'OTHER';

    const CUSTOM_INFO_CURRENCY_EUR = 'EUR';
    const CUSTOM_INFO_CURRENCY_GBP = 'GBP';
    const CUSTOM_INFO_CURRENCY_USD = 'USD';
    const CUSTOM_INFO_CURRENCY_CNY = 'CNY';

    /**
     * @var int
     */
    private $parcelValue;

    /**
     * @var string
     */
    private $contentDescription;

    /**
     * @var string
     */
    private $shipmentType;

    /**
     * @var string
     */
    private $parcelReturnInstructions;

    /**
     * @var bool
     */
    private $privateAddress;

    /**
     * this is the currency used for field parcelValue.In case of shipment to non-European country,
     * this is also the currency used for all parcel contents value (field valueOfitem) in 3 letters format.
     *
     * Possible values are: EUR=Euro    GBP=Pound   Sterling    USD=US Dollar   CNY=Yuan Renminbi
     *
     * @var string
     */
    private $currency;

    /**
     * Amount paid by the sender for the sending of this shipment. See contract pricing with bpost.
     * Decimal format field (3.2)
     * Minimum value : 0
     * Maximum value : 999.99
     * Currency for field amtPostagePaidByAddresse is always EUR !
     *
     * @var float
     */
    private $amtPostagePaidByAddresse;

    /**
     * @param string $contentDescription
     *
     * @throws BpostInvalidLengthException
     */
    public function setContentDescription($contentDescription)
    {
        $length = 50;
        if (mb_strlen($contentDescription) > $length) {
            throw new BpostInvalidLengthException('contentDescription', mb_strlen($contentDescription), $length);
        }

        $this->contentDescription = $contentDescription;
    }

    /**
     * @return string
     */
    public function getContentDescription()
    {
        return $this->contentDescription;
    }

    /**
     * @param string $parcelReturnInstructions
     *
     * @throws BpostInvalidValueException
     */
    public function setParcelReturnInstructions($parcelReturnInstructions)
    {
        $parcelReturnInstructions = strtoupper($parcelReturnInstructions);

        if (!in_array($parcelReturnInstructions, self::getPossibleParcelReturnInstructionValues())) {
            throw new BpostInvalidValueException(
                'parcelReturnInstructions',
                $parcelReturnInstructions,
                self::getPossibleParcelReturnInstructionValues()
            );
        }

        $this->parcelReturnInstructions = $parcelReturnInstructions;
    }

    /**
     * @return string
     */
    public function getParcelReturnInstructions()
    {
        return $this->parcelReturnInstructions;
    }

    /**
     * @return array
     */
    public static function getPossibleParcelReturnInstructionValues()
    {
        return array(
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTA,
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_RTS,
            self::CUSTOM_INFO_PARCEL_RETURN_INSTRUCTION_ABANDONED,
        );
    }

    /**
     * @param int $parcelValue
     */
    public function setParcelValue($parcelValue)
    {
        $this->parcelValue = $parcelValue;
    }

    /**
     * @return int
     */
    public function getParcelValue()
    {
        return $this->parcelValue;
    }

    /**
     * @param bool $privateAddress
     */
    public function setPrivateAddress($privateAddress)
    {
        $this->privateAddress = $privateAddress;
    }

    /**
     * @return bool
     */
    public function getPrivateAddress()
    {
        return $this->privateAddress;
    }

    /**
     * @param string $shipmentType
     *
     * @throws BpostInvalidValueException
     */
    public function setShipmentType($shipmentType)
    {
        $shipmentType = strtoupper($shipmentType);

        if (!in_array($shipmentType, self::getPossibleShipmentTypeValues())) {
            throw new BpostInvalidValueException('shipmentType', $shipmentType, self::getPossibleShipmentTypeValues());
        }

        $this->shipmentType = $shipmentType;
    }

    /**
     * @return string
     */
    public function getShipmentType()
    {
        return $this->shipmentType;
    }

    /**
     * @return array
     */
    public static function getPossibleShipmentTypeValues()
    {
        return array(
            self::CUSTOM_INFO_SHIPMENT_TYPE_SAMPLE,
            self::CUSTOM_INFO_SHIPMENT_TYPE_GIFT,
            self::CUSTOM_INFO_SHIPMENT_TYPE_GOODS,
            self::CUSTOM_INFO_SHIPMENT_TYPE_DOCUMENTS,
            self::CUSTOM_INFO_SHIPMENT_TYPE_OTHER,
        );
    }

    /**
     * @return float
     */
    public function getAmtPostagePaidByAddresse()
    {
        return $this->amtPostagePaidByAddresse;
    }

    /**
     * @param float $amtPostagePaidByAddresse
     */
    public function setAmtPostagePaidByAddresse($amtPostagePaidByAddresse)
    {
        $this->amtPostagePaidByAddresse = $amtPostagePaidByAddresse;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @throws BpostInvalidValueException
     */
    public function setCurrency($currency)
    {
        if (!in_array($currency, self::getPossibleCurrencyValues())) {
            throw new BpostInvalidValueException('currency', $currency, self::getPossibleCurrencyValues());
        }
        $this->currency = $currency;
    }

    public static function getPossibleCurrencyValues()
    {
        return array(
            self::CUSTOM_INFO_CURRENCY_EUR,
            self::CUSTOM_INFO_CURRENCY_GBP,
            self::CUSTOM_INFO_CURRENCY_USD,
            self::CUSTOM_INFO_CURRENCY_CNY,
        );
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param DomDocument $document
     * @param string      $prefix
     *
     * @return DomElement
     *
     * @throws DOMException
     */
    public function toXML(DOMDocument $document, $prefix = null)
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
     * @param SimpleXMLElement $xml
     *
     * @return CustomsInfo
     *
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        $customsInfo = new CustomsInfo();

        if (isset($xml->parcelValue) && $xml->parcelValue != '') {
            $customsInfo->setParcelValue(
                (int) $xml->parcelValue
            );
        }
        if (isset($xml->contentDescription) && $xml->contentDescription != '') {
            $customsInfo->setContentDescription(
                (string) $xml->contentDescription
            );
        }
        if (isset($xml->shipmentType) && $xml->shipmentType != '') {
            $customsInfo->setShipmentType(
                (string) $xml->shipmentType
            );
        }
        if (isset($xml->parcelReturnInstructions) && $xml->parcelReturnInstructions != '') {
            $customsInfo->setParcelReturnInstructions(
                (string) $xml->parcelReturnInstructions
            );
        }
        if (isset($xml->privateAddress) && $xml->privateAddress != '') {
            $customsInfo->setPrivateAddress(
                (string) $xml->privateAddress == 'true'
            );
        }
        if (isset($xml->currency) && $xml->currency != '') {
            $customsInfo->setCurrency(
                (string) $xml->currency
            );
        }
        if (isset($xml->amtPostagePaidByAddresse) && $xml->amtPostagePaidByAddresse != '') {
            $customsInfo->setAmtPostagePaidByAddresse(
                (float) $xml->amtPostagePaidByAddresse
            );
        }

        return $customsInfo;
    }

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function parcelValueToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
    {
        if ($this->getParcelValue() !== null) {
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('parcelValue', $prefix),
                    $this->getParcelValue()
                )
            );
        }
    }

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function currencyToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
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

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function amtPostagePaidByAddresseToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
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

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function contentDescriptionToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
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

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function shipmentTypeToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
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

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function parcelReturnInstructionValuesToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
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

    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param DOMElement  $customsInfo
     *
     * @throws DOMException
     */
    private function privateAddressToXML(DOMDocument $document, $prefix, DOMElement $customsInfo)
    {
        if ($this->getPrivateAddress() !== null) {
            if ($this->getPrivateAddress()) {
                $value = 'true';
            } else {
                $value = 'false';
            }
            $customsInfo->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('privateAddress', $prefix),
                    $value
                )
            );
        }
    }
}
