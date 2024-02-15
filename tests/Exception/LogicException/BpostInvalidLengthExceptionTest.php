<?php

namespace Tests\Exception\LogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use PHPUnit_Framework_TestCase;

class BpostInvalidLengthExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidLengthException('streetName', 41, 40);
        $this->assertSame('Invalid length for entry "streetName" (41 characters), maximum is 40.', $ex->getMessage());
    }
}
