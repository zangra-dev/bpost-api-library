<?php

namespace Tests\Exception\LogicException;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidDayException;
use PHPUnit_Framework_TestCase;

class BpostInvalidDayExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostInvalidDayException('unicorn', array('Monday', 'Tuesday'));
        $this->assertSame('Invalid value (unicorn) for day, possible values are: Monday, Tuesday.', $ex->getMessage());
    }
}
