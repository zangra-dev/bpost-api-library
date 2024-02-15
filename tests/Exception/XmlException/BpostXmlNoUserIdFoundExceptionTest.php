<?php

namespace Tests\Exception\XmlException;

use Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoUserIdFoundException;
use PHPUnit_Framework_TestCase;

class BpostXmlNoUserIdFoundExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $ex = new BpostXmlNoUserIdFoundException();
        $this->assertSame('No UserId found', $ex->getMessage());

        $ex = new BpostXmlNoUserIdFoundException('Oops');
        $this->assertSame('No UserId found: Oops', $ex->getMessage());
    }
}
