<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

class CreateLabelForOrder extends CreateLabel
{
    protected function getUrlPrefix(): string
    {
        return 'orders';
    }
}
