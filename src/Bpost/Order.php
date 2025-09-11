<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost;

use Bpost\BpostApiClient\Bpost\Order\Box;
use Bpost\BpostApiClient\Bpost\Order\Line;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoReferenceFoundException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Order class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Order
{
    /**
     * Order reference: unique ID used in your web shop to assign to an order.
     * If the value already exists, it will update current order info.
     * Existing boxes will not be changed, new boxes will be added.
     */
    private string $reference;

    /** This information is used on your invoice and allows you to attribute different cost centers */
    private ?string $costCenter = null;

    private array $lines = [];

    private array $boxes = [];

    public function __construct(string $reference)
    {
        $this->setReference($reference);
    }

    public function setBoxes(array $boxes): void
    {
        $this->boxes = $boxes;
    }

    public function getBoxes(): array
    {
        return $this->boxes;
    }

    public function addBox(Box $box): void
    {
        $this->boxes[] = $box;
    }

    public function setCostCenter(?string $costCenter): void
    {
        $this->costCenter = $costCenter;
    }

    public function getCostCenter(): ?string
    {
        return $this->costCenter;
    }

    public function setLines(array $lines): void
    {
        $this->lines = $lines;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function addLine(Line $line): void
    {
        $this->lines[] = $line;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, string $accountId): DOMElement
    {
        $order = $document->createElement('tns:order');
        $order->setAttribute('xmlns:common', 'http://schema.post.be/shm/deepintegration/v5/common');
        $order->setAttribute('xmlns:tns', 'http://schema.post.be/shm/deepintegration/v5/');
        $order->setAttribute('xmlns', 'http://schema.post.be/shm/deepintegration/v5/national');
        $order->setAttribute('xmlns:international', 'http://schema.post.be/shm/deepintegration/v5/international');
        $order->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $order->setAttribute('xsi:schemaLocation', 'http://schema.post.be/shm/deepintegration/v5/');
        $order->setAttribute('xmlns:national', 'http://schema.post.be/shm/deepintegration/v5/national');

        $document->appendChild($order);

        $order->appendChild($document->createElement('tns:accountId', $accountId));
        $order->appendChild($document->createElement('tns:reference', $this->getReference()));

        if ($this->getCostCenter() !== null) {
            $order->appendChild($document->createElement('tns:costCenter', $this->getCostCenter()));
        }

        foreach ($this->getLines() as $line) {
            $order->appendChild($line->toXML($document, 'tns'));
        }

        foreach ($this->getBoxes() as $box) {
            $order->appendChild($box->toXML($document, 'tns'));
        }

        return $order;
    }

    /**
     * @throws BpostXmlNoReferenceFoundException
     * @throws BpostNotImplementedException
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): Order
    {
        if (!isset($xml->reference)) {
            throw new BpostXmlNoReferenceFoundException();
        }

        $order = new Order((string) $xml->reference);

        if (isset($xml->costCenter) && (string) $xml->costCenter !== '') {
            $order->setCostCenter((string) $xml->costCenter);
        }

        if (isset($xml->orderLine)) {
            foreach ($xml->orderLine as $orderLine) {
                $order->addLine(Line::createFromXML($orderLine));
            }
        }

        if (isset($xml->box)) {
            foreach ($xml->box as $box) {
                $order->addBox(Box::createFromXML($box));
            }
        }

        return $order;
    }
}
