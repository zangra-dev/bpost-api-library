<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Common\BasicAttribute;

class ShopHandlingInstruction extends BasicAttribute
{
    public function validate()
    {
        $this->validateLength(50);
    }

    /**
     * @return string
     */
    protected function getDefaultKey()
    {
        return 'shopHandlingInstruction';
    }
}
