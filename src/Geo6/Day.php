<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Geo6;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidDayException;
use SimpleXMLElement;

/**
 * Geo6 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
final class Day
{
    public const DAY_INDEX_MONDAY    = 1;
    public const DAY_INDEX_TUESDAY   = 2;
    public const DAY_INDEX_WEDNESDAY = 3;
    public const DAY_INDEX_THURSDAY  = 4;
    public const DAY_INDEX_FRIDAY    = 5;
    public const DAY_INDEX_SATURDAY  = 6;
    public const DAY_INDEX_SUNDAY    = 7;

    public const DAY_NAME_MONDAY    = 'Monday';
    public const DAY_NAME_TUESDAY   = 'Tuesday';
    public const DAY_NAME_WEDNESDAY = 'Wednesday';
    public const DAY_NAME_THURSDAY  = 'Thursday';
    public const DAY_NAME_FRIDAY    = 'Friday';
    public const DAY_NAME_SATURDAY  = 'Saturday';
    public const DAY_NAME_SUNDAY    = 'Sunday';

    private const DAY_MAP = [
        self::DAY_NAME_MONDAY    => self::DAY_INDEX_MONDAY,
        self::DAY_NAME_TUESDAY   => self::DAY_INDEX_TUESDAY,
        self::DAY_NAME_WEDNESDAY => self::DAY_INDEX_WEDNESDAY,
        self::DAY_NAME_THURSDAY  => self::DAY_INDEX_THURSDAY,
        self::DAY_NAME_FRIDAY    => self::DAY_INDEX_FRIDAY,
        self::DAY_NAME_SATURDAY  => self::DAY_INDEX_SATURDAY,
        self::DAY_NAME_SUNDAY    => self::DAY_INDEX_SUNDAY,
    ];

    private ?string $amOpen  = null;
    private ?string $amClose = null;
    private ?string $pmOpen  = null;
    private ?string $pmClose = null;
    private string $day = self::DAY_NAME_MONDAY;

    public function setAmClose(string $amClose): void
    {
        $this->amClose = $amClose;
    }

    public function getAmClose(): ?string
    {
        return $this->amClose;
    }

    public function setAmOpen(string $amOpen): void
    {
        $this->amOpen = $amOpen;
    }

    public function getAmOpen(): ?string
    {
        return $this->amOpen;
    }

    public function setDay(string $day): void
    {
        $normalized = ucfirst(strtolower($day));
        $this->day = $normalized;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    /**
     * @throws BpostInvalidDayException
     */
    public function getDayIndex(): int
    {
        if (isset(self::DAY_MAP[$this->day])) {
            return self::DAY_MAP[$this->day];
        }

        throw new BpostInvalidDayException($this->day, array_keys(self::DAY_MAP));
    }

    public function setPmClose(string $pmClose): void
    {
        $this->pmClose = $pmClose;
    }

    public function getPmClose(): ?string
    {
        return $this->pmClose;
    }

    public function setPmOpen(string $pmOpen): void
    {
        $this->pmOpen = $pmOpen;
    }

    public function getPmOpen(): ?string
    {
        return $this->pmOpen;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $day = new self();
        $day->setDay($xml->getName());

        if (isset($xml->AMOpen) && (string) $xml->AMOpen !== '') {
            $day->setAmOpen((string) $xml->AMOpen);
        }
        if (isset($xml->AMClose) && (string) $xml->AMClose !== '') {
            $day->setAmClose((string) $xml->AMClose);
        }
        if (isset($xml->PMOpen) && (string) $xml->PMOpen !== '') {
            $day->setPmOpen((string) $xml->PMOpen);
        }
        if (isset($xml->PMClose) && (string) $xml->PMClose !== '') {
            $day->setPmClose((string) $xml->PMClose);
        }

        return $day;
    }
}

