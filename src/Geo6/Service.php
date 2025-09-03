<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Geo6;

use SimpleXMLElement;

/**
 * Geo6 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Service
{
    private ?string $category = null;
    private ?string $flag     = null;
    private ?string $name     = null;

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setFlag(string $flag): void
    {
        $this->flag = $flag;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $service = new self();
        $service->setName((string) $xml);

        if (isset($xml['category']) && (string) $xml['category'] !== '') {
            $service->setCategory((string) $xml['category']);
        }
        if (isset($xml['flag']) && (string) $xml['flag'] !== '') {
            $service->setFlag((string) $xml['flag']);
        }

        return $service;
    }
}