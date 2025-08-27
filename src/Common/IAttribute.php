<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common;

use DOMDocument;
use DOMElement;
use SimpleXMLElement;

interface IAttribute
{
    /**
     * @param DOMDocument $document
     * @param string      $prefix
     * @param string      $type
     *
     * @return DOMElement
     */
    public function toXml(DOMDocument $document, $prefix = null, $type = null);

    /**
     * @param SimpleXMLElement $xml
     *
     * @return IAttribute
     */
    public static function createFromXml(SimpleXMLElement $xml);
}
