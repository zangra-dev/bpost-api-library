<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception;

use Bpost\BpostApiClient\BpostException;
use Exception;

/**
 * Class BpostNotImplementedException
 */
class BpostNotImplementedException extends BpostException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $message = 'Not implemented' . (empty($message) ? '' : ': ' . $message);
        parent::__construct($message, $code, $previous);
    }
}
