<?php
namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Box\National\ShopHandlingInstruction;
use Bpost\BpostApiClient\Bpost\Order\PugoAddress;
use Bpost\BpostApiClient\Bpost\ProductConfiguration\Product;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Messaging;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;

/**
 * bPost AtBpost class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class AtIntlPugo extends International
{
    /** @var string */
    protected $product = Product::PRODUCT_NAME_BPACK_AT_BPOST_INTERNATIONAL;

    /** @var string */
    private $pugoId;

    /** @var string */
    private $pugoName;

    /** @var \Bpost\BpostApiClient\Bpost\Order\PugoAddress */
    private $pugoAddress;

    /** @var string */
    private $receiverName;

    /** @var string */
    private $receiverCompany;

    /** @var string */
    protected $requestedDeliveryDate;

    /** @var ShopHandlingInstruction */
    private $shopHandlingInstruction;

    /**
     * @param string $product Possible values are: bpack@bpost
     * @throws BpostInvalidValueException
     */
    public function setProduct($product)
    {
        if (!in_array($product, self::getPossibleProductValues())) {
            throw new BpostInvalidValueException('product', $product, self::getPossibleProductValues());
        }

        parent::setProduct($product);
    }

    /**
     * @return array
     */
    public static function getPossibleProductValues()
    {
        return array(
            Product::PRODUCT_NAME_BPACK_AT_BPOST,
        );
    }

    /**
     * @param \Bpost\BpostApiClient\Bpost\Order\PugoAddress $pugoAddress
     */
    public function setPugoAddress($pugoAddress)
    {
        $this->pugoAddress = $pugoAddress;
    }

    /**
     * @return \Bpost\BpostApiClient\Bpost\Order\PugoAddress
     */
    public function getPugoAddress()
    {
        return $this->pugoAddress;
    }

    /**
     * @param string $pugoId
     */
    public function setPugoId($pugoId)
    {
        $this->pugoId = $pugoId;
    }

    /**
     * @return string
     */
    public function getPugoId()
    {
        return $this->pugoId;
    }

    /**
     * @param string $pugoName
     */
    public function setPugoName($pugoName)
    {
        $this->pugoName = $pugoName;
    }

    /**
     * @return string
     */
    public function getPugoName()
    {
        return $this->pugoName;
    }

    /**
     * @param string $receiverCompany
     */
    public function setReceiverCompany($receiverCompany)
    {
        $this->receiverCompany = $receiverCompany;
    }

    /**
     * @return string
     */
    public function getReceiverCompany()
    {
        return $this->receiverCompany;
    }

    /**
     * @param string $receiverName
     */
    public function setReceiverName($receiverName)
    {
        $this->receiverName = $receiverName;
    }

    /**
     * @return string
     */
    public function getReceiverName()
    {
        return $this->receiverName;
    }

    /**
     * @return string
     */
    public function getRequestedDeliveryDate()
    {
        return $this->requestedDeliveryDate;
    }

    /**
     * @param string $requestedDeliveryDate
     */
    public function setRequestedDeliveryDate($requestedDeliveryDate)
    {
        $this->requestedDeliveryDate = $requestedDeliveryDate;
    }

    /**
     * @return string
     */
    public function getShopHandlingInstruction()
    {
        if ($this->shopHandlingInstruction !== null) {
            return $this->shopHandlingInstruction->getValue();
        }
        return null;
    }

    /**
     * @param string $shopHandlingInstruction
     */
    public function setShopHandlingInstruction($shopHandlingInstruction)
    {
        $this->shopHandlingInstruction = new ShopHandlingInstruction($shopHandlingInstruction);
    }

    /**
     * Return the object as an array for usage in the XML
     *
     * @param  \DomDocument $document
     * @param  string       $prefix
     * @param  string       $type
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null, $type = null)
    {
        $internationalBox = $document->createElement($this->getPrefixedTagName('internationalBox', 'tns'));
        $boxElement = parent::toPugoXML($document, 'international', 'atIntlPugo');

        $internationalBox->appendChild($boxElement);

        if ($this->getPugoId() !== null) {
            $boxElement->appendChild(
                $document->createElement('international:pugoId', $this->getPugoId())
            );
        }
        if ($this->getPugoName() !== null) {
            $boxElement->appendChild(
                $document->createElement('international:pugoName', $this->getPugoName())
            );
        }

        if ($this->getPugoAddress() !== null) {
            $boxElement->appendChild(
                $this->getPugoAddress()->toXML($document, 'common', true)
            );
        }

        $this->addToXmlRequestedDeliveryDate($document, $boxElement, $prefix);
        $this->addToXmlShopHandlingInstruction($document, $boxElement, $prefix);
        return $internationalBox;
    }

    /**
     * @param \DOMDocument $document
     * @param \DOMElement  $typeElement
     * @param string       $prefix
     */
    protected function addToXmlRequestedDeliveryDate(\DOMDocument $document, \DOMElement $typeElement, $prefix)
    {
        if ($this->getRequestedDeliveryDate() !== null) {
            $typeElement->appendChild(
                $document->createElement('requestedDeliveryDate', $this->getRequestedDeliveryDate())
            );
        }
    }

    private function addToXmlShopHandlingInstruction(\DOMDocument $document, \DOMElement $typeElement, $prefix)
    {
        if ($this->getShopHandlingInstruction() !== null) {
            $typeElement->appendChild(
                $document->createElement('shopHandlingInstruction', $this->getShopHandlingInstruction())
            );
        }
    }

    /**
     * @param  \SimpleXMLElement $xml
     *
     * @return AtBpost
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        dump(__METHOD__);
        $atBpost = new AtBpost();

        if (isset($xml->atBpost->product) && $xml->atBpost->product != '') {
            $atBpost->setProduct(
                (string)$xml->atBpost->product
            );
        }
        if (isset($xml->atBpost->options)) {
            /** @var \SimpleXMLElement $optionData */
            foreach ($xml->atBpost->options as $optionData) {
                $optionData = $optionData->children('http://schema.post.be/shm/deepintegration/v3/common');

                if (in_array(
                    $optionData->getName(),
                    array(
                        Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED,
                        Messaging::MESSAGING_TYPE_INFO_NEXT_DAY,
                        Messaging::MESSAGING_TYPE_INFO_REMINDER,
                        Messaging::MESSAGING_TYPE_KEEP_ME_INFORMED,
                    )
                )
                ) {
                    $option = Messaging::createFromXML($optionData);
                } else {
                    $className = '\\Bpost\\BpostApiClient\\Bpost\\Order\\Box\\Option\\' . ucfirst($optionData->getName());
                    if (!method_exists($className, 'createFromXML')) {
                        throw new BpostNotImplementedException();
                    }
                    $option = call_user_func(
                        array($className, 'createFromXML'),
                        $optionData
                    );
                }

                $atBpost->addOption($option);
            }
        }
        if (isset($xml->atBpost->weight) && $xml->atBpost->weight != '') {
            $atBpost->setWeight(
                (int)$xml->atBpost->weight
            );
        }
        if (isset($xml->atBpost->receiverName) && $xml->atBpost->receiverName != '') {
            $atBpost->setReceiverName(
                (string)$xml->atBpost->receiverName
            );
        }
        if (isset($xml->atBpost->receiverCompany) && $xml->atBpost->receiverCompany != '') {
            $atBpost->setReceiverCompany(
                (string)$xml->atBpost->receiverCompany
            );
        }
        if (isset($xml->atBpost->pugoId) && $xml->atBpost->pugoId != '') {
            $atBpost->setPugoId(
                (string)$xml->atBpost->pugoId
            );
        }
        if (isset($xml->atBpost->pugoName) && $xml->atBpost->pugoName != '') {
            $atBpost->setPugoName(
                (string)$xml->atBpost->pugoName
            );
        }
        if (isset($xml->atBpost->pugoAddress)) {
            /** @var \SimpleXMLElement $pugoAddressData */
            $pugoAddressData = $xml->atBpost->pugoAddress->children(
                'http://schema.post.be/shm/deepintegration/v3/common'
            );
            $atBpost->setPugoAddress(
                PugoAddress::createFromXML($pugoAddressData)
            );
        }
        if (isset($xml->atBpost->requestedDeliveryDate) && $xml->atBpost->requestedDeliveryDate != '') {
            $atBpost->setRequestedDeliveryDate(
                (string)$xml->atBpost->requestedDeliveryDate
            );
        }
        if (isset($xml->atBpost->shopHandlingInstruction) && $xml->atBpost->shopHandlingInstruction != '') {
            $atBpost->setShopHandlingInstruction(
                (string)$xml->atBpost->shopHandlingInstruction
            );
        }

        return $atBpost;
    }
}
