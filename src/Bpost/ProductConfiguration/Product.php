<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\ProductConfiguration;

use SimpleXMLElement;

/**
 * Class Product
 */
class Product
{
    public const PRODUCT_NAME_BPACK_EASY_RETOUR           = 'bpack Easy Retour';
    public const PRODUCT_NAME_BPACK_24H_PRO               = 'bpack 24h Pro';
    public const PRODUCT_NAME_BPACK_24H_BUSINESS          = 'bpack 24h business';
    public const PRODUCT_NAME_BPACK_AT_BPOST              = 'bpack@bpost';
    public const PRODUCT_NAME_BPACK_CLICK_AND_COLLECT     = 'bpack Click & Collect';
    public const PRODUCT_NAME_BPACK_24_7                  = 'bpack 24/7';
    public const PRODUCT_NAME_BPACK_BUSINESS              = 'bpack Bus';
    public const PRODUCT_NAME_BPACK_PALLET                = 'bpack Pallet';
    public const PRODUCT_NAME_BPACK_WORLD_EASY_RETURN     = 'bpack World Easy Return';
    public const PRODUCT_NAME_BPACK_WORLD_EXPRESS_PRO     = 'bpack World Express Pro';
    public const PRODUCT_NAME_BPACK_WORLD_BUSINESS        = 'bpack World Business';
    public const PRODUCT_NAME_BPACK_EUROPE_BUSINESS       = 'bpack Europe Business';
    public const PRODUCT_NAME_BPACK_AT_BPOST_INTERNATIONAL= 'bpack@bpost international';

    private bool $default;
    private string $name;

    /** @var Price[] */
    private array $prices = [];
    /** @var Option[] */
    private array $options = [];

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        // <product default="true" name="bpack 24/7"> ... </product>
        $a = $xml->attributes();

        $self = new self();
        $self->setDefault(((string) $a['default']) === 'true');
        $self->setName((string) $a['name']);

        foreach ($xml->children()->price ?? [] as $priceXml) {
            $self->addPrice(Price::createFromXML($priceXml));
        }
        foreach ($xml->children()->option ?? [] as $optionXml) {
            $self->addOption(Option::createFromXML($optionXml));
        }

        return $self;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrices(): array
    {
        return $this->prices;
    }
    public function addPrice(Price $price): void
    {
        $this->prices[] = $price;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    public function isForNationalShipping(): bool
    {
        return in_array(
            $this->getName(),
            [
                self::PRODUCT_NAME_BPACK_EASY_RETOUR,
                self::PRODUCT_NAME_BPACK_24H_PRO,
                self::PRODUCT_NAME_BPACK_24H_BUSINESS,
                self::PRODUCT_NAME_BPACK_AT_BPOST,
                self::PRODUCT_NAME_BPACK_CLICK_AND_COLLECT,
                self::PRODUCT_NAME_BPACK_24_7,
            ],
            true
        );
    }
}
