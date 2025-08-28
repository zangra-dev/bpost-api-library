<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpack247;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoUserIdFoundException;
use DateTime;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * bPost Customer class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Customer
{
    public const CUSTOMER_PREFERRED_LANGUAGE_NL = 'nl-BE';
    public const CUSTOMER_PREFERRED_LANGUAGE_FR = 'fr-BE';
    public const CUSTOMER_PREFERRED_LANGUAGE_EN = 'en-US';

    public const CUSTOMER_TITLE_MR = 'Mr.';
    public const CUSTOMER_TITLE_MS = 'Ms.';

    public function __construct(
        private ?bool $activated = null,
        private ?string $userID = null,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $companyName = null,
        private ?string $street = null,
        private ?string $number = null,
        private ?string $email = null,
        private string $mobilePrefix = '0032',
        private ?string $mobileNumber = null,
        private ?string $postalCode = null,
        private array $packStations = [],
        private ?string $town = null,
        private ?string $preferredLanguage = null,
        private ?string $title = null,
        private ?bool $isComfortZoneUser = null,
        private ?DateTime $dateOfBirth = null,
        private ?string $deliveryCode = null,
        private ?bool $optIn = null,
        private ?bool $receivePromotions = null,
        private ?bool $useInformationForThirdParty = null,
        private ?string $userName = null,
    ) {}


    public function setActivated(?bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setDateOfBirth(?DateTime $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDeliveryCode(?string $deliveryCode): void
    {
        $this->deliveryCode = $deliveryCode;
    }

    public function getDeliveryCode(): ?string
    {
        return $this->deliveryCode;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setIsComfortZoneUser(?bool $isComfortZoneUser): void
    {
        $this->isComfortZoneUser = $isComfortZoneUser;
    }

    public function getIsComfortZoneUser(): ?bool
    {
        return $this->isComfortZoneUser;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setMobileNumber(?string $mobileNumber): void
    {
        $this->mobileNumber = $mobileNumber;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobilePrefix(string $mobilePrefix): void
    {
        $this->mobilePrefix = $mobilePrefix;
    }

    public function getMobilePrefix(): string
    {
        return $this->mobilePrefix;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setOptIn(?bool $optIn): void
    {
        $this->optIn = $optIn;
    }

    public function getOptIn(): ?bool
    {
        return $this->optIn;
    }

    public function addPackStation(CustomerPackStation $packStation): void
    {
        $this->packStations[] = $packStation;
    }

    public function setPackStations(array $packStations): void
    {
        $this->packStations = $packStations;
    }

    public function getPackStations(): array
    {
        return $this->packStations;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setPreferredLanguage(?string $preferredLanguage): void
    {
        if ($preferredLanguage === null) {
            $this->preferredLanguage = null;
            return;
        }
        if (!in_array($preferredLanguage, self::getPossiblePreferredLanguageValues(), true)) {
            throw new BpostInvalidValueException('preferred language', $preferredLanguage, self::getPossiblePreferredLanguageValues());
        }
        $this->preferredLanguage = $preferredLanguage;
    }

    public function getPreferredLanguage(): ?string
    {
        return $this->preferredLanguage;
    }

    public static function getPossiblePreferredLanguageValues(): array
    {
        return [
            self::CUSTOMER_PREFERRED_LANGUAGE_NL,
            self::CUSTOMER_PREFERRED_LANGUAGE_FR,
            self::CUSTOMER_PREFERRED_LANGUAGE_EN,
        ];
    }

    public function setReceivePromotions(?bool $receivePromotions): void
    {
        $this->receivePromotions = $receivePromotions;
    }

    public function getReceivePromotions(): ?bool
    {
        return $this->receivePromotions;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setTitle(?string $title): void
    {
        if ($title === null) {
            $this->title = null;
            return;
        }
        if (!in_array($title, self::getPossibleTitleValues(), true)) {
            throw new BpostInvalidValueException('title', $title, self::getPossibleTitleValues());
        }
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public static function getPossibleTitleValues(): array
    {
        return [
            self::CUSTOMER_TITLE_MR,
            self::CUSTOMER_TITLE_MS,
        ];
    }

    public function setTown(?string $town): void
    {
        $this->town = $town;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setUseInformationForThirdParty(?bool $useInformationForThirdParty): void
    {
        $this->useInformationForThirdParty = $useInformationForThirdParty;
    }

    public function getUseInformationForThirdParty(): ?bool
    {
        return $this->useInformationForThirdParty;
    }

    public function setUserID(?string $userID): void
    {
        $this->userID = $userID;
    }

    public function getUserID(): ?string
    {
        return $this->userID;
    }

    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function toXML(DOMDocument $document): DOMElement
    {
        $customer = $document->createElement(
            'Customer'
        );
        $customer->setAttribute(
            'xmlns',
            'http://schema.post.be/ServiceController/customer'
        );
        $customer->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $customer->setAttribute(
            'xsi:schemaLocation',
            'http://schema.post.be/ServiceController/customer'
        );

        $document->appendChild($customer);

        $this->namingToXML($document, $customer);
        $this->addressToXML($document, $customer);
        $this->contactToXML($document, $customer);
        $this->postalCodeToXML($document, $customer);
        $this->preferredLanguageToXML($document, $customer);
        $this->titleToXML($document, $customer);

        return $customer;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws BpostXmlNoUserIdFoundException
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): Customer
    {
        // @todo work with classmaps ...
        if (!isset($xml->UserID)) {
            throw new BpostXmlNoUserIdFoundException();
        }

        $customer = new Customer();

        if (isset($xml->UserID) && $xml->UserID != '') {
            $customer->setUserID((string) $xml->UserID);
        }
        if (isset($xml->FirstName) && $xml->FirstName != '') {
            $customer->setFirstName((string) $xml->FirstName);
        }
        if (isset($xml->LastName) && $xml->LastName != '') {
            $customer->setLastName((string) $xml->LastName);
        }
        if (isset($xml->Street) && $xml->Street != '') {
            $customer->setStreet((string) $xml->Street);
        }
        if (isset($xml->Number) && $xml->Number != '') {
            $customer->setNumber((string) $xml->Number);
        }
        if (isset($xml->CompanyName) && $xml->CompanyName != '') {
            $customer->setCompanyName((string) $xml->CompanyName);
        }
        if (isset($xml->DateOfBirth) && $xml->DateOfBirth != '') {
            $dateTime = new DateTime((string) $xml->DateOfBirth);
            $customer->setDateOfBirth($dateTime);
        }
        if (isset($xml->DeliveryCode) && $xml->DeliveryCode != '') {
            $customer->setDeliveryCode(
                (string) $xml->DeliveryCode
            );
        }
        if (isset($xml->Email) && $xml->Email != '') {
            $customer->setEmail((string) $xml->Email);
        }
        if (isset($xml->MobilePrefix) && $xml->MobilePrefix != '') {
            $customer->setMobilePrefix(
                trim((string) $xml->MobilePrefix)
            );
        }
        if (isset($xml->MobileNumber) && $xml->MobileNumber != '') {
            $customer->setMobileNumber(
                (string) $xml->MobileNumber
            );
        }
        if (isset($xml->Postalcode) && $xml->Postalcode != '') {
            $customer->setPostalCode(
                (string) $xml->Postalcode
            );
        }
        if (isset($xml->PreferredLanguage) && $xml->PreferredLanguage != '') {
            $customer->setPreferredLanguage(
                (string) $xml->PreferredLanguage
            );
        }
        if (isset($xml->ReceivePromotions) && $xml->ReceivePromotions != '') {
            $receivePromotions = in_array((string)$xml->ReceivePromotions, ['true','1'], true);
            $customer->setReceivePromotions($receivePromotions);
        }
        if (isset($xml->actived) && $xml->actived != '') {
            $activated = in_array((string)$xml->actived, ['true','1'], true);
            $customer->setActivated($activated);
        }
        if (isset($xml->Title) && $xml->Title != '') {
            $title = (string) $xml->Title;
            $title = ucfirst(strtolower($title));
            if (!str_ends_with($title, '.')) {
                $title .= '.';
            }

            $customer->setTitle($title);
        }
        if (isset($xml->Town) && $xml->Town != '') {
            $customer->setTown((string) $xml->Town);
        }

        if (isset($xml->PackStations->CustomerPackStation)) {
            foreach ($xml->PackStations->CustomerPackStation as $packStation) {
                $customer->addPackStation(CustomerPackStation::createFromXML($packStation));
            }
        }

        return $customer;
    }

    private function namingToXML(DOMDocument $document, DOMElement $customer): void
    {
        if ($this->getFirstName() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'FirstName',
                    $this->getFirstName()
                )
            );
        }
        if ($this->getLastName() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'LastName',
                    $this->getLastName()
                )
            );
        }
    }

    private function contactToXML(DOMDocument $document, DOMElement $customer): void
    {
        if ($this->getEmail() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Email',
                    $this->getEmail()
                )
            );
        }
        if ($this->getMobilePrefix() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'MobilePrefix',
                    $this->getMobilePrefix()
                )
            );
        }
        if ($this->getMobileNumber() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'MobileNumber',
                    $this->getMobileNumber()
                )
            );
        }
    }

    private function addressToXML(DOMDocument $document, DOMElement $customer): void
    {
        if ($this->getStreet() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Street',
                    $this->getStreet()
                )
            );
        }
        if ($this->getNumber() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Number',
                    $this->getNumber()
                )
            );
        }
    }

    private function preferredLanguageToXML(DOMDocument $document, DOMElement $customer): void
    {
        if ($this->getPreferredLanguage() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'PreferredLanguage',
                    $this->getPreferredLanguage()
                )
            );
        }
    }

    private function titleToXML(DOMDocument $document, DOMElement $customer): void
    {
        if ($this->getTitle() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'Title',
                    $this->getTitle()
                )
            );
        }
    }

    private function postalCodeToXML(DOMDocument $document, DOMElement $customer): void
    {
        if ($this->getPostalCode() !== null) {
            $customer->appendChild(
                $document->createElement(
                    'PostalCode',
                    $this->getPostalCode()
                )
            );
        }
    }
}
