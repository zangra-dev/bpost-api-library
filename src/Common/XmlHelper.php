<?php

namespace Bpost\BpostApiClient\Common;

use Bpost\BpostApiClient\Exception\BpostNotImplementedException;

class XmlHelper
{
    /**
     * Prefix $tagName with the $prefix, if needed
     *
     * @param string $prefix
     * @param string $tagName
     *
     * @return string
     */
    public static function getPrefixedTagName($tagName, $prefix = null)
    {
        if (empty($prefix)) {
            return $tagName;
        }

        return $prefix . ':' . $tagName;
    }

    /**
     * @param string $className
     *
     * @throws BpostNotImplementedException
     */
    public static function assertMethodCreateFromXmlExists($className)
    {
        if (!method_exists($className, 'createFromXML')) {
            throw new BpostNotImplementedException('Method createFromXML not found for class ' . $className);
        }
    }
}
