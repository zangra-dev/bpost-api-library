<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common\BasicAttribute;

use Bpost\BpostApiClient\Common\BasicAttribute;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;

class EmailAddressCharacteristic extends BasicAttribute
{
    /**
     * @throws BpostInvalidLengthException
     * @throws BpostInvalidPatternException
     */
    public function validate(): void
    {
        $this->validateLength(40);
        $this->validatePattern('([a-zA-Z0-9_\.\-+])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+');
    }

    protected function getDefaultKey(): string
    {
        return 'emailAddress';
    }
}
