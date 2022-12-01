<?php

namespace Tests\Exception\BpostLogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidPatternException;
use PHPUnit_Framework_TestCase;

class BpostInvalidPatternExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidPatternException('error', 'OOPS', '([A-Z]{3})');
        $this->assertSame('Invalid value (OOPS) for entry "error", pattern is: "([A-Z]{3})".', $ex->getMessage());
    }
}
