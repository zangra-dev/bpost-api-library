<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Bpost\Order\Box;
use Bpost\BpostApiClient\Common\ApiVersions;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use DOMDocument;

class ModifyOrder implements HttpRequestBuilderInterface
{
    /**
     * @throws BpostInvalidValueException
     */
    public function __construct(
        private readonly string $reference,
        string $status
    ) {
        $normalized = strtoupper($status);
        if (!in_array($normalized, Box::getPossibleStatusValues(), true)) {
            throw new BpostInvalidValueException('status', $normalized, Box::getPossibleStatusValues());
        }
        $this->status = $normalized;
    }
    private string $status;

    public function getXml(): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $orderUpdate = $document->createElement('orderUpdate');
        $orderUpdate->setAttribute('xmlns', 'http://schema.post.be/shm/deepintegration/v3/');
        $orderUpdate->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $orderUpdate->appendChild($document->createElement('status', $this->status));

        $document->appendChild($orderUpdate);

        return $document->saveXML() ?: '';
    }


    public function getHeaders(): array
    {
        return [
            'Content-Type: application/vnd.bpost.shm-orderUpdate-' . ApiVersions::V3 . '+XML',
        ];
    }

    public function getUrl(): string
    {
        return '/orders/' . $this->reference;
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