<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common\BasicAttribute;

use Bpost\BpostApiClient\Common\BasicAttribute;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;

class PhoneNumber extends BasicAttribute
{
    /**
     * @throws BpostInvalidLengthException
     */
    public function validate(): void
    {
        $this->validateLength(20);
    }

    protected function getDefaultKey(): string
    {
        return 'phoneNumber';
    }
}
