<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Geo6;

use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidXmlResponseException;
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
class Poi
{
    private ?string $id        = null;
    private ?string $type      = null;
    private ?string $office    = null;
    private ?string $street    = null;
    private ?string $nr        = null;
    private ?string $zip       = null;
    private ?string $city      = null;
    private ?int    $x         = null;
    private ?int    $y         = null;
    private ?float  $latitude  = null;
    private ?float  $longitude = null;

    /** @var Service[] */
    private array $services = [];

    /** @var array<int, Day> Indexed by Day::DAY_INDEX_* */
    private array $hours = [];

    private ?string $closedFrom = null;
    private ?string $closedTo   = null;
    private ?string $note       = null;
    private ?string $page       = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(?string $office): void
    {
        $this->office = $office;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getNr(): ?string
    {
        return $this->nr;
    }

    public function setNr(?string $nr): void
    {
        $this->nr = $nr;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): void
    {
        $this->x = $x;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): void
    {
        $this->y = $y;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function addService(Service $service): void
    {
        $this->services[] = $service;
    }

    public function setServices(array $services): void
    {
        $this->services = $services;
    }

    public function getHours(): array
    {
        return $this->hours;
    }

    public function addHour(int $index, Day $day): void
    {
        $this->hours[$index] = $day;
    }

    public function setHours(array $hours): void
    {
        $this->hours = $hours;
    }

    public function getClosedFrom(): ?string
    {
        return $this->closedFrom;
    }

    public function setClosedFrom(?string $closedFrom): void
    {
        $this->closedFrom = $closedFrom;
    }

    public function getClosedTo(): ?string
    {
        return $this->closedTo;
    }

    public function setClosedTo(?string $closedTo): void
    {
        $this->closedTo = $closedTo;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): void
    {
        $this->page = $page;
    }

    public static function createFromXML(SimpleXMLElement $xml): self
    {
        if (!isset($xml->Record)) {
            throw new BpostInvalidXmlResponseException('"Record" missing');
        }

        $recordXml = $xml->Record;
        $poi = new self();

        // Identifiants / type / nom
        if (isset($recordXml->Id) && (string)$recordXml->Id !== '')   { $poi->setId((string)$recordXml->Id); }
        if (isset($recordXml->ID) && (string)$recordXml->ID !== '')   { $poi->setId((string)$recordXml->ID); }

        if (isset($recordXml->Type) && (string)$recordXml->Type !== '') { $poi->setType((string)$recordXml->Type); }

        if (isset($recordXml->Name) && (string)$recordXml->Name !== '')   { $poi->setOffice((string)$recordXml->Name); }
        if (isset($recordXml->OFFICE) && (string)$recordXml->OFFICE !== '') { $poi->setOffice((string)$recordXml->OFFICE); }

        // Adresse
        if (isset($recordXml->Street) && (string)$recordXml->Street !== '') { $poi->setStreet((string)$recordXml->Street); }
        if (isset($recordXml->STREET) && (string)$recordXml->STREET !== '') { $poi->setStreet((string)$recordXml->STREET); }

        if (isset($recordXml->Number) && (string)$recordXml->Number !== '') { $poi->setNr((string)$recordXml->Number); }
        if (isset($recordXml->NR) && (string)$recordXml->NR !== '')         { $poi->setNr((string)$recordXml->NR); }

        if (isset($recordXml->Zip) && (string)$recordXml->Zip !== '')       { $poi->setZip((string)$recordXml->Zip); }
        if (isset($recordXml->ZIP) && (string)$recordXml->ZIP !== '')       { $poi->setZip((string)$recordXml->ZIP); }

        if (isset($recordXml->City) && (string)$recordXml->City !== '')     { $poi->setCity((string)$recordXml->City); }
        if (isset($recordXml->CITY) && (string)$recordXml->CITY !== '')     { $poi->setCity((string)$recordXml->CITY); }

        // Coordonnées
        if (isset($recordXml->X) && (string)$recordXml->X !== '')           { $poi->setX((int)$recordXml->X); }
        if (isset($recordXml->Y) && (string)$recordXml->Y !== '')           { $poi->setY((int)$recordXml->Y); }
        if (isset($recordXml->Longitude) && (string)$recordXml->Longitude !== '') { $poi->setLongitude((float)$recordXml->Longitude); }
        if (isset($recordXml->Latitude) && (string)$recordXml->Latitude !== '')   { $poi->setLatitude((float)$recordXml->Latitude); }

        // Services
        if (isset($recordXml->Services) && isset($recordXml->Services->Service)) {
            foreach ($recordXml->Services->Service as $serviceXml) {
                $poi->addService(Service::createFromXML($serviceXml));
            }
        }

        if (isset($recordXml->Hours)) {
            $hours = $recordXml->Hours;

            if (isset($hours->Monday))    { $poi->addHour(Day::DAY_INDEX_MONDAY,    Day::createFromXML($hours->Monday)); }
            if (isset($hours->Tuesday))   { $poi->addHour(Day::DAY_INDEX_TUESDAY,   Day::createFromXML($hours->Tuesday)); }
            if (isset($hours->Wednesday)) { $poi->addHour(Day::DAY_INDEX_WEDNESDAY, Day::createFromXML($hours->Wednesday)); }
            if (isset($hours->Thursday))  { $poi->addHour(Day::DAY_INDEX_THURSDAY,  Day::createFromXML($hours->Thursday)); }
            if (isset($hours->Friday))    { $poi->addHour(Day::DAY_INDEX_FRIDAY,    Day::createFromXML($hours->Friday)); }
            if (isset($hours->Saturday))  { $poi->addHour(Day::DAY_INDEX_SATURDAY,  Day::createFromXML($hours->Saturday)); }
            if (isset($hours->Sunday))    { $poi->addHour(Day::DAY_INDEX_SUNDAY,    Day::createFromXML($hours->Sunday)); }
        }

        if (isset($recordXml->ClosedFrom) && (string)$recordXml->ClosedFrom !== '') { $poi->setClosedFrom((string)$recordXml->ClosedFrom); }
        if (isset($recordXml->ClosedTo)   && (string)$recordXml->ClosedTo   !== '') { $poi->setClosedTo((string)$recordXml->ClosedTo); }

        // Note
        if (isset($recordXml->NOTE) && (string)$recordXml->NOTE !== '') { $poi->setNote((string)$recordXml->NOTE); }

        // Lien page
        if (isset($xml->Page) && isset($xml->Page['ServiceRef']) && (string)$xml->Page['ServiceRef'] !== '') {
            $poi->setPage((string)$xml->Page['ServiceRef']);
        }

        return $poi;
    }
}