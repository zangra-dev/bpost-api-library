<?php

namespace Bpost\BpostApiClient\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Common\ComplexAttribute;
use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

class ParcelLockerReducedMobilityZone extends ComplexAttribute
{
    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param string      $type
     *
     * @return DOMElement
     */
    public function toXml(DOMDocument $document, $prefix = null, $type = null)
    {
        $tagName = XmlHelper::getPrefixedTagName('parcelLockerReducedMobilityZone', $prefix);

        $xml = $document->createElement($tagName);

        return $xml;
    }

    /**
     * @todo Implement it, because today, nothing is specified
     *
     * @param SimpleXMLElement $xml
     *
     * @return ParcelLockerReducedMobilityZone|ComplexAttribute
     */
    public static function createFromXml(SimpleXMLElement $xml)
    {
        $self = new self();

        return $self;
    }
}
