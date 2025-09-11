<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost;

use Bpost\BpostApiClient\Bpost\ProductConfiguration\DeliveryMethod;
use SimpleXMLElement;

/**
 * Class ProductConfiguration
 */
class ProductConfiguration
{
    private array $deliveryMethods = [];

    public function getDeliveryMethods(): array
    {
        return $this->deliveryMethods;
    }

    public function addDeliveryMethod(DeliveryMethod $deliveryMethod): void
    {
        $this->deliveryMethods[] = $deliveryMethod;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $productConfiguration = new self();

        if (isset($xml->deliveryMethod)) {
            foreach ($xml->deliveryMethod as $deliveryMethodXml) {
                $productConfiguration->addDeliveryMethod(
                    DeliveryMethod::createFromXML($deliveryMethodXml)
                );
            }
        }

        return $productConfiguration;
    }
}
