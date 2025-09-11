<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Common\ApiVersions;
use Bpost\BpostApiClient\Common\ValidatedValue\LabelFormat;

abstract class CreateLabel implements HttpRequestBuilderInterface
{
    public function __construct(
        protected readonly string $reference,
        protected readonly LabelFormat $labelFormat,
        protected readonly bool $asPdf,
        protected readonly bool $withReturnLabels
    ) {}

    /**
     * @return string
     */
    abstract protected function getUrlPrefix(): string;

    public function getXml(): ?string
    {
        return null;
    }

    public function getHeaders(): array
    {
        $acceptSuffix = $this->asPdf ? 'pdf' : 'image';

        return [
            'Accept: application/vnd.bpost.shm-label-' . $acceptSuffix . '-' . ApiVersions::V3_3 . '+XML',
            'Content-Type: application/vnd.bpost.shm-labelRequest-' . ApiVersions::V3_3 . '+XML',
        ];
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return sprintf(
            '/%s/%s/labels/%s%s',
            $this->getUrlPrefix(),
            $this->reference,
            $this->labelFormat->getValue(),
            $this->withReturnLabels ? '/withReturnLabels' : ''
        );
    }

    public function getMethod(): string
    {
        return self::METHOD_GET;
    }

    public function isExpectXml(): bool
    {
        return true;
    }
}