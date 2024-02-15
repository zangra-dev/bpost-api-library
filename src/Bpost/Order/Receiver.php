<?php

namespace Bpost\BpostApiClient\Bpost\Order;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use SimpleXMLElement;

/**
 * bPost Receiver class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Receiver extends Customer
{
    const TAG_NAME = 'receiver';

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Receiver
     *
     * @throws BpostInvalidLengthException
     */
    public static function createFromXML(SimpleXMLElement $xml)
    {
        /** @var Receiver $receiver */
        $receiver = parent::createFromXMLHelper($xml, new Receiver());

        return $receiver;
    }
}
