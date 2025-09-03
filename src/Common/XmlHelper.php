<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Common;

use Bpost\BpostApiClient\Exception\BpostNotImplementedException;

class XmlHelper
{
    /**
     * Prefix $tagName with the $prefix, if needed.
     */
    public static function getPrefixedTagName(string $tagName, ?string $prefix = null): string
    {
        return empty($prefix) ? $tagName : $prefix . ':' . $tagName;
    }

    /**
     * Ensure the given class has a createFromXML method.
     *
     * @throws BpostNotImplementedException
     */
    public static function assertMethodCreateFromXmlExists(string $className): void
    {
        if (!method_exists($className, 'createFromXML')) {
            throw new BpostNotImplementedException(
                sprintf('Method createFromXML not found for class %s', $className)
            );
        }
    }
}