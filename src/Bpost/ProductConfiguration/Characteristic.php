<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\ProductConfiguration;

use SimpleXMLElement;

/**
 * Class Characteristic
 */
class Characteristic
{
    private string $displayValue;
    private int $value;
    private string $name;

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        /*
         * Exemple :
         * <characteristic displayValue="Basic (0-500 EUR)" value="1" name="Insurance range code"/>
         */
        $attributes = $xml->attributes();

        $instance = new self();
        $instance->setDisplayValue((string) $attributes['displayValue']);
        $instance->setValue((int) $attributes['value']);
        $instance->setName((string) $attributes['name']);

        return $instance;
    }

    public function getDisplayValue(): string
    {
        return $this->displayValue;
    }

    public function setDisplayValue(string $displayValue): void
    {
        $this->displayValue = $displayValue;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}