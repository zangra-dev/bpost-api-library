<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\ProductConfiguration;

use SimpleXMLElement;

/**
 * Class DeliveryMethod
 */
class DeliveryMethod
{
    public const DELIVERY_METHOD_NAME_HOME_OR_OFFICE  = 'home or office';
    public const DELIVERY_METHOD_NAME_PICKUP_POINT    = 'pick-up point';
    public const DELIVERY_METHOD_NAME_PARCEL_LOCKER   = 'parcel locker';
    public const DELIVERY_METHOD_NAME_CLICK_AND_COLLECT = 'Click & Collect';

    private string $name;
    private string $visibility;
    /** @var Product[] */
    private array $products = [];

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $attributes = $xml->attributes();
        $children   = $xml->children();

        $instance = new self();
        if (isset($attributes['name'])) {
            $instance->setName((string) $attributes['name']);
        }
        // Correction de l'attribut: visibility (et fallback si l’API renvoie l’ancienne coquille)
        if (isset($attributes['visibility'])) {
            $instance->setVisibility((string) $attributes['visibility']);
        } elseif (isset($attributes['visiblity'])) {
            $instance->setVisibility((string) $attributes['visiblity']);
        }

        if (isset($children->product)) {
            foreach ($children->product as $productXml) {
                $instance->addProduct(Product::createFromXML($productXml));
            }
        }

        return $instance;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function isVisibleAndActive(): bool
    {
        return $this->getVisibility() === Visibility::DELIVERY_METHOD_VISIBILITY_VISIBLE;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }
}