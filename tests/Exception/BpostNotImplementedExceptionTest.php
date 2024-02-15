<?php

namespace Tests\Exception;

use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use PHPUnit_Framework_TestCase;

class BpostNotImplementedExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostNotImplementedException();
        $this->assertContains('Not implemented', $ex->getMessage());
    }
}
