<?php

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

class CreateLabelForBox extends CreateLabel
{
    protected function getUrlPrefix()
    {
        return 'boxes';
    }
}
