<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\Option\Option;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * Interface IBox
 */
interface IBox
{
    public function setOptions(array $options): void;

    public function getOptions(): array;

    public function addOption(Option $option): void;

    public function setProduct(string $product): void;

    public function getProduct(): ?string;

    public static function getPossibleProductValues(): array;

    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement;

    public static function createFromXML(SimpleXMLElement $xml): self;
}