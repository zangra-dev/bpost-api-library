<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\XmlException;

use Bpost\BpostApiClient\Exception\BpostXmlException;
use Exception;

/**
 * Class BpostXmlNoUserIdFoundException
 */
class BpostXmlNoUserIdFoundException extends BpostXmlException
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        $message = 'No UserId found' . ($message !== '' ? ': ' . $message : '');
        parent::__construct($message, $code, $previous);
    }
}
