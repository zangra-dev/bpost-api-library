<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\National\Unregistered;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\ParcelsDepotAddress;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost At247 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class At247 extends National
{
    private ?string $parcelsDepotId = null;
    private ?string $parcelsDepotName = null;
    private ?ParcelsDepotAddress $parcelsDepotAddress = null;

    /** @var string */
    protected string $product = Product::PRODUCT_NAME_BPACK_24H_PRO;

    private ?string $memberId = null;
    private ?Unregistered $unregistered = null;
    private ?string $receiverName = null;
    private ?string $receiverCompany = null;
    protected ?string $requestedDeliveryDate = null;

    public function setMemberId(?string $memberId): void
    {
        $this->memberId = $memberId;
    }

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function setParcelsDepotAddress(?ParcelsDepotAddress $parcelsDepotAddress): void
    {
        $this->parcelsDepotAddress = $parcelsDepotAddress;
    }

    public function getParcelsDepotAddress(): ?ParcelsDepotAddress
    {
        return $this->parcelsDepotAddress;
    }

    public function setParcelsDepotId(?string $parcelsDepotId): void
    {
        $this->parcelsDepotId = $parcelsDepotId;
    }

    public function getParcelsDepotId(): ?string
    {
        return $this->parcelsDepotId;
    }

    public function setParcelsDepotName(?string $parcelsDepotName): void
    {
        $this->parcelsDepotName = $parcelsDepotName;
    }

    public function getParcelsDepotName(): ?string
    {
        return $this->parcelsDepotName;
    }

    public function getUnregistered(): ?Unregistered
    {
        return $this->unregistered;
    }

    public function setUnregistered(?Unregistered $unregistered): void
    {
        $this->unregistered = $unregistered;
    }

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
            Product::PRODUCT_NAME_BPACK_24_7,
        ];
    }

    public function setReceiverCompany(?string $receiverCompany): void
    {
        $this->receiverCompany = $receiverCompany;
    }

    public function getReceiverCompany(): ?string
    {
        return $this->receiverCompany;
    }

    public function setReceiverName(?string $receiverName): void
    {
        $this->receiverName = $receiverName;
    }

    public function getReceiverName(): ?string
    {
        return $this->receiverName;
    }

    public function getRequestedDeliveryDate(): ?string
    {
        return $this->requestedDeliveryDate;
    }

    public function setRequestedDeliveryDate(?string $requestedDeliveryDate): void
    {
        $this->requestedDeliveryDate = $requestedDeliveryDate;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $nationalElement = $document->createElement(XmlHelper::getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'at24-7');
        $nationalElement->appendChild($boxElement);

        if ($this->parcelsDepotId !== null) {
            $boxElement->appendChild($document->createElement('parcelsDepotId', $this->parcelsDepotId));
        }
        if ($this->parcelsDepotName !== null) {
            $boxElement->appendChild($document->createElement('parcelsDepotName', $this->parcelsDepotName));
        }
        if ($this->parcelsDepotAddress !== null) {
            $boxElement->appendChild($this->parcelsDepotAddress->toXML($document));
        }
        if ($this->memberId !== null) {
            $boxElement->appendChild($document->createElement('memberId', $this->memberId));
        }

        $this->addToXmlUnregistered($document, $boxElement, $prefix);

        if ($this->receiverName !== null) {
            $boxElement->appendChild($document->createElement('receiverName', $this->receiverName));
        }
        if ($this->receiverCompany !== null) {
            $boxElement->appendChild($document->createElement('receiverCompany', $this->receiverCompany));
        }

        $this->addToXmlRequestedDeliveryDate($document, $boxElement, $prefix);

        return $nationalElement;
    }

    protected function addToXmlRequestedDeliveryDate(DOMDocument $document, DOMElement $typeElement, ?string $prefix): void
    {
        $date = $this->requestedDeliveryDate;
        if ($date !== null && $date !== '') {
            $typeElement->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('requestedDeliveryDate', $prefix), $date)
            );
        }
    }

    /**
     * @throws \DOMException
     */
    protected function addToXmlUnregistered(DOMDocument $document, DOMElement $typeElement, ?string $prefix): void
    {
        if ($this->unregistered !== null) {
            $typeElement->appendChild($this->unregistered->toXml($document, $prefix, null));
        }
    }

    /**
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     * @throws BpostInvalidLengthException
     */
    public static function createFromXML(SimpleXMLElement $xml, ?National $self = null): At247
    {
        $at247 = new At247();

        if (isset($xml->{'at24-7'}->product) && (string)$xml->{'at24-7'}->product !== '') {
            $at247->setProduct((string)$xml->{'at24-7'}->product);
        }

        if (isset($xml->{'at24-7'}->options)) {
            foreach ($xml->{'at24-7'}->options as $optionData) {
                $optionData = $optionData->children('http://schema.post.be/shm/deepintegration/v3/common');

                if ($optionData->getName() === Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED) {
                    $option = Messaging::createFromXML($optionData);
                } else {
                    $option = self::getOptionFromOptionData($optionData);
                }

                $at247->addOption($option);
            }
        }

        if (isset($xml->{'at24-7'}->weight) && (string)$xml->{'at24-7'}->weight !== '') {
            $at247->setWeight((int)$xml->{'at24-7'}->weight);
        }
        if (isset($xml->{'at24-7'}->memberId) && (string)$xml->{'at24-7'}->memberId !== '') {
            $at247->setMemberId((string)$xml->{'at24-7'}->memberId);
        }
        if (isset($xml->{'at24-7'}->receiverName) && (string)$xml->{'at24-7'}->receiverName !== '') {
            $at247->setReceiverName((string)$xml->{'at24-7'}->receiverName);
        }
        if (isset($xml->{'at24-7'}->receiverCompany) && (string)$xml->{'at24-7'}->receiverCompany !== '') {
            $at247->setReceiverCompany((string)$xml->{'at24-7'}->receiverCompany);
        }
        if (isset($xml->{'at24-7'}->parcelsDepotId) && (string)$xml->{'at24-7'}->parcelsDepotId !== '') {
            $at247->setParcelsDepotId((string)$xml->{'at24-7'}->parcelsDepotId);
        }
        if (isset($xml->{'at24-7'}->parcelsDepotName) && (string)$xml->{'at24-7'}->parcelsDepotName !== '') {
            $at247->setParcelsDepotName((string)$xml->{'at24-7'}->parcelsDepotName);
        }
        if (isset($xml->{'at24-7'}->parcelsDepotAddress)) {
            $parcelsDepotAddressData = $xml->{'at24-7'}->parcelsDepotAddress
                ->children('http://schema.post.be/shm/deepintegration/v3/common');
            $at247->setParcelsDepotAddress(ParcelsDepotAddress::createFromXML($parcelsDepotAddressData));
        }
        if (isset($xml->{'at24-7'}->requestedDeliveryDate) && (string)$xml->{'at24-7'}->requestedDeliveryDate !== '') {
            $at247->setRequestedDeliveryDate((string)$xml->{'at24-7'}->requestedDeliveryDate);
        }

        return $at247;
    }
}
