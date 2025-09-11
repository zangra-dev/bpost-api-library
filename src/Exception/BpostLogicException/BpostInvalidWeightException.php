<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Exception\BpostLogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Exception;

/**
 * Class BpostInvalidWeightException
 */
class BpostInvalidWeightException extends BpostLogicException
{
    public function __construct(int $invalidWeight, int $maximumWeight, int $code = 0, ?Exception $previous = null)
    {
        $message = sprintf(
            'Invalid weight (%d g), maximum is %d g.',
            $invalidWeight,
            $maximumWeight * 1000
        );
        parent::__construct($message, $code, $previous);
    }
}
