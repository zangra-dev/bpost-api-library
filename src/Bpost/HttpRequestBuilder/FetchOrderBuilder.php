<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Common\ApiVersions;

class FetchOrderBuilder implements HttpRequestBuilderInterface
{
    public function __construct(
        private readonly string $reference
    ) {}

    public function getXml(): ?string
    {
        return null;
    }

    public function getHeaders(): array
    {
        return [
            'Accept: application/vnd.bpost.shm-order-' . ApiVersions::V3_5 . '+XML',
        ];
    }

    public function getUrl(): string
    {
        return '/orders/' . $this->reference;
    }

    public function isExpectXml(): bool
    {
        return true;
    }

    public function getMethod(): string
    {
        return self::METHOD_GET;
    }
}