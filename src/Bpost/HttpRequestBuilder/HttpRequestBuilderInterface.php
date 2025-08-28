<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

interface HttpRequestBuilderInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public function getHeaders(): array;

    public function getUrl(): string;

    public function getXml(): ?string;

    public function isExpectXml(): bool;

    public function getMethod(): string;
}
