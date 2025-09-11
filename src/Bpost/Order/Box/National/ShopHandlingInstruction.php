<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Common\BasicAttribute;

class ShopHandlingInstruction extends BasicAttribute
{
    public function validate(): void
    {
        $this->validateLength(50);
    }

    protected function getDefaultKey(): string
    {
        return 'shopHandlingInstruction';
    }
}
