<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Common\ComplexAttribute;
use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

class ParcelLockerReducedMobilityZone extends ComplexAttribute
{
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        return $document->createElement(XmlHelper::getPrefixedTagName('reducedMobilityZone', $prefix));
    }


    public static function createFromXML(SimpleXMLElement $xml): self
    {
        return new self();
    }
}
