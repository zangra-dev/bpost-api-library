<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpack247;

use SimpleXMLElement;

/**
 * bPost Customer Pack Station class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class CustomerPackStation
{
    public function __construct(
        private ?string $customLabel = null,
        private ?string $orderNumber = null,
        private ?string $packstationId = null,
    ) {}

    public function setCustomLabel(?string $customLabel): void
    {
        $this->customLabel = $customLabel;
    }

    public function getCustomLabel(): ?string
    {
        return $this->customLabel;
    }

    public function setOrderNumber(?string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setPackstationId(?string $packstationId): void
    {
        $this->packstationId = $packstationId;
    }

    public function getPackstationId(): ?string
    {
        return $this->packstationId;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $packStation = new self();

        if (isset($xml->OrderNumber) && (string)$xml->OrderNumber !== '') {
            $packStation->setOrderNumber((string)$xml->OrderNumber);
        }
        if (isset($xml->CustomLabel) && (string)$xml->CustomLabel !== '') {
            $packStation->setCustomLabel((string)$xml->CustomLabel);
        }
        if (isset($xml->PackstationID) && (string)$xml->PackstationID !== '') {
            $packStation->setPackstationId((string)$xml->PackstationID);
        }

        return $packStation;
    }
}
