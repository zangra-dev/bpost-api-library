<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * Class ValidatedValue
 */
abstract class ValidatedValue
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     * @throws BpostLogicException
     */
    public function __construct(mixed $value)
    {
        $this->setValue($value);
        $this->validate();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->getValue();
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function validateLength(int $length): void
    {
        if (mb_strlen((string)$this->getValue()) > $length) {
            throw new BpostInvalidLengthException('', mb_strlen((string)$this->getValue()), $length);
        }
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function validateChoice(array $allowedValues): void
    {
        if (!in_array($this->getValue(), $allowedValues, true)) {
            throw new BpostInvalidValueException('', $this->getValue(), $allowedValues);
        }
    }

    /**
     * @throws BpostInvalidPatternException
     */
    public function validatePattern(string $regexPattern): void
    {
        if (!preg_match("/^$regexPattern\$/", (string)$this->getValue())) {
            throw new BpostInvalidPatternException('', $this->getValue(), $regexPattern);
        }
    }

    /**
     * Each child class must implement its own validation logic.
     *
     * @throws BpostLogicException
     */
    abstract public function validate(): void;
}