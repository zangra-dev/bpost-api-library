<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient;

use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidLengthException;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;

/**
 * bPost Form handler class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class FormHandler
{
    private Bpost $bpost;

    /** @var array<string,mixed> */
    private array $parameters = [];

    /**
     * Create bPostFormHandler instance
     */
    public function __construct(string $accountId, string $passPhrase, string $apiUrl = Bpost::API_URL)
    {
        $this->bpost = new Bpost($accountId, $passPhrase, $apiUrl);
    }

    /**
     * Calculate the hash
     */
    private function getChecksum(): string
    {
        $keysToHash = [
            'accountId',
            'action',
            'costCenter',
            'customerCountry',
            'deliveryMethodOverrides',
            'extraSecure',
            'orderReference',
            'orderWeight',
        ];

        $base = 'accountId=' . $this->bpost->getAccountId() . '&';

        foreach ($keysToHash as $key) {
            if (!array_key_exists($key, $this->parameters)) {
                continue;
            }

            $value = $this->parameters[$key];

            if (!is_array($value)) {
                $base .= $key . '=' . $value . '&';
                continue;
            }

            // Si c’est un tableau, concaténer chaque entrée (tri déjà fait dans setParameter)
            foreach ($value as $entry) {
                $base .= $key . '=' . $entry . '&';
            }
        }

        // add passphrase
        $base .= $this->bpost->getPassPhrase();

        return hash('sha256', $base);
    }

    /**
     * Get the parameters
     *
     * @return array<string,mixed>
     */
    public function getParameters(bool $form = false, bool $includeChecksum = true): array
    {
        $return = $this->parameters;

        if ($form && isset($return['orderLine']) && is_array($return['orderLine'])) {
            foreach ($return['orderLine'] as $key => $value) {
                $return['orderLine[' . $key . ']'] = $value;
            }
            unset($return['orderLine']);
        }

        if ($includeChecksum) {
            $return['accountId'] = $this->bpost->getAccountId();
            $return['checksum']  = $this->getChecksum();
        }

        return $return;
    }

    /**
     * Set a parameter
     *
     * @throws BpostInvalidValueException
     * @throws BpostInvalidLengthException
     */
    public function setParameter(string $key, mixed $value): void
    {
        switch ($key) {
            // limited values
            case 'action':
            case 'lang':
                $allowedValues = [
                    'action' => ['START', 'CONFIRM'],
                    'lang'   => ['NL', 'FR', 'EN', 'DE', 'Default'],
                ];
                if (!in_array($value, $allowedValues[$key], true)) {
                    throw new BpostInvalidValueException($key, (string) $value, $allowedValues[$key]);
                }
                $this->parameters[$key] = $value;
                break;

            // maximum 2 chars
            case 'customerCountry':
                if (mb_strlen((string) $value) > 2) {
                    throw new BpostInvalidLengthException($key, mb_strlen((string) $value), 2);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 8 chars
            case 'customerStreetNumber':
            case 'customerBox':
                if (mb_strlen((string) $value) > 8) {
                    throw new BpostInvalidLengthException($key, mb_strlen((string) $value), 8);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 20 chars
            case 'customerPhoneNumber':
                if (mb_strlen((string) $value) > 20) {
                    throw new BpostInvalidLengthException($key, mb_strlen((string) $value), 20);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 32 chars
            case 'customerPostalCode':
                if (mb_strlen((string) $value) > 32) {
                    throw new BpostInvalidLengthException($key, mb_strlen((string) $value), 32);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 40 chars
            case 'customerFirstName':
            case 'customerLastName':
            case 'customerCompany':
            case 'customerStreet':
            case 'customerCity':
                if (mb_strlen((string) $value) > 40) {
                    throw new BpostInvalidLengthException($key, mb_strlen((string) $value), 40);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 50 chars
            case 'orderReference':
            case 'costCenter':
            case 'customerEmail':
                if (mb_strlen((string) $value) > 50) {
                    throw new BpostInvalidLengthException($key, mb_strlen((string) $value), 50);
                }
                $this->parameters[$key] = (string) $value;
                break;

            // integers
            case 'orderTotalPrice':
            case 'orderWeight':
                $this->parameters[$key] = (int) $value;
                break;

            // array (order lines)
            case 'orderLine':
                $this->parameters[$key] ??= [];
                $this->parameters[$key][] = $value;
                break;

            // unknown (free fields/URLs/flags/overrides...)
            case 'deliveryMethodOverrides':
            case 'extra':
            case 'extraSecure':
            case 'confirmUrl':
            case 'cancelUrl':
            case 'errorUrl':
            default:
                if (is_array($value)) {
                    // garantir un ordre stable pour le checksum
                    sort($value);
                }
                $this->parameters[$key] = $value;
        }
    }
}
