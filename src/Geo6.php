<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\ApiCaller\ApiCaller;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostCurlException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidXmlResponseException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostTaxipostLocatorException;
use Bpost\BpostApiClient\Geo6\Poi;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
class Geo6
{
    public const API_URL = 'https://pudo.bpost.be/Locator';
    public const VERSION = '3';

    /**
     * Types de points (combinaisons via addition binaire)
     * @see getPointType()
     * @see getServicePointPageUrl()
     */
    public const POINT_TYPE_POST_OFFICE          = 1;
    public const POINT_TYPE_POST_POINT           = 2;
    public const POINT_TYPE_BPACK_247            = 4;
    public const POINT_TYPE_CLICK_COLLECT_SHOP   = 8;

    private ?ApiCaller $apiCaller = null;

    private string $appId;
    private string $partner;

    /** Timeout en secondes */
    private int $timeOut = 10;

    /** Suffixe d’UA applicatif */
    private string $userAgent = '';

    private LoggerInterface $logger;

    /**
     * @param string $partner Paramètre statique de protection/statistiques
     * @param string $appId   Paramètre statique de protection/statistiques
     * @param LoggerInterface|null $psrLogger Logger PSR optionnel (NullLogger par défaut)
     */
    public function __construct(string $partner, string $appId, ?LoggerInterface $logger = null)
    {
        $this->setPartner($partner);
        $this->setAppId($appId);
        $this->logger = $logger ?? new NullLogger();
    }

    public function getApiCaller(): ApiCaller
    {
        if ($this->apiCaller === null) {
            $this->apiCaller = new ApiCaller($this->logger);
        }
        return $this->apiCaller;
    }

    public function setApiCaller(ApiCaller $apiCaller): void
    {
        $this->apiCaller = $apiCaller;
    }

    /**
     * Construit l’URL de page publique (GET)
     */
    private function buildUrl(string $method, array $parameters = []): string
    {
        return self::API_URL . '?' . $this->buildParameters($method, $parameters);
    }

    /**
     * Construit le payload (url-encoded) pour POST
     */
    private function buildParameters(string $method, array $parameters = []): string
    {
        // Ajout des credentials + format
        $parameters['Function'] = $method;
        $parameters['Partner']  = $this->getPartner();
        $parameters['AppId']    = $this->getAppId();
        $parameters['Format']   = 'xml';

        return http_build_query($parameters);
    }

    /**
     * Appel réel HTTP (POST) et parsing XML
     *
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostTaxipostLocatorException
     */
    private function doCall(string $method, array $parameters = []): SimpleXMLElement
    {
        $options = [
            CURLOPT_URL            => self::API_URL,
            CURLOPT_USERAGENT      => $this->getUserAgent(),
            CURLOPT_FOLLOWLOCATION => true,
            // Le service n’exige pas de client cert — on garde le comportement historique :
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->getTimeOut(),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $this->buildParameters($method, $parameters),
        ];

        $this->getApiCaller()->doCall($options);

        $body = $this->getApiCaller()->getResponseBody();
        $xml  = @simplexml_load_string($body);

        // XML invalide ou structure d’erreur générique
        if ($xml === false || (isset($xml->head) && isset($xml->body))) {
            throw new BpostInvalidXmlResponseException();
        }

        // Erreur Taxipost
        if (isset($xml['type']) && (string) $xml['type'] === 'TaxipostLocatorError') {
            throw new BpostTaxipostLocatorException((string) $xml->txt, (int) $xml->status);
        }

        return $xml;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setPartner(string $partner): void
    {
        $this->partner = $partner;
    }

    public function getPartner(): string
    {
        return $this->partner;
    }

    /** Timeout en secondes */
    public function setTimeOut(int $seconds): void
    {
        $this->timeOut = $seconds;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    /**
     * User-Agent complet (lib + suffixe app)
     */
    public function getUserAgent(): string
    {
        return 'PHP Bpost Geo6/' . self::VERSION . ' ' . $this->userAgent;
    }

    /**
     * Suffixe d’UA (ex: "my-app/1.2.3")
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    // ======================
    // Webservice methods
    // ======================

    /**
     * The GetNearestServicePoints web service delivers the nearest bpost pick-up points
     *
     * @param string $street
     * @param string $number
     * @param string $zone
     * @param string $language nl|fr
     * @param int    $type     1=Office, 2=Point, 3=Office+Point, 4=24/7, 7=Office+Point+24/7
     * @param int    $limit
     * @param string $country  e.g. "BE", "FR"
     *
     * @return array<int, array{poi:Poi, distance:float}>
     *
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostTaxipostLocatorException
     */
    public function getNearestServicePoint(
        string $street,
        string $number,
        string $zone,
        string $language = 'nl',
        int $type = 3,
        int $limit = 10,
        string $country = 'BE'
    ): array {
        $parameters = [
            'Street'   => $street,
            'Number'   => $number,
            'Zone'     => $zone,
            'Country'  => $country,
            'Language' => $language,
            'Type'     => $type,
            'Limit'    => $limit,
        ];

        $xml = $this->doCall('search', $parameters);

        if (!isset($xml->PoiList->Poi)) {
            throw new BpostInvalidXmlResponseException();
        }

        $pois = [];
        foreach ($xml->PoiList->Poi as $poi) {
            $pois[] = [
                'poi'       => Poi::createFromXML($poi),
                'distance'  => isset($poi->Distance) ? (float) $poi->Distance : 0.0,
            ];
        }

        return $pois;
    }

    /**
     * The GetServicePointDetails web service delivers the details for a pick-up point.
     *
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostTaxipostLocatorException
     */
    public function getServicePointDetails(
        string $id,
        string $language = 'nl',
        int $type = 3,
        string $country = 'BE'
    ): Poi {
        $parameters = [
            'Id'       => $id,
            'Language' => $language,
            'Type'     => $type,
            'Country'  => $country,
        ];

        $xml = $this->doCall('info', $parameters);

        if (!isset($xml->Poi)) {
            throw new BpostInvalidXmlResponseException();
        }

        return Poi::createFromXML($xml->Poi);
    }

    /**
     * URL publique de la page bpost d’un point.
     *
     * @see getPointType() pour calculer $type
     */
    public function getServicePointPageUrl(
        string $id,
        string $language = 'nl',
        int $type = 3,
        string $country = 'BE'
    ): string {
        $parameters = [
            'Id'       => $id,
            'Language' => $language,
            'Type'     => $type,
            'Country'  => $country,
        ];

        return $this->buildUrl('page', $parameters);
    }

    /** @deprecated Renommé en getServicePointPageUrl() */
    public function getServicePointPage(
        string $id,
        string $language = 'nl',
        int $type = 3,
        string $country = 'BE'
    ): string {
        return $this->getServicePointPageUrl($id, $language, $type, $country);
    }

    /**
     * Calcule le « type » combiné (bitmask) pour filtrer les points
     */
    public function getPointType(
        bool $withPostOffice = true,
        bool $withPostPoint = true,
        bool $withBpack247 = false,
        bool $withClickAndCollectShop = false
    ): int {
        return
            ($withPostOffice ? self::POINT_TYPE_POST_OFFICE : 0)
            + ($withPostPoint ? self::POINT_TYPE_POST_POINT : 0)
            + ($withBpack247 ? self::POINT_TYPE_BPACK_247 : 0)
            + ($withClickAndCollectShop ? self::POINT_TYPE_CLICK_COLLECT_SHOP : 0);
    }
}
