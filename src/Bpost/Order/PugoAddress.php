<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;

/**
 * bPost PugoAddress class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class PugoAddress extends Address
{
    public const TAG_NAME = 'pugoAddress';

    public function toXML(DOMDocument $document, ?string $prefix = 'common'): DOMElement
    {
        // <national:pugoAddress>
        $el = $document->createElement(
            XmlHelper::getPrefixedTagName(self::TAG_NAME, 'national')
        );

        // Enfants en "common:*"
        if ($this->getStreetName() !== null) {
            $el->appendChild($document->createElement(
                XmlHelper::getPrefixedTagName('streetName', 'common'),
                $this->getStreetName()
            ));
        }
        if ($this->getNumber() !== null) {
            $el->appendChild($document->createElement(
                XmlHelper::getPrefixedTagName('number', 'common'),
                $this->getNumber()
            ));
        }
        if ($this->getBox() !== null) {
            $el->appendChild($document->createElement(
                XmlHelper::getPrefixedTagName('box', 'common'),
                $this->getBox()
            ));
        }
        if ($this->getPostalCode() !== null) {
            $el->appendChild($document->createElement(
                XmlHelper::getPrefixedTagName('postalCode', 'common'),
                $this->getPostalCode()
            ));
        }
        if ($this->getLocality() !== null) {
            $el->appendChild($document->createElement(
                XmlHelper::getPrefixedTagName('locality', 'common'),
                $this->getLocality()
            ));
        }
        if ($this->getCountryCode() !== null) {
            $el->appendChild($document->createElement(
                XmlHelper::getPrefixedTagName('countryCode', 'common'),
                $this->getCountryCode()
            ));
        }

        return $el;
    }
}
