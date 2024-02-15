<?php

namespace Tests\Bpost\Order\Box\National;

use Bpost\BpostApiClient\Bpost\Order\Box\National\ShopHandlingInstruction;
use Bpost\BpostApiClient\BpostException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use PHPUnit_Framework_TestCase;

class ShopHandlingInstructionTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $value = str_repeat('a', 50);
        try {
            $test = new ShopHandlingInstruction($value);
            $this->assertEquals($value, $test->getValue());
        } catch (BpostException $ex) {
            $this->fail('Exception launched for valid value: "' . $value . '"');
        }

        $value = str_repeat('a', 51);
        try {
            new ShopHandlingInstruction($value);
            $this->fail('Exception uncaught for invalid value: "' . $value . '"');
        } catch (BpostInvalidLengthException $ex) {
            $this->assertTrue(true);
        }
    }
}
