<?php

namespace Bpost\BpostApiClient\Exception\XmlException;

use Bpost\BpostApiClient\Exception\BpostXmlException;
use Exception;

/**
 * Class BpostXmlNoUserIdFoundException
 */
class BpostXmlNoUserIdFoundException extends BpostXmlException
{
    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = 'No UserId found' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
