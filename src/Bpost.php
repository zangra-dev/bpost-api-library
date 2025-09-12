<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\ApiCaller\ApiCaller;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\CreateLabelForBoxBuilder;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\CreateLabelForOrderBuilder;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\CreateLabelInBulkForOrdersBuilder;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\CreateOrReplaceOrderBuilder;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\FetchOrderBuilder;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\FetchProductConfigBuilder;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\HttpRequestBuilderInterface;
use Bpost\BpostApiClient\Bpost\Labels;
use Bpost\BpostApiClient\Bpost\Order;
use Bpost\BpostApiClient\Bpost\Order\Box;
use Bpost\BpostApiClient\Bpost\Order\Box\Option\Insured;
use Bpost\BpostApiClient\Bpost\ProductConfiguration;
use Bpost\BpostApiClient\Common\ValidatedValue\LabelFormat;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostCurlException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidResponseException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidSelectionException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidXmlResponseException;
use Bpost\BpostApiClient\Exception\BpostLogicException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use Bpost\BpostApiClient\Exception\BpostNotImplementedException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlNoReferenceFoundException;
use Bpost\BpostApiClient\Bpost\HttpRequestBuilder\ModifyOrderBuilder;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleXMLElement;

/**
 * Bpost class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Bpost
{
    public const LABEL_FORMAT_A4 = 'A4';
    public const LABEL_FORMAT_A6 = 'A6';
    public const API_URL = 'https://shm-rest.bpost.cloud/services/shm';
    public const VERSION = '3.3.0';
    public const MIN_WEIGHT = 0;
    public const MAX_WEIGHT = 30000;
    private ?ApiCaller $apiCaller = null;
    private string $accountId;
    private string $passPhrase;
    private int $port = 0;
    private int $timeOut = 30;
    private ?string $userAgent = null;
    private string $apiUrl;
    private ?LoggerInterface $logger;

    public function __construct(string $accountId, string $passPhrase, string $apiUrl = self::API_URL, ?LoggerInterface $logger = null)
    {
        $this->accountId  = $accountId;
        $this->passPhrase = $passPhrase;
        $this->apiUrl     = $apiUrl;
        $this->logger     = $logger ?? new NullLogger();
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
     * @throws BpostXmlInvalidItemException
     */
    private static function decodeResponse(SimpleXMLElement $item, ?array $return = null, int $i = 0): array
    {
        $arrayKeys   = [
            'barcode',
            'orderLine',
            Insured::INSURANCE_TYPE_ADDITIONAL_INSURANCE,
            Box\Option\Messaging::MESSAGING_TYPE_INFO_DISTRIBUTED,
            'infoPugo',
        ];
        $integerKeys = ['totalPrice'];

        foreach ($item as $key => $value) {
            $key = (string) $key;
            $attributes = (array) $value->attributes();

            if (!empty($attributes) && isset($attributes['@attributes'])) {
                $return[$key]['@attributes'] = $attributes['@attributes'];
            }

            if (isset($value['nil']) && (string) $value['nil'] === 'true') {
                $return[$key] = null;
            } elseif (isset($value[0]) && (string) $value == '') {
                if (in_array($key, $arrayKeys, true)) {
                    $return[$key][] = self::decodeResponse($value);
                } else {
                    $return[$key] = self::decodeResponse($value, null, 1);
                }
            } else {
                if (in_array($key, $arrayKeys, true)) {
                    $return[$key][] = (string) $value;
                } elseif ((string) $value === 'true') {
                    $return[$key] = true;
                } elseif ((string) $value === 'false') {
                    $return[$key] = false;
                } elseif (in_array($key, $integerKeys, true)) {
                    $return[$key] = (int) $value;
                } else {
                    $return[$key] = (string) $value;
                }
            }
        }

        return $return ?? [];
    }

    /**
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidXmlResponseException
     */
    private function doCall(HttpRequestBuilderInterface $builder): string|SimpleXMLElement
    {
        $headers   = $builder->getHeaders();
        $headers[] = 'Authorization: Basic ' . $this->getAuthorizationHeader();

        $options = [
            CURLOPT_URL            => rtrim($this->apiUrl, '/') . '/' . rawurlencode($this->accountId) . $builder->getUrl(),
            CURLOPT_USERAGENT      => $this->getUserAgent(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->getTimeOut(),
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER     => $headers,
        ];

        if ($this->getPort() !== 0) {
            $options[CURLOPT_PORT] = $this->getPort();
        }
        if ($builder->getMethod() === 'POST') {
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = $builder->getXml();
        }

        $this->getApiCaller()->doCall($options);

        $response    = $this->getApiCaller()->getResponseBody();
        $httpCode    = $this->getApiCaller()->getResponseHttpCode();
        $contentType = $this->getApiCaller()->getResponseContentType();

        if (!in_array($httpCode, [0, 200, 201], true)) {
            $xml = @simplexml_load_string($response);

            if ($xml !== false && str_starts_with($xml->getName(), 'invalid')) {
                $message = (string) $xml->error;
                $code    = isset($xml->code) ? (int) $xml->code : null;
                throw new BpostInvalidSelectionException($message, $code);
            }

            $message = '';
            if (($contentType !== null && str_contains($contentType, 'text/plain')) || in_array($httpCode, [400, 404], true)) {
                $message = $response;
            }

            throw new BpostInvalidResponseException($message, $httpCode);
        }

        if (!$builder->isExpectXml()) {
            return $response;
        }

        $xml = @simplexml_load_string($response);
        if ($xml === false) {
            throw new BpostInvalidXmlResponseException();
        }

        return $xml;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    private function getAuthorizationHeader(): string
    {
        return base64_encode($this->accountId . ':' . $this->passPhrase);
    }

    public function getPassPhrase(): string
    {
        return $this->passPhrase;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function getUserAgent(): string
    {
        return 'PHP Bpost/' . self::VERSION . ' ' . ($this->userAgent ?? '');
    }

    public function setTimeOut(int $seconds): void
    {
        $this->timeOut = $seconds;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    // ========== Webservice methods ==========

    /**
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidXmlResponseException
     */
    public function createOrReplaceOrder(Order $order): bool
    {
        $builder = new CreateOrReplaceOrderBuilder($order, $this->accountId);
        return $this->doCall($builder) === '';
    }


    /**
     * @throws BpostNotImplementedException
     * @throws BpostXmlNoReferenceFoundException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidValueException
     * @throws BpostInvalidResponseException
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     */
    public function fetchOrder(string $reference): Order
    {
        $builder = new FetchOrderBuilder($reference);
        $xml     = $this->doCall($builder);
        \assert($xml instanceof SimpleXMLElement);

        return Order::createFromXML($xml);
    }

    /**
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidXmlResponseException
     */
    public function fetchProductConfig(): ProductConfiguration
    {
        $builder = new FetchProductConfigBuilder();
        $xml     = $this->doCall($builder);
        \assert($xml instanceof SimpleXMLElement);

        return ProductConfiguration::createFromXML($xml);
    }

    /**
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidValueException
     * @throws BpostInvalidXmlResponseException
     */
    public function modifyOrderStatus(string $reference, string $status): bool
    {
        $builder = new ModifyOrderBuilder($reference, $status);
        return $this->doCall($builder) === '';
    }

    /** @return string[] */
    public static function getPossibleLabelFormatValues(): array
    {
        return [self::LABEL_FORMAT_A4, self::LABEL_FORMAT_A6];
    }

    /**
     * @throws BpostInvalidResponseException
     * @throws BpostLogicException
     * @throws BpostCurlException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostInvalidValueException
     */
    public function createLabelForOrder(
        string $reference,
        string $format = self::LABEL_FORMAT_A6,
        bool $withReturnLabels = false,
        bool $asPdf = false
    ): array {
        $builder = new CreateLabelForOrderBuilder($reference, new LabelFormat($format), $asPdf, $withReturnLabels);
        $xml     = $this->doCall($builder);
        \assert($xml instanceof SimpleXMLElement);

        return Labels::createFromXML($xml);
    }

    /**
     * @throws BpostInvalidResponseException
     * @throws BpostLogicException
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidValueException
     */
    public function createLabelForBox(
        string $barcode,
        string $format = self::LABEL_FORMAT_A6,
        bool $withReturnLabels = false,
        bool $asPdf = false
    ): array {
        $builder = new CreateLabelForBoxBuilder($barcode, new LabelFormat($format), $asPdf, $withReturnLabels);
        $xml     = $this->doCall($builder);
        \assert($xml instanceof SimpleXMLElement);

        return Labels::createFromXML($xml);
    }

    /**
     * @throws BpostInvalidResponseException
     * @throws BpostLogicException
     * @throws BpostCurlException
     * @throws BpostInvalidXmlResponseException
     * @throws BpostInvalidSelectionException
     * @throws BpostInvalidValueException
     */
    public function createLabelInBulkForOrders(
        array $references,
        string $format = LabelFormat::FORMAT_A6,
        bool $withReturnLabels = false,
        bool $asPdf = false,
        bool $forcePrinting = false
    ): array {
        $builder = new CreateLabelInBulkForOrdersBuilder(
            $references,
            new LabelFormat($format),
            $asPdf,
            $withReturnLabels,
            $forcePrinting
        );
        $xml = $this->doCall($builder);
        \assert($xml instanceof SimpleXMLElement);

        return Labels::createFromXML($xml);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger->setLogger($logger);
    }

    public function isValidWeight(int $weight): bool
    {
        return self::MIN_WEIGHT <= $weight && $weight <= self::MAX_WEIGHT;
    }
}