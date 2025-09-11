<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order;

use SimpleXMLElement;

/**
 * bPost Sender class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Sender extends Customer
{
    public const TAG_NAME = 'sender';

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        /** @var self $sender */
        $sender = parent::createFromXMLHelper($xml, new self());
        return $sender;
    }
}
