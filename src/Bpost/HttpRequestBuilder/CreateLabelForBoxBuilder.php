<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

class CreateLabelForBoxBuilder extends CreateLabelBuilder
{
    protected function getUrlPrefix(): string
    {
        return 'boxes';
    }
}
