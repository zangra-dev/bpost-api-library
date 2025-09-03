<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\BpostLogicException;

use Exception;

/**
 * Class BpostInvalidDayException
 */
class BpostInvalidDayException extends BpostInvalidValueException
{
    public function __construct(string $invalidValue, array $allowedValues, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct('day', $invalidValue, $allowedValues, $code, $previous);
    }
}
