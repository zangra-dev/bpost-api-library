<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost AtHome class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class AtHome extends National
{
    private ?Receiver $receiver = null;
    protected ?string $requestedDeliveryDate = null;

    /**
     * @throws BpostInvalidValueException
     */
    public function setProduct(string $product): void
    {
        if (!in_array($product, self::getPossibleProductValues(), true)) {
            throw new BpostInvalidValueException('product', $product, self::getPossibleProductValues());
        }
        parent::setProduct($product);
    }

    public static function getPossibleProductValues(): array
    {
        return [
            Product::PRODUCT_NAME_BPACK_24H_PRO,
            Product::PRODUCT_NAME_BPACK_24H_BUSINESS,
            Product::PRODUCT_NAME_BPACK_BUSINESS,
            Product::PRODUCT_NAME_BPACK_PALLET,
            Product::PRODUCT_NAME_BPACK_EASY_RETOUR,
        ];
    }

    public function setReceiver(?Receiver $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getReceiver(): ?Receiver
    {
        return $this->receiver;
    }

    public function getRequestedDeliveryDate(): ?string
    {
        return $this->requestedDeliveryDate;
    }

    public function setRequestedDeliveryDate(?string $requestedDeliveryDate): void
    {
        $this->requestedDeliveryDate = $requestedDeliveryDate;
    }

    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $nationalElement = $document->createElement(XmlHelper::getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'atHome');
        $nationalElement->appendChild($boxElement);

        if ($this->receiver !== null) {
            $boxElement->appendChild($this->receiver->toXML($document));
        }

        $this->addToXmlRequestedDeliveryDate($document, $boxElement);

        return $nationalElement;
    }

    /**
     * @throws \DOMException
     */
    protected function addToXmlRequestedDeliveryDate(DOMDocument $document, DOMElement $typeElement): void
    {
        if ($this->requestedDeliveryDate !== null) {
            $typeElement->appendChild(
                $document->createElement('requestedDeliveryDate', $this->requestedDeliveryDate)
            );
        }
    }

    public static function createFromXML(SimpleXMLElement $xml, National $self = null): AtHome
    {
        if ($self === null) {
            $self = new self();
        }

        if (!isset($xml->atHome)) {
            throw new BpostXmlInvalidItemException();
        }

        $atHomeXml = $xml->atHome[0];

        /** @var AtHome $self */
        $self = parent::createFromXML($atHomeXml, $self);

        if (isset($atHomeXml->receiver)) {
            $self->setReceiver(
                Receiver::createFromXML(
                    $atHomeXml->receiver->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }

        if (isset($atHomeXml->requestedDeliveryDate) && (string)$atHomeXml->requestedDeliveryDate !== '') {
            $self->setRequestedDeliveryDate((string)$atHomeXml->requestedDeliveryDate);
        }

        return $self;
    }
}
