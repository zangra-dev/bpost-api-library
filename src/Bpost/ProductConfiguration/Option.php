<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\ProductConfiguration;

use SimpleXMLElement;

/**
 * Class Option
 */
class Option
{
    private string $visibility;
    private int $price;
    private string $name;
    /** @var Characteristic[] */
    private array $characteristics = [];

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        // Ex: <option visibility="NOT_VISIBLE_BY_CONSUMER_OPTIONAL" price="0" name="Cash on delivery"/>
        $attr     = $xml->attributes();
        $children = $xml->children();

        $self = new self();

        // visibility (supporte l’ancienne faute "visiblity")
        if (isset($attr['visibility'])) {
            $self->setVisibility((string) $attr['visibility']);
        } elseif (isset($attr['visiblity'])) {
            $self->setVisibility((string) $attr['visiblity']);
        }

        if (isset($attr['price'])) {
            $self->setPrice((int) $attr['price']);
        }

        if (isset($attr['name'])) {
            $self->setName((string) $attr['name']);
        }

        // characteristics (supporte l’ancienne faute "chracteristic")
        if (isset($children->characteristic)) {
            foreach ($children->characteristic as $charXml) {
                $self->addCharacteristic(Characteristic::createFromXML($charXml));
            }
        } elseif (isset($children->chracteristic)) {
            foreach ($children->chracteristic as $charXml) {
                $self->addCharacteristic(Characteristic::createFromXML($charXml));
            }
        }

        return $self;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }
    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /** @return Characteristic[] */
    public function getCharacteristics(): array
    {
        return $this->characteristics;
    }
    public function addCharacteristic(Characteristic $characteristic): void
    {
        $this->characteristics[] = $characteristic;
    }
}