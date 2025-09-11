<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

class CreateLabelForOrderBuilder extends CreateLabelBuilder
{
    protected function getUrlPrefix(): string
    {
        return 'orders';
    }
}
