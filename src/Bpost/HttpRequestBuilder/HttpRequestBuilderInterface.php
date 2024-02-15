<?php

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

interface HttpRequestBuilderInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    public function getHeaders();

    public function getUrl();

    public function getXml();

    public function isExpectXml();

    public function getMethod();
}
