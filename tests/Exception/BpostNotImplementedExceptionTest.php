<?php

namespace Tests\Exception;

use Bpost\BpostApiClient\Exception\BpostNotImplementedException;

class BpostNotImplementedExceptionTest extends \PHPUnit_Framework_TestCase

{
    public function testGetMessage()
    {
        $ex = new BpostNotImplementedException();
        $this->assertContains('Not implemented', $ex->getMessage());
    }
}
