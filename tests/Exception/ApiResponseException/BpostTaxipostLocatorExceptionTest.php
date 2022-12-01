<?php

namespace Tests\Exception\BpostApiResponseException;

use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostTaxipostLocatorException;
use PHPUnit_Framework_TestCase;

class BpostTaxipostLocatorExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostTaxipostLocatorException('Oops');
        $this->assertSame('Oops', $ex->getMessage());

        $ex = new BpostTaxipostLocatorException('');
        $this->assertSame('', $ex->getMessage());
    }
}
