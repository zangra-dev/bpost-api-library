<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common;

use Bpost\BpostApiClient\Exception\BpostLogicException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

abstract class BasicAttribute
{
    private mixed $value;
    private string $key;

    public function __construct(mixed $value, string $key = '')
    {
        $this->value = $value;
        $this->setKey($key);
        $this->validate();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    private function setKey(string $key): void
    {
        $this->key = $key !== '' ? $key : $this->getDefaultKey();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }

    /**
     * @throws BpostInvalidLengthException
     */
    public function validateLength(int $length): void
    {
        if (mb_strlen((string) $this->getValue()) > $length) {
            throw new BpostInvalidLengthException($this->getKey(), mb_strlen((string) $this->getValue()), $length);
        }
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function validateChoice(array $allowedValues): void
    {
        if (!in_array($this->getValue(), $allowedValues, true)) {
            throw new BpostInvalidValueException($this->getKey(), $this->getValue(), $allowedValues);
        }
    }

    /**
     * @throws BpostInvalidPatternException
     */
    public function validatePattern(string $regexPattern): void
    {
        if (!preg_match("/^$regexPattern$/", (string) $this->getValue())) {
            throw new BpostInvalidPatternException($this->getKey(), (string) $this->getValue(), $regexPattern);
        }
    }

    abstract protected function getDefaultKey(): string;

    /**
     * Each class must validate data
     * and throw BpostLogicException if crash
     * @throws BpostLogicException
     */
    abstract public function validate(): void;
}
