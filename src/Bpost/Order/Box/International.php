<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\CustomsInfo\CustomsInfo;
use Bpost\BpostApiClient\Bpost\Order\Box\International\ParcelContent;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Option;
use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use DOMDocument;
use DOMElement;
use DOMException;
use SimpleXMLElement;

/**
 * bPost International class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class International implements IBox
{
    private ?string $product = null;

    private array $options = [];

    private ?Receiver $receiver = null;

    private ?int $parcelWeight = null;

    private ?CustomsInfo $customsInfo = null;

    private array $parcelContents = [];

    public function setCustomsInfo(?CustomsInfo $customsInfo): void
    {
        $this->customsInfo = $customsInfo;
    }

    public function getCustomsInfo(): ?CustomsInfo
    {
        return $this->customsInfo;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    public function setParcelWeight(?int $parcelWeight): void
    {
        $this->parcelWeight = $parcelWeight;
    }

    public function getParcelWeight(): ?int
    {
        return $this->parcelWeight;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setProduct(string $product): void
    {
        if (!in_array($product, self::getPossibleProductValues(), true)) {
            throw new BpostInvalidValueException('product', $product, self::getPossibleProductValues());
        }
        $this->product = $product;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public static function getPossibleProductValues(): array
    {
        return [
            Product::PRODUCT_NAME_BPACK_WORLD_BUSINESS,
            Product::PRODUCT_NAME_BPACK_WORLD_EASY_RETURN,
            Product::PRODUCT_NAME_BPACK_WORLD_EXPRESS_PRO,
            Product::PRODUCT_NAME_BPACK_EUROPE_BUSINESS,
            Product::PRODUCT_NAME_BPACK_AT_BPOST_INTERNATIONAL,
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

    public function getParcelContents(): array
    {
        return $this->parcelContents;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setParcelContents(array $parcelContents): self
    {
        foreach ($parcelContents as $parcelContent) {
            if (!$parcelContent instanceof ParcelContent) {
                throw new BpostInvalidValueException(
                    'parcelContents',
                    is_object($parcelContent) ? get_class($parcelContent) : gettype($parcelContent),
                    ['Bpost\\BpostApiClient\\Bpost\\Order\\Box\\International\\ParcelContent']
                );
            }
            $this->addParcelContent($parcelContent);
        }
        return $this;
    }

    public function addParcelContent(ParcelContent $parcelContent): void
    {
        $this->parcelContents[] = $parcelContent;
    }

    /**
     * @throws DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $internationalBox = $document->createElement(XmlHelper::getPrefixedTagName('internationalBox', $prefix));
        $innerPrefix = 'international';
        $international = $document->createElement(XmlHelper::getPrefixedTagName('international', $innerPrefix));
        $internationalBox->appendChild($international);

        if ($this->product !== null) {
            $international->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('product', $innerPrefix), $this->product)
            );
        }

        if (!empty($this->options)) {
            $optionsElement = $document->createElement(XmlHelper::getPrefixedTagName('options', $innerPrefix));
            foreach ($this->options as $option) {
                $optionsElement->appendChild($option->toXML($document, 'common'));
            }
            $international->appendChild($optionsElement);
        }

        if ($this->receiver !== null) {
            $international->appendChild($this->receiver->toXML($document, $innerPrefix));
        }

        if ($this->parcelWeight !== null) {
            $international->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('parcelWeight', $innerPrefix),
                    (string)$this->parcelWeight
                )
            );
        }

        if ($this->customsInfo !== null) {
            $international->appendChild($this->customsInfo->toXML($document, $innerPrefix));
        }

        if (!empty($this->parcelContents)) {
            $parcelContents = $document->createElement(XmlHelper::getPrefixedTagName('parcelContents', $innerPrefix));
            foreach ($this->parcelContents as $parcelContent) {
                $parcelContents->appendChild($parcelContent->toXML($document, $innerPrefix));
            }
            $international->appendChild($parcelContents);
        }

        return $internationalBox;
    }

    /**
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(SimpleXMLElement $xml): International
    {
        $international = new self();

        if (isset($xml->international->product) && (string)$xml->international->product !== '') {
            $international->setProduct((string)$xml->international->product);
        }

        if (isset($xml->international->options)) {
            $options = $xml->international->options->children('http://schema.post.be/shm/deepintegration/v3/common');
            foreach ($options as $optionData) {
                switch ($optionData->getName()) {
                    case Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED:
                    case Messaging::MESSAGING_TYPE_KEEP_ME_INFORMED:
                    case Messaging::MESSAGING_TYPE_INFO_REMINDER:
                    case Messaging::MESSAGING_TYPE_INFO_NEXT_DAY:
                        $option = Messaging::createFromXML($optionData);
                        break;
                    default:
                        $className = '\\Bpost\\BpostApiClient\\Bpost\\Order\\Box\\Option\\' . ucfirst($optionData->getName());
                        XmlHelper::assertMethodCreateFromXmlExists($className);
                        $option = $className::createFromXML($optionData);
                }
                $international->addOption($option);
            }
        }

        if (isset($xml->international->parcelWeight) && (string)$xml->international->parcelWeight !== '') {
            $international->setParcelWeight((int)$xml->international->parcelWeight);
        }

        if (isset($xml->international->receiver)) {
            $receiverData = $xml->international->receiver->children('http://schema.post.be/shm/deepintegration/v3/common');
            $international->setReceiver(Receiver::createFromXML($receiverData));
        }

        if (isset($xml->international->customsInfo)) {
            $international->setCustomsInfo(CustomsInfo::createFromXML($xml->international->customsInfo));
        }

        if (isset($xml->international->parcelContents)) {
            $parcelContents = $xml->international->parcelContents->children('international', true);
            foreach ($parcelContents as $parcelContentXml) {
                $international->addParcelContent(ParcelContent::createFromXML($parcelContentXml));
            }
        }

        return $international;
    }
}