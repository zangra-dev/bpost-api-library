<?php

namespace Bpost\BpostApiClient\Exception\BpostApiResponseException;

use Bpost\BpostApiClient\Exception\BpostApiResponseException;
use Exception;

/**
 * Class BpostInvalidXmlResponseException
 */
class BpostInvalidXmlResponseException extends BpostApiResponseException
{
    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = 'Invalid XML-response' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
