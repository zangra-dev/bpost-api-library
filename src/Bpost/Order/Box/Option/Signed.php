<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\Option;

use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Signature class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Signed extends Option
{
    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = 'common'): DOMElement
    {
        return $document->createElement(XmlHelper::getPrefixedTagName('signed', $prefix));
    }

    public static function createFromXML(SimpleXMLElement $xml): static
    {
        return new static();
    }
}
