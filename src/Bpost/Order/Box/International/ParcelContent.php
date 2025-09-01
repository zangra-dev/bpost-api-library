<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\International;

use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use DOMException;
use SimpleXMLElement;

/**
 * bpost ParcelContent for international shipment.
 */
class ParcelContent
{
    /**
     * Number of items of each type for the specified parcel content.
     */
    private ?int $numberOfItemType = null;

    /**
     * Value for the number of items and NOT per item
     * Max length = 50
     * Integer format in cents, for example for 10€, you must sent 1000, NO decimal.
     */
    private ?int $valueOfItem = null;

    /**
     * description of parcel content
     * Max length = 30 characters.
     */
    private ?string $itemDescription = null;

    /**
    /**
     * Weight for the number of items of each type and NOT per item.
     * Integer format, NO decimal ! In gramme (gr).
     * Range 1-30000.
     */
    private ?int $nettoWeight = null;

    /**
     * HS stands for Harmonized System.
     * It’s a multipurpose international product nomenclature that describes the type of good that is shipped.
     * Today, customs officers must use HS code to clear every commodity that enters or crosses any international borders.
     * Integer format, maximum 9 digits
     * you can find the code on https://www.tariffnumber.com/.
     */
    private ?string $hsTariffCode = null;

    /**
     * 2 letters country code from the orign of goods
     * you can find the code on https://countrycode.org/.
     */
    private ?string $originOfGoods = null;

    public function getNumberOfItemType(): ?int
    {
        return $this->numberOfItemType;
    }

    public function setNumberOfItemType(?int $numberOfItemType): void
    {
        $this->numberOfItemType = $numberOfItemType;
    }

    public function getValueOfItem(): ?int
    {
        return $this->valueOfItem;
    }

    public function setValueOfItem(?int $valueOfItem): void
    {
        $this->valueOfItem = $valueOfItem;
    }

    public function getItemDescription(): ?string
    {
        return $this->itemDescription;
    }

    public function setItemDescription(?string $itemDescription): void
    {
        if (strlen($itemDescription) > 30) {
            $itemDescription = substr($itemDescription, 0, 30);
        }

        $this->itemDescription = $itemDescription;
    }

    public function getNettoWeight(): ?int
    {
        return $this->nettoWeight;
    }

    public function setNettoWeight(?int $nettoWeight): void
    {
        $this->nettoWeight = $nettoWeight;
    }

    public function getHsTariffCode(): ?string
    {
        return $this->hsTariffCode;
    }

    public function setHsTariffCode(?string $hsTariffCode): void
    {
        $this->hsTariffCode = $hsTariffCode;
    }

    public function getOriginOfGoods(): ?string
    {
        return $this->originOfGoods;
    }

    public function setOriginOfGoods(?string $originOfGoods): void
    {
        $this->originOfGoods = $originOfGoods;
    }

    /**
     * @throws DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        $parcelContent = $document->createElement(XmlHelper::getPrefixedTagName('parcelContent', $prefix));

        $parcelContent->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('numberOfItemType', $prefix),
                (string) $this->getNumberOfItemType()
            )
        );
        $parcelContent->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('valueOfItem', $prefix),
                (string) $this->getValueOfItem()
            )
        );
        $parcelContent->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('itemDescription', $prefix),
                (string) $this->getItemDescription()
            )
        );
        $parcelContent->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('nettoWeight', $prefix),
                (string) $this->getNettoWeight()
            )
        );
        $parcelContent->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('hsTariffCode', $prefix),
                (string) $this->getHsTariffCode()
            )
        );
        $parcelContent->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('originOfGoods', $prefix),
                (string) $this->getOriginOfGoods()
            )
        );

        return $parcelContent;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $parcelContent = new self();

        if (isset($xml->numberOfItemType) && (string)$xml->numberOfItemType !== '') {
            $parcelContent->setNumberOfItemType((int)$xml->numberOfItemType);
        }
        if (isset($xml->valueOfItem) && (string)$xml->valueOfItem !== '') {
            // XML porte des cents → int
            $parcelContent->setValueOfItem((int)$xml->valueOfItem);
        }
        if (isset($xml->itemDescription) && (string)$xml->itemDescription !== '') {
            $parcelContent->setItemDescription((string)$xml->itemDescription);
        }
        if (isset($xml->nettoWeight) && (string)$xml->nettoWeight !== '') {
            $parcelContent->setNettoWeight((int)$xml->nettoWeight);
        }
        if (isset($xml->hsTariffCode) && (string)$xml->hsTariffCode !== '') {
            // FIX: garder en string (pas (int))
            $parcelContent->setHsTariffCode((string)$xml->hsTariffCode);
        }
        if (isset($xml->originOfGoods) && (string)$xml->originOfGoods !== '') {
            $parcelContent->setOriginOfGoods((string)$xml->originOfGoods);
        }

        return $parcelContent;
    }
}