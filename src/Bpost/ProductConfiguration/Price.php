<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\ProductConfiguration;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidWeightException;
use SimpleXMLElement;

/**
 * Class Price
 */
class Price
{
    private string $countryIso2;
    private int $priceLessThan2;
    private int $price2To5;
    private int $price5To10;
    private int $price10To20;
    private int $price20To30;

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        // <price price20To30="820" price10To20="720" price5To10="620" price2To5="520" priceLessThan2="420" countryIso2Code="BE"/>
        $a = $xml->attributes();

        $self = new self();
        $self->setCountryIso2((string) $a['countryIso2Code']);
        $self->setPriceLessThan2((int) $a['priceLessThan2']);
        $self->setPrice2To5((int) $a['price2To5']);
        $self->setPrice5To10((int) $a['price5To10']);
        $self->setPrice10To20((int) $a['price10To20']);
        $self->setPrice20To30((int) $a['price20To30']);

        return $self;
    }

    /**
     * @throws BpostInvalidWeightException
     */
    public function getPriceByWeight(int $weight): int
    {
        if ($weight <= 2000) {
            return $this->getPriceLessThan2();
        }
        if ($weight <= 5000) {
            return $this->getPrice2To5();
        }
        if ($weight <= 10000) {
            return $this->getPrice5To10();
        }
        if ($weight <= 20000) {
            return $this->getPrice10To20();
        }
        if ($weight <= 30000) {
            return $this->getPrice20To30();
        }

        throw new BpostInvalidWeightException($weight, 30);
    }

    public function getCountryIso2(): string
    {
        return $this->countryIso2;
    }
    public function setCountryIso2(string $countryIso2): void
    {
        $this->countryIso2 = $countryIso2;
    }

    public function getPriceLessThan2(): int
    {
        return $this->priceLessThan2;
    }
    public function setPriceLessThan2(int $priceLessThan2): void
    {
        $this->priceLessThan2 = $priceLessThan2;
    }

    public function getPrice2To5(): int
    {
        return $this->price2To5;
    }
    public function setPrice2To5(int $price2To5): void
    {
        $this->price2To5 = $price2To5;
    }

    public function getPrice5To10(): int
    {
        return $this->price5To10;
    }
    public function setPrice5To10(int $price5To10): void
    {
        $this->price5To10 = $price5To10;
    }

    public function getPrice10To20(): int
    {
        return $this->price10To20;
    }
    public function setPrice10To20(int $price10To20): void
    {
        $this->price10To20 = $price10To20;
    }

    public function getPrice20To30(): int
    {
        return $this->price20To30;
    }
    public function setPrice20To30(int $price20To30): void
    {
        $this->price20To30 = $price20To30;
    }
}