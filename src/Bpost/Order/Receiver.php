<?php
declare(strict_types=1);

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
    public const TAG_NAME = 'receiver';

    /**
     * @throws BpostInvalidLengthException
     */
    public static function createFromXML(SimpleXMLElement $xml): self
    {
        /** @var self $receiver */
        $receiver = parent::createFromXMLHelper($xml, new self());
        return $receiver;
    }
}