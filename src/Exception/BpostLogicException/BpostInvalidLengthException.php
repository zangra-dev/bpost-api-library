<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\BpostLogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Exception;

/**
 * Class BpostInvalidLengthException
 */
class BpostInvalidLengthException extends BpostLogicException
{
    public function __construct(string $nameEntry, int $invalidLength, int $maximumLength, int $code = 0, ?Exception $previous = null)
    {
        $message = sprintf(
            'Invalid length for entry "%s" (%d characters), maximum is %d.',
            $nameEntry,
            $invalidLength,
            $maximumLength
        );
        parent::__construct($message, $code, $previous);
    }
}