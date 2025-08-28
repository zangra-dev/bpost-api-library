<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Bpost\Order;
use Bpost\BpostApiClient\Common\ApiVersions;
use DOMDocument;

class CreateOrReplaceOrderBuilder implements HttpRequestBuilderInterface
{
    public function __construct(
        private readonly Order $order,
        private readonly string $accountId,
    ) {}

    public function getXml(): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->appendChild(
            $this->order->toXML($document, $this->accountId)
        );

        return $document->saveXML() ?: '';
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type: application/vnd.bpost.shm-order-' . ApiVersions::V5 . '+XML',
        ];
    }

    public function getUrl(): string
    {
        return '/orders';
    }

    public function isExpectXml(): bool
    {
        return false;
    }

    public function getMethod(): string
    {
        return self::METHOD_POST;
    }
}
