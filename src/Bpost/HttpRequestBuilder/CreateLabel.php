<?php

namespace Bpost\BpostApiClient\Bpost\HttpRequestBuilder;

use Bpost\BpostApiClient\Common\ValidatedValue\LabelFormat;

abstract class CreateLabel implements HttpRequestBuilderInterface
{
    /**
     * @var string
     */
    protected $reference;
    /**
     * @var LabelFormat
     */
    protected $labelFormat;
    /**
     * @var bool
     */
    protected $asPdf;
    /**
     * @var bool
     */
    protected $withReturnLabels;

    /**
     * @param string      $reference
     * @param LabelFormat $labelFormat
     * @param bool        $asPdf
     * @param bool        $withReturnLabels
     */
    public function __construct($reference, LabelFormat $labelFormat, $asPdf, $withReturnLabels)
    {
        $this->reference = $reference;
        $this->labelFormat = $labelFormat;
        $this->asPdf = $asPdf;
        $this->withReturnLabels = $withReturnLabels;
    }

    /**
     * @return string
     */
    abstract protected function getUrlPrefix();

    /**
     * @return null
     */
    public function getXml()
    {
        return null;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return array(
            'Accept: application/vnd.bpost.shm-label-' . ($this->asPdf ? 'pdf' : 'image') . '-v3.3+XML',
            'Content-Type: application/vnd.bpost.shm-labelRequest-v3.3+XML',
        );
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return sprintf(
            '/%s/%s/labels/%s%s',
            $this->getUrlPrefix(),
            $this->reference,
            $this->labelFormat->getValue(),
            $this->withReturnLabels ? '/withReturnLabels' : ''
        );
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return self::METHOD_GET;
    }

    /**
     * @return bool
     */
    public function isExpectXml()
    {
        return true;
    }
}
