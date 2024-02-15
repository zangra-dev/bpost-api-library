<?php

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

class CreateLabelForOrder extends CreateLabel
{
    protected function getUrlPrefix()
    {
        return 'orders';
    }
}
