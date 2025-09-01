<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\ProductConfiguration;

/**
 * Class Visibility
 */
class Visibility
{
    public const VISIBLE                          = 'VISIBLE';
    public const NOT_VISIBLE_BY_CONSUMER_OPTIONAL = 'NOT_VISIBLE_BY_CONSUMER_OPTIONAL';
    public const NOT_VISIBLE_BY_CONSUMER_DEFAULT  = 'NOT_VISIBLE_BY_CONSUMER_DEFAULT';
    public const VISIBLE_BY_CONSUMER_AND_MANDATORY= 'VISIBLE_BY_CONSUMER_AND_MANDATORY';
    public const DELIVERY_METHOD_VISIBILITY_VISIBLE     = 'VISIBLE';
    public const DELIVERY_METHOD_VISIBILITY_GREYED_OUT  = 'GREYED_OUT';
    public const DELIVERY_METHOD_VISIBILITY_INVISIBLE   = 'INVISIBLE';
}
