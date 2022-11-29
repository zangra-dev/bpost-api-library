<?php

namespace Bpost\BpostApiClient\Common;

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
}
