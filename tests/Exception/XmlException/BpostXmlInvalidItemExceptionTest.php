<?php

namespace Tests\Exception\XmlException;

use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;
use PHPUnit_Framework_TestCase;

class BpostXmlInvalidItemExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostXmlInvalidItemException();
        $this->assertSame('Invalid item', $ex->getMessage());

        $ex = new BpostXmlInvalidItemException('Oops');
        $this->assertSame('Invalid item: Oops', $ex->getMessage());
    }
}
