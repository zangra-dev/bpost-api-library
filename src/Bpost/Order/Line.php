<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Line class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Line
{
    private ?string $text = null;
    private ?int $numberOfItems = null;

    public function __construct(?string $text = null, ?int $numberOfItems = null)
    {
        if ($text !== null) {
            $this->setText($text);
        }
        if ($numberOfItems !== null) {
            $this->setNumberOfItems($numberOfItems);
        }
    }

    public function setNumberOfItems(int $nbOfItems): void
    {
        $this->numberOfItems = $nbOfItems;
    }

    public function getNumberOfItems(): ?int
    {
        return $this->numberOfItems;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        $line = $document->createElement(XmlHelper::getPrefixedTagName('orderLine', $prefix));

        if ($this->text !== null) {
            $line->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('text', $prefix), $this->text)
            );
        }
        if ($this->numberOfItems !== null) {
            $line->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('nbOfItems', $prefix), (string) $this->numberOfItems)
            );
        }

        return $line;
    }

    public static function createFromXML(SimpleXMLElement $xml): Line
    {
        $line = new Line();

        if (isset($xml->text) && (string) $xml->text !== '') {
            $line->setText((string) $xml->text);
        }
        if (isset($xml->nbOfItems) && (string) $xml->nbOfItems !== '') {
            $line->setNumberOfItems((int) $xml->nbOfItems);
        }

        return $line;
    }
}