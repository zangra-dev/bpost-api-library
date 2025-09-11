<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\BpostApiResponseException;

use Bpost\BpostApiClient\Exception\BpostApiResponseException;
use Exception;

/**
 * Class BpostInvalidXmlResponseException
 */
class BpostInvalidXmlResponseException extends BpostApiResponseException
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        $message = 'Invalid XML-response' . ($message === '' ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}