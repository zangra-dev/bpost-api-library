<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\BpostLogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Exception;

/**
 * Class BpostInvalidValueException
 */
class BpostInvalidPatternException extends BpostLogicException
{
    public function __construct(string $key, string $invalidValue, string $regexPattern, int $code = 0, ?Exception $previous = null)
    {
        $message = sprintf(
            'Invalid value (%s) for entry "%s", pattern is: "%s".',
            $invalidValue,
            $key,
            $regexPattern
        );
        parent::__construct($message, $code, $previous);
    }
}