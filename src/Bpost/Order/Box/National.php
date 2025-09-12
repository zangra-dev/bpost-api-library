<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\OpeningHour\Day;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Option;
use Bpost\BpostApiClient\BpostException;
use Bpost\BpostApiClient\Common\ComplexAttribute;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost National class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
abstract class National extends ComplexAttribute implements IBox
{
    protected ?string $product = null;

    /** @var Option[] */
    protected array $options = [];

    protected ?int $weight = null;

    /** @var Day[] */
    private array $openingHours = [];

    private ?string $desiredDeliveryPlace = null;

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

    public function setProduct(string $product): void
    {
        $this->product = $product;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    /**
     * @remark should be implemented by the child class
     */
    public static function getPossibleProductValues(): array
    {
        return [];
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setOpeningHours(array $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    public function addOpeningHour(Day $day): void
    {
        $this->openingHours[] = $day;
    }

    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    public function setDesiredDeliveryPlace(?string $desiredDeliveryPlace): void
    {
        $this->desiredDeliveryPlace = $desiredDeliveryPlace;
    }

    public function getDesiredDeliveryPlace(): ?string
    {
        return $this->desiredDeliveryPlace;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $typeElement = $document->createElement((string)$type);

        if ($this->product !== null) {
            $typeElement->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('product', $prefix), $this->product)
            );
        }

        if (!empty($this->options)) {
            $optionsElement = $document->createElement('options');
            foreach ($this->options as $option) {
                $optionsElement->appendChild($option->toXML($document));
            }
            $typeElement->appendChild($optionsElement);
        }

        if ($this->weight !== null) {
            $typeElement->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('weight', $prefix), (string)$this->weight)
            );
        }

        if (!empty($this->openingHours)) {
            $openingHoursElement = $document->createElement('openingHours');
            foreach ($this->openingHours as $day) {
                $openingHoursElement->appendChild($day->toXML($document));
            }
            $typeElement->appendChild($openingHoursElement);
        }

        if ($this->desiredDeliveryPlace !== null) {
            $typeElement->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('desiredDeliveryPlace', $prefix),
                    $this->desiredDeliveryPlace
                )
            );
        }

        return $typeElement;
    }

    /**
     * @throws BpostInvalidLengthException
     * @throws BpostNotImplementedException
     * @throws BpostInvalidValueException
     * @throws BpostException
     */
    public static function createFromXML(SimpleXMLElement $xml, National $self = null): National
    {
        if ($self === null) {
            throw new BpostException('Set an instance of National');
        }

        if (isset($nationalXml->product) && (string)$nationalXml->product !== '') {
            $self->setProduct((string)$nationalXml->product);
        }

        if (!empty($nationalXml->options)) {
            foreach ($nationalXml->options as $optionData) {
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

                $self->addOption($option);
            }
        }

        if (isset($nationalXml->weight) && (string)$nationalXml->weight !== '') {
            $self->setWeight((int)$nationalXml->weight);
        }

        if (isset($nationalXml->openingHours) && (string)$nationalXml->openingHours !== '') {
            foreach ($nationalXml->openingHours->children() as $day => $value) {
                $self->addOpeningHour(new Day((string)$day, (string)$value));
            }
        }

        if (isset($nationalXml->desiredDeliveryPlace) && (string)$nationalXml->desiredDeliveryPlace !== '') {
            $self->setDesiredDeliveryPlace((string)$nationalXml->desiredDeliveryPlace);
        }

        return $self;
    }

    /**
     * @throws BpostNotImplementedException
     */
    protected static function getOptionFromOptionData(SimpleXMLElement $optionData): Option
    {
        $className = '\\Bpost\\BpostApiClient\\Bpost\\Order\\Box\\Option\\' . ucfirst($optionData->getName());
        XmlHelper::assertMethodCreateFromXmlExists($className);

        /** @var callable $factory */
        $factory = [$className, 'createFromXML'];
        return $factory($optionData);
    }
}