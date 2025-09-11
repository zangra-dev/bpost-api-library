<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Common\ApiVersions;
use Bpost\BpostApiClient\Common\ValidatedValue\LabelFormat;
use DOMDocument;
use DOMException;

class CreateLabelInBulkForOrdersBuilder implements HttpRequestBuilderInterface
{
    public function __construct(
        private readonly array $references,
        private readonly LabelFormat $labelFormat,
        private readonly bool $asPdf,
        private readonly bool $withReturnLabels,
        private readonly bool $forcePrinting,
    ) {}

    public function getXml(): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $batchLabels = $document->createElement('batchLabels');
        $batchLabels->setAttribute('xmlns', 'http://schema.post.be/shm/deepintegration/v3/');

        foreach ($this->references as $reference) {
            $batchLabels->appendChild($document->createElement('order', (string) $reference));
        }

        $document->appendChild($batchLabels);

        return $document->saveXML() ?: '';
    }

    public function getHeaders(): array
    {
        $media = $this->asPdf ? 'pdf' : 'image';

        return [
            'Accept: application/vnd.bpost.shm-label-' . $media . '-' . ApiVersions::V3_4 . '+XML',
            'Content-Type: application/vnd.bpost.shm-labelRequest-' . ApiVersions::V3 . '+XML',
        ];
    }

    public function getUrl(): string
    {
        $url = '/labels/' . $this->labelFormat->getValue();

        if ($this->withReturnLabels) {
            $url .= '/withReturnLabels';
        }
        if ($this->forcePrinting) {
            $url .= '?forcePrinting=true';
        }

        return $url;
    }

    public function isExpectXml(): bool
    {
        return true;
    }

    public function getMethod(): string
    {
        return self::METHOD_POST;
    }
}