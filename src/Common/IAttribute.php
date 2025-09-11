<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common;

use DOMDocument;
use DOMElement;
use SimpleXMLElement;

interface IAttribute
{
    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement;

    public static function createFromXML(SimpleXMLElement $xml): static;
}
