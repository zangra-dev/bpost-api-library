<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\Option;

use DOMDocument;
use DOMElement;

/**
 * bPost Option class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
abstract class Option
{
    abstract public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement;
}
