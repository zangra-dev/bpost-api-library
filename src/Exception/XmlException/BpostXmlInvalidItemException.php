<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\XmlException;

use Bpost\BpostApiClient\Exception\BpostXmlException;
use Exception;

/**
 * Class BpostXmlInvalidItemException
 */
class BpostXmlInvalidItemException extends BpostXmlException
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        $message = 'Invalid item' . ($message !== '' ? ': ' . $message : '');
        parent::__construct($message, $code, $previous);
    }
}
