<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Label;

use SimpleXMLElement;

/**
 * Class Barcode
 */
class Barcode
{
    public function __construct(
        private ?string $barcode = null,
        private ?string $reference = null,
    ) {}

    public function setBarcode(?string $barcode): void
    {
        $this->barcode = $barcode;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $self = new self();

        if (isset($xml->barcode) && (string)$xml->barcode !== '') {
            $self->setBarcode((string)$xml->barcode);
        }
        if (isset($xml->reference) && (string)$xml->reference !== '') {
            $self->setReference((string)$xml->reference);
        }

        return $self;
    }
}

