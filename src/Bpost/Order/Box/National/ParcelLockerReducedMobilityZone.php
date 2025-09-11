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
    public function toXml(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $tagName = XmlHelper::getPrefixedTagName('parcelLockerReducedMobilityZone', $prefix);
        return $document->createElement($tagName);
    }

    /**
     * @todo Implement it, because today, nothing is specified
     * @return ParcelLockerReducedMobilityZone|ComplexAttribute
     */
    public static function createFromXml(SimpleXMLElement $xml): self
    {
        return new self();
    }
}
