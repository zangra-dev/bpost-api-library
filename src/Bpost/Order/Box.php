<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Bpost\Order\Box\International;
use Bpost\BpostApiClient\Bpost\Order\Box\National;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Box class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Box
{
    public const BOX_STATUS_OPEN             = 'OPEN';
    public const BOX_STATUS_PENDING          = 'PENDING';
    public const BOX_STATUS_PRINTED          = 'PRINTED';
    public const BOX_STATUS_CANCELLED        = 'CANCELLED';
    public const BOX_STATUS_ON_HOLD          = 'ON-HOLD';
    public const BOX_STATUS_ANNOUNCED        = 'ANNOUNCED';
    public const BOX_STATUS_IN_TRANSIT       = 'IN_TRANSIT';
    public const BOX_STATUS_AWAITING_PICKUP  = 'AWAITING_PICKUP';
    public const BOX_STATUS_DELIVERED        = 'DELIVERED';
    public const BOX_STATUS_BACK_TO_SENDER   = 'BACK_TO_SENDER';

    private ?Sender $sender = null;
    private ?National $nationalBox = null;
    private ?International $internationalBox = null;

    private ?string $remark = null;
    private ?string $status = null;
    private ?string $barcode = null;
    private ?string $additionalCustomerReference = null;

    public function setInternationalBox(International $internationalBox): void
    {
        $this->internationalBox = $internationalBox;
    }

    public function getInternationalBox(): ?International
    {
        return $this->internationalBox;
    }

    public function setNationalBox(National $nationalBox): void
    {
        $this->nationalBox = $nationalBox;
    }

    public function getNationalBox(): ?National
    {
        return $this->nationalBox;
    }

    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setSender(Sender $sender): void
    {
        $this->sender = $sender;
    }

    public function getSender(): ?Sender
    {
        return $this->sender;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setStatus(string $status): void
    {
        $status = strtoupper($status);
        if (!in_array($status, self::getPossibleStatusValues(), true)) {
            throw new BpostInvalidValueException('status', $status, self::getPossibleStatusValues());
        }
        $this->status = $status;
    }

    public function setBarcode(string $barcode): void
    {
        $this->barcode = strtoupper($barcode);
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setAdditionalCustomerReference(string $additionalCustomerReference): void
    {
        $this->additionalCustomerReference = $additionalCustomerReference;
    }

    public function getAdditionalCustomerReference(): ?string
    {
        return $this->additionalCustomerReference;
    }


    public static function getPossibleStatusValues(): array
    {
        return [
            self::BOX_STATUS_OPEN,
            self::BOX_STATUS_PENDING,
            self::BOX_STATUS_PRINTED,
            self::BOX_STATUS_CANCELLED,
            self::BOX_STATUS_ON_HOLD,
            self::BOX_STATUS_ANNOUNCED,
            self::BOX_STATUS_IN_TRANSIT,
            self::BOX_STATUS_AWAITING_PICKUP,
            self::BOX_STATUS_DELIVERED,
            self::BOX_STATUS_BACK_TO_SENDER,
        ];
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        $box = $document->createElement(XmlHelper::getPrefixedTagName('box', $prefix));

        $this->senderToXML($document, $prefix, $box);
        $this->boxToXML($document, $prefix, $box);
        $this->remarkToXML($document, $prefix, $box);
        $this->additionalCustomerReferenceToXML($document, $prefix, $box);
        $this->barcodeToXML($document, $prefix, $box);

        return $box;
    }

    /**
     * @throws BpostInvalidValueException
     * @throws BpostNotImplementedException
     */
    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $box = new self();

        if (isset($xml->sender)) {
            $box->setSender(
                Sender::createFromXML(
                    $xml->sender->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }

        if (isset($xml->nationalBox)) {
            $nationalBoxData = $xml->nationalBox->children('http://schema.post.be/shm/deepintegration/v3/national');
            $classNameExtracted = $nationalBoxData->getName();
            if ($classNameExtracted === 'at24-7') {
                $classNameExtracted = 'at247';
            }
            $className = '\\Bpost\\BpostApiClient\\Bpost\\Order\\Box\\' . ucfirst((string)$classNameExtracted);
            XmlHelper::assertMethodCreateFromXmlExists($className);

            /** @var National $nationalBox */
            $nationalBox = call_user_func([$className, 'createFromXML'], $nationalBoxData);
            $box->setNationalBox($nationalBox);
        }

        if (isset($xml->internationalBox)) {
            $internationalBoxData = $xml->internationalBox->children('http://schema.post.be/shm/deepintegration/v3/international');
            $classNameExtracted = $internationalBoxData->getName();
            if ($classNameExtracted === 'atIntlHome') {
                $classNameExtracted = 'international';
            }
            $className = '\\Bpost\\BpostApiClient\\Bpost\\Order\\Box\\' . ucfirst((string)$classNameExtracted);
            XmlHelper::assertMethodCreateFromXmlExists($className);

            /** @var International $internationalBox */
            $internationalBox = call_user_func([$className, 'createFromXML'], $internationalBoxData);
            $box->setInternationalBox($internationalBox);
        }

        if (isset($xml->remark) && (string) $xml->remark !== '') {
            $box->setRemark((string)$xml->remark);
        }
        if (isset($xml->additionalCustomerReference) && (string) $xml->additionalCustomerReference !== '') {
            $box->setAdditionalCustomerReference((string)$xml->additionalCustomerReference);
        }
        if (!empty($xml->barcode)) {
            $box->setBarcode((string)$xml->barcode);
        }
        if (isset($xml->status) && (string) $xml->status !== '') {
            $box->setStatus((string)$xml->status);
        }

        return $box;
    }

    /**
     * @throws \DOMException
     */
    private function barcodeToXML(DOMDocument $document, ?string $prefix, DOMElement $box): void
    {
        if ($this->barcode !== null) {
            $box->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('barcode', $prefix), $this->barcode)
            );
        }
    }

    /**
     * @throws \DOMException
     */
    private function boxToXML(DOMDocument $document, ?string $prefix, DOMElement $box): void
    {
        if ($this->nationalBox !== null) {
            $box->appendChild($this->nationalBox->toXML($document, $prefix));
        }
        if ($this->internationalBox !== null) {
            $box->appendChild($this->internationalBox->toXML($document, $prefix));
        }
    }

    private function senderToXML(DOMDocument $document, ?string $prefix, DOMElement $box): void
    {
        if ($this->sender !== null) {
            $box->appendChild($this->sender->toXML($document, $prefix));
        }
    }

    /**
     * @throws \DOMException
     */
    private function remarkToXML(DOMDocument $document, ?string $prefix, DOMElement $box): void
    {
        if ($this->remark !== null) {
            $box->appendChild(
                $document->createElement(XmlHelper::getPrefixedTagName('remark', $prefix), $this->remark)
            );
        }
    }

    private function additionalCustomerReferenceToXML(DOMDocument $document, ?string $prefix, DOMElement $box): void
    {
        if ($this->additionalCustomerReference !== null) {
            $box->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('additionalCustomerReference', $prefix),
                    $this->additionalCustomerReference
                )
            );
        }
    }
}