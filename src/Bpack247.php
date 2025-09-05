<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\Bpack247\Customer;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostApiBusinessException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostApiSystemException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostCurlException;
use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostInvalidResponseException;
use DOMDocument;
use SimpleXMLElement;
use CurlHandle;

/**
 * bPost Bpack24/7 class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Bpack247
{
    /** URL for the API */
    public const API_URL = 'http://www.bpack247.be/BpostRegistrationWebserviceREST/servicecontroller.svc';

    /** current version */
    public const VERSION = '3.0.0';

    private string $accountId;
    private string $passPhrase;

    /** @var CurlHandle|null */
    private ?CurlHandle $curl = null;

    /** * The port to use. */
    private ?int $port = null;

    /** Timeout in seconds */
    private int $timeOut = 30;

    private string $userAgent = '';

    public function __construct(string $accountId, string $passPhrase)
    {
        $this->accountId  = $accountId;
        $this->passPhrase = $passPhrase;
    }

    /**
     * Make the call
     *
     * @throws BpostApiBusinessException
     * @throws BpostApiSystemException
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     */
    private function doCall(string $url, ?string $body = null, string $method = 'GET'): SimpleXMLElement
    {
        $headers = [
            'Authorization: Basic ' . $this->getAuthorizationHeader(),
        ];

        $options = [
            CURLOPT_URL            => self::API_URL . $url,
            CURLOPT_USERAGENT      => $this->getUserAgent(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->getTimeOut(),
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER     => $headers,
        ];

        if ($this->port !== null) {
            $options[CURLOPT_PORT] = $this->port;
        }

        if ($method === 'POST') {
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = $body ?? '';
        }

        $this->curl = curl_init();
        curl_setopt_array($this->curl, $options);

        try {
            $response = curl_exec($this->curl);
            $info     = curl_getinfo($this->curl);

            $errorNumber  = curl_errno($this->curl);
            $errorMessage = curl_error($this->curl);

            if ($errorNumber !== 0) {
                throw new BpostCurlException($errorMessage, $errorNumber);
            }

            $httpCode = (int)($info['http_code'] ?? 0);

            // Non 200 => tenter de parser l'erreur métier pour renvoyer l’exception dédiée
            if (!in_array($httpCode, [0, 200], true)) {
                $xml = @simplexml_load_string((string)$response);

                if (
                    $xml !== false
                    && ($xml->getName() === 'businessException' || $xml->getName() === 'systemException')
                ) {
                    $message = (string) ($xml->message ?? '');
                    $code    = isset($xml->code) ? (int) $xml->code : 0;

                    if ($xml->getName() === 'businessException') {
                        throw new BpostApiBusinessException($message, $code);
                    }
                    throw new BpostApiSystemException($message, $code);
                }

                throw new BpostInvalidResponseException('', $httpCode);
            }

            // 200: parser XML
            $xml = simplexml_load_string((string)$response);
            if ($xml === false) {
                // pas de XML valide alors que 200 => considérer comme réponse invalide
                throw new BpostInvalidResponseException('Empty or invalid XML body', 200);
            }

            if ($xml->getName() === 'businessException') {
                $message = (string) ($xml->message ?? '');
                $code    = (int) ($xml->code ?? 0);
                throw new BpostApiBusinessException($message, $code);
            }

            return $xml;
        } finally {
            if (is_resource($this->curl) || $this->curl instanceof CurlHandle) {
                curl_close($this->curl);
            }
            $this->curl = null;
        }
    }

    /**
     * Generate the secret string for the Authorization header
     */
    private function getAuthorizationHeader(): string
    {
        return base64_encode($this->accountId . ':' . $this->passPhrase);
    }

    /**
     * After this time the request will stop.
     */
    public function setTimeOut(int $seconds): void
    {
        $this->timeOut = $seconds;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    /**
     * Get the useragent that will be used.
     * Our version will be prepended to yours.
     * It will look like: "PHP Bpost/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        $extra = trim($this->userAgent);
        return sprintf('PHP Bpost Bpack247/%s%s', self::VERSION, $extra !== '' ? ' ' . $extra : '');
    }

    /**
     * Set your application user-agent, e.g. "MyApp/1.2.3"
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    // Webservice methods
    /**
     * @throws BpostApiBusinessException
     * @throws BpostApiSystemException
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     */
    public function createMember(Customer $customer): SimpleXMLElement
    {
        $url = '/customer';

        $document = new DOMDocument('1.0', 'utf-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->appendChild($customer->toXML($document));

        return $this->doCall($url, $document->saveXML(), 'POST');
    }

    /**
     * Retrieve member information
     *
     * @throws BpostApiBusinessException
     * @throws BpostApiSystemException
     * @throws BpostCurlException
     * @throws BpostInvalidResponseException
     * @throws Exception\XmlException\BpostXmlNoUserIdFoundException
     */
    public function getMember(string $id): Customer
    {
        $xml = $this->doCall('/customer/' . $id);
        return Customer::createFromXML($xml);
    }
}