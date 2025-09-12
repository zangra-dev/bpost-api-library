<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use SimpleXMLElement;

/**
 * Class Labels
 */
class Labels
{
    /**
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): array
    {
        $labels = [];

        if (isset($xml->label)) {
            foreach ($xml->label as $labelXml) {
                if ($labelXml instanceof \SimpleXMLElement) {
                    $labels[] = Label::createFromXML($labelXml);
                }
            }
        }

        return $labels;
    }
}