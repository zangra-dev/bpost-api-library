<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\BpostLogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Exception;

/**
 * Class BpostInvalidValueException
 */
class BpostInvalidValueException extends BpostLogicException
{
    public function __construct(string $key, string $invalidValue, array $allowedValues, int $code = 0, ?Exception $previous = null)
    {
        $message = sprintf(
            'Invalid value (%s) for %s, possible values are: %s.',
            $invalidValue,
            $key,
            implode(', ', $allowedValues)
        );
        parent::__construct($message, $code, $previous);
    }
}