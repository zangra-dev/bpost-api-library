<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Common\ApiVersions;

class FetchProductConfig implements HttpRequestBuilderInterface
{
    public function getXml(): ?string
    {
        return null;
    }


    public function getHeaders(): array
    {
        return [
            'Accept: application/vnd.bpost.shm-productConfiguration-' . ApiVersions::V3_1 . '+XML',
        ];
    }

    public function getUrl(): string
    {
        return '/productconfig';
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
