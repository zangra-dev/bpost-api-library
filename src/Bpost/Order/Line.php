<?php

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Common\XmlHelper;
use DomDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Line class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Line
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $numberOfItems;

    /**
     * @param int $nbOfItems
     */
    public function setNumberOfItems($nbOfItems)
    {
        $this->numberOfItems = $nbOfItems;
    }

    /**
     * @return int
     */
    public function getNumberOfItems()
    {
        return $this->numberOfItems;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @param int    $numberOfItems
     */
    public function __construct($text = null, $numberOfItems = null)
    {
        if ($text != null) {
            $this->setText($text);
        }
        if ($numberOfItems != null) {
            $this->setNumberOfItems($numberOfItems);
        }
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param DomDocument $document
     * @param string      $prefix
     *
     * @return DOMElement
     */
    public function toXML(DOMDocument $document, $prefix = null)
    {
        $line = $document->createElement(XmlHelper::getPrefixedTagName('orderLine', $prefix));

        if ($this->getText() !== null) {
            $line->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('text', $prefix),
                    $this->getText()
                )
            );
        }
        if ($this->getNumberOfItems() !== null) {
            $line->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('nbOfItems', $prefix),
                    $this->getNumberOfItems()
                )
            );
        }

        return $line;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Line
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        $line = new Line();
        if (isset($xml->text) && $xml->text !== '') {
            $line->setText((string) $xml->text);
        }
        if (isset($xml->nbOfItems) && $xml->nbOfItems !== '') {
            $line->setNumberOfItems((int) $xml->nbOfItems);
        }

        return $line;
    }
}
