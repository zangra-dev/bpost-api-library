<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common\ValidatedValue;

use Bpost\BpostApiClient\Common\ValidatedValue;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * Class LabelFormat
 */
class LabelFormat extends ValidatedValue
{
    public const FORMAT_A4 = 'A4';
    public const FORMAT_A6 = 'A6';

    public function setValue($value): void
    {
        parent::setValue(strtoupper((string) $value));
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function validate(): void
    {
        $this->validateChoice([
            self::FORMAT_A4,
            self::FORMAT_A6,
        ]);
    }
}