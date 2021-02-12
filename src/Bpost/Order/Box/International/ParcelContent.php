<?php

namespace Bpost\BpostApiClient\Bpost\Order\Box\International;

/**
 * bPost ParcelContent for international shipment.
 */
class ParcelContent
{
    /**
     * Number of itemsof each type for the specified parcel content.
     *
     * @var int
     */
    private $numberOfItemType;

    /**
     * Value for the number of items and NOT per item
     * Max length = 50
     * Integer format in cents, for example for 10€, you must sent 1000, NO decima.
     *
     * @var string
     */
    private $valueOfItem;

    /**
     * description of parcel content
     * Max length = 30 characters.
     *
     * @var string
     */
    private $itemDescription;

    /**
     * Weight for the number of itemsof each typeand NOT per item.
     * Integer format, NO decimal ! In gramme (gr).
     * Range 1-30000.
     *
     * @var int
     */
    private $nettoWeight;

    /**
     * HS stands for Harmonized System.
     * It’s a multipurpose international product nomenclature that describes the type of good that is shipped.
     * Today, customs officers must use HS code to clear every commodity that enters or crosses any international borders.
     * Integer format, maximum 9 digits
     * you can find the code on https://www.tariffnumber.com/.
     *
     * @var string
     */
    private $hsTariffCode;

    /**
     * 2 letters country code from the orign of goods
     * you can find the code on https://countrycode.org/.
     *
     * @var string
     */
    private $originOfGoods;

    /**
     * @return int
     */
    public function getNumberOfItemType()
    {
        return $this->numberOfItemType;
    }

    /**
     * @param int $numberOfItemType
     *
     * @return self
     */
    public function setNumberOfItemType($numberOfItemType)
    {
        $this->numberOfItemType = $numberOfItemType;

        return $this;
    }

    /**
     * @return string
     */
    public function getValueOfItem()
    {
        return $this->valueOfItem;
    }

    /**
     * @param string $valueOfItem
     *
     * @return self
     */
    public function setValueOfItem($valueOfItem)
    {
        $this->valueOfItem = $valueOfItem;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * @param string $itemDescription
     *
     * @return self
     */
    public function setItemDescription($itemDescription)
    {
        if (strlen($itemDescription) > 30) {
            $itemDescription = substr($itemDescription, 0, 30);
        }

        $this->itemDescription = $itemDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getNettoWeight()
    {
        return $this->nettoWeight;
    }

    /**
     * @param int $nettoWeight
     *
     * @return self
     */
    public function setNettoWeight($nettoWeight)
    {
        $this->nettoWeight = $nettoWeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getHsTariffCode()
    {
        return $this->hsTariffCode;
    }

    /**
     * @param string $hsTariffCode
     *
     * @return self
     */
    public function setHsTariffCode($hsTariffCode)
    {
        $this->hsTariffCode = $hsTariffCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginOfGoods()
    {
        return $this->originOfGoods;
    }

    /**
     * @param string $originOfGoods
     *
     * @return self
     */
    public function setOriginOfGoods($originOfGoods)
    {
        $this->originOfGoods = $originOfGoods;

        return $this;
    }

    public function toXML(\DOMDocument $document, $prefix = null)
    {
        $tagName = 'parcelContent';
        if (null !== $prefix) {
            $tagName = $prefix.':'.$tagName;
        }

        $parcelContent = $document->createElement($tagName);

        $parcelContent->appendChild(
            $document->createElement('international:numberOfItemType', $this->getNumberOfItemType())
        );
        $parcelContent->appendChild(
            $document->createElement('international:valueOfItem', $this->getValueOfItem())
        );
        $parcelContent->appendChild(
            $document->createElement('international:itemDescription', $this->getItemDescription())
        );
        $parcelContent->appendChild(
            $document->createElement('international:nettoWeight', $this->getNettoWeight())
        );
        $parcelContent->appendChild(
            $document->createElement('international:hsTariffCode', $this->getHsTariffCode())
        );
        $parcelContent->appendChild(
            $document->createElement('international:originOfGoods', $this->getOriginOfGoods())
        );

        return $parcelContent;
    }
}
