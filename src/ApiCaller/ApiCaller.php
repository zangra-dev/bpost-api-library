<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\ApiCaller;

use Bpost\BpostApiClient\Exception\BpostApiResponseException\BpostCurlException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class ApiCaller
 *
 * @codeCoverageIgnore That makes a HTTP request with the bpost API
 */
class ApiCaller
{
    private ?int $responseHttpCode = null;
    private string $responseBody = '';
    private ?string $responseContentType = null;

    public function __construct(
        private readonly LoggerInterface $logger = new NullLogger()
    ) {}


    public function getResponseHttpCode(): ?int
    {
        return $this->responseHttpCode;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getResponseContentType(): ?string
    {
        return $this->responseContentType;
    }

    /**
     * @throws BpostCurlException
     */
    public function doCall(array $options): bool
    {
        $curl = curl_init();
        if (!$curl instanceof \CurlHandle) {
            throw new BpostCurlException('Unable to initialize cURL');
        }

        curl_setopt_array($curl, $options);

        $this->logger->debug('curl request', $options);

        try {
            $result = curl_exec($curl);
            $errno  = curl_errno($curl);
            $error  = curl_error($curl);

            $info = curl_getinfo($curl); // array<string,mixed>

            $this->logger->debug('curl response', [
                'status'   => $errno . ' (' . $error . ')',
                'headers'  => $info,
                'response' => $result,
            ]);

            if ($errno !== 0) {
                throw new BpostCurlException($error !== '' ? $error : 'cURL error', $errno);
            }

            // seulement maintenant qu'on sait que ce n’est pas une erreur
            $this->responseBody = is_string($result) ? $result : '';

            $this->responseHttpCode   = isset($info['http_code']) ? (int) $info['http_code'] : null;
            $this->responseContentType = $info['content_type'] ?? null;

            return true;
        } finally {
            curl_close($curl);
        }
    }

    /**
     * If the httpCode is 200, return 200
     * If the httpCode is 203, return 200
     * If the httpCode is 404 ,return 404
     * ...
     *
     * @return int
     */
    public function getHttpCodeType(): int
    {
        return (int) (100 * floor($this->responseHttpCode / 100));
    }
}
