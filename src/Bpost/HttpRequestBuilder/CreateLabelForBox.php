<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

class CreateLabelForBox extends CreateLabel
{
    protected function getUrlPrefix()
    {
        return 'boxes';
    }
}
