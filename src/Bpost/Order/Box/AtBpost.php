<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\National\ShopHandlingInstruction;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\PugoAddress;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost AtBpost class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class AtBpost extends National
{
    protected ?string $product = Product::PRODUCT_NAME_BPACK_AT_BPOST;
    private ?string $pugoId = null;
    private ?string $pugoName = null;
    private ?PugoAddress $pugoAddress = null;
    private ?string $receiverName = null;
    private ?string $receiverCompany = null;
    protected ?string $requestedDeliveryDate = null;
    private ?ShopHandlingInstruction $shopHandlingInstruction = null;

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
            Product::PRODUCT_NAME_BPACK_AT_BPOST,
        ];
    }

    public function setPugoAddress(?PugoAddress $pugoAddress): void
    {
        $this->pugoAddress = $pugoAddress;
    }

    public function getPugoAddress(): ?PugoAddress
    {
        return $this->pugoAddress;
    }

    public function setPugoId(?string $pugoId): void
    {
        $this->pugoId = $pugoId;
    }

    public function getPugoId(): ?string
    {
        return $this->pugoId;
    }

    public function setPugoName(?string $pugoName): void
    {
        $this->pugoName = $pugoName;
    }

    public function getPugoName(): ?string
    {
        return $this->pugoName;
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

    public function getShopHandlingInstruction(): ?string
    {
        return $this->shopHandlingInstruction?->getValue();
    }

    public function setShopHandlingInstruction(?string $shopHandlingInstruction): void
    {
        $this->shopHandlingInstruction = $shopHandlingInstruction !== null
            ? new ShopHandlingInstruction($shopHandlingInstruction)
            : null;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $nationalElement = $document->createElement(XmlHelper::getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'atBpost');
        $nationalElement->appendChild($boxElement);

        if ($this->pugoId !== null) {
            $boxElement->appendChild($document->createElement('pugoId', $this->pugoId));
        }
        if ($this->pugoName !== null) {
            $boxElement->appendChild($document->createElement('pugoName', $this->pugoName));
        }
        if ($this->pugoAddress !== null) {
            $boxElement->appendChild($this->pugoAddress->toXML($document, 'common'));
        }
        if ($this->receiverName !== null) {
            $boxElement->appendChild($document->createElement('receiverName', $this->receiverName));
        }
        if ($this->receiverCompany !== null) {
            $boxElement->appendChild($document->createElement('receiverCompany', $this->receiverCompany));
        }

        $this->addToXmlRequestedDeliveryDate($document, $boxElement, $prefix);
        $this->addToXmlShopHandlingInstruction($document, $boxElement, $prefix);

        return $nationalElement;
    }

    /**
     * @throws \DOMException
     */
    protected function addToXmlRequestedDeliveryDate(DOMDocument $document, DOMElement $typeElement, ?string $prefix): void
    {
        if ($this->requestedDeliveryDate !== null) {
            $typeElement->appendChild(
                $document->createElement('requestedDeliveryDate', $this->requestedDeliveryDate)
            );
        }
    }

    private function addToXmlShopHandlingInstruction(DOMDocument $document, DOMElement $typeElement, ?string $prefix): void
    {
        $value = $this->getShopHandlingInstruction();
        if ($value !== null) {
            $typeElement->appendChild($document->createElement('shopHandlingInstruction', $value));
        }
    }

    /**
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     * @throws BpostInvalidLengthException
     */
    public static function createFromXML(SimpleXMLElement $xml, National $self = null): AtBpost
    {
        $atBpost = new AtBpost();

        if (isset($xml->atBpost->product) && (string)$xml->atBpost->product !== '') {
            $atBpost->setProduct((string)$xml->atBpost->product);
        }

        if (isset($xml->atBpost->options)) {
            foreach ($xml->atBpost->options as $optionData) {
                $optionData = $optionData->children('http://schema.post.be/shm/deepintegration/v3/common');

                if (in_array(
                    $optionData->getName(),
                    [
                        Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED,
                        Messaging::MESSAGING_TYPE_INFO_NEXT_DAY,
                        Messaging::MESSAGING_TYPE_INFO_REMINDER,
                        Messaging::MESSAGING_TYPE_KEEP_ME_INFORMED,
                    ],
                    true
                )) {
                    $option = Messaging::createFromXML($optionData);
                } else {
                    $option = self::getOptionFromOptionData($optionData);
                }

                $atBpost->addOption($option);
            }
        }

        if (isset($xml->atBpost->weight) && (string)$xml->atBpost->weight !== '') {
            $atBpost->setWeight((int)$xml->atBpost->weight);
        }
        if (isset($xml->atBpost->receiverName) && (string)$xml->atBpost->receiverName !== '') {
            $atBpost->setReceiverName((string)$xml->atBpost->receiverName);
        }
        if (isset($xml->atBpost->receiverCompany) && (string)$xml->atBpost->receiverCompany !== '') {
            $atBpost->setReceiverCompany((string)$xml->atBpost->receiverCompany);
        }
        if (isset($xml->atBpost->pugoId) && (string)$xml->atBpost->pugoId !== '') {
            $atBpost->setPugoId((string)$xml->atBpost->pugoId);
        }
        if (isset($xml->atBpost->pugoName) && (string)$xml->atBpost->pugoName !== '') {
            $atBpost->setPugoName((string)$xml->atBpost->pugoName);
        }
        if (isset($xml->atBpost->pugoAddress)) {
            $pugoAddressData = $xml->atBpost->pugoAddress
                ->children('http://schema.post.be/shm/deepintegration/v3/common');
            $atBpost->setPugoAddress(PugoAddress::createFromXML($pugoAddressData));
        }
        if (isset($xml->atBpost->requestedDeliveryDate) && (string)$xml->atBpost->requestedDeliveryDate !== '') {
            $atBpost->setRequestedDeliveryDate((string)$xml->atBpost->requestedDeliveryDate);
        }
        if (isset($xml->atBpost->shopHandlingInstruction) && (string)$xml->atBpost->shopHandlingInstruction !== '') {
            $atBpost->setShopHandlingInstruction((string)$xml->atBpost->shopHandlingInstruction);
        }

        return $atBpost;
    }
}