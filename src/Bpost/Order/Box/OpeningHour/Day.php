<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\OpeningHour;

use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use DOMDocument;
use DOMElement;

/**
 * bPost Day class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Day
{
    public const DAY_MONDAY    = 'Monday';
    public const DAY_TUESDAY   = 'Tuesday';
    public const DAY_WEDNESDAY = 'Wednesday';
    public const DAY_THURSDAY  = 'Thursday';
    public const DAY_FRIDAY    = 'Friday';
    public const DAY_SATURDAY  = 'Saturday';
    public const DAY_SUNDAY    = 'Sunday';

    private string $day;
    private string $value;

    /**
     * @throws BpostInvalidValueException
     */
    public function __construct(string $day, string $value)
    {
        $this->setDay($day);
        $this->setValue($value);
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setDay(string $day): void
    {
        if (!in_array($day, self::getPossibleDayValues(), true)) {
            throw new BpostInvalidValueException('day', $day, self::getPossibleDayValues());
        }
        $this->day = $day;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public static function getPossibleDayValues(): array
    {
        return [
            self::DAY_MONDAY,
            self::DAY_TUESDAY,
            self::DAY_WEDNESDAY,
            self::DAY_THURSDAY,
            self::DAY_FRIDAY,
            self::DAY_SATURDAY,
            self::DAY_SUNDAY,
        ];
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = null): DOMElement
    {
        return $document->createElement(
            XmlHelper::getPrefixedTagName($this->getDay(), $prefix),
            $this->getValue()
        );
    }
}