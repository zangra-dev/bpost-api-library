<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

class ParcelsDepotAddress extends Address
{
    public const TAG_NAME = 'parcelsDepotAddress';

    public function toXML(DOMDocument $document, ?string $prefix = 'common'): DOMElement
    {
        $commonAddress = parent::toXML($document, 'common');

        $container = $document->createElement(
            XmlHelper::getPrefixedTagName(self::TAG_NAME, 'national')
        );

        /** @var \DOMNode $child */
        foreach (iterator_to_array($commonAddress->childNodes) as $child) {
            $container->appendChild($child->cloneNode(true));
        }

        return $container;
    }
}
