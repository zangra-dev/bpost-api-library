<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box\Option;

use Bpost\BpostApiClient\Common\XmlHelper;
use DOMDocument;
use DOMElement;

/**
 * bPost CashOnDelivery class
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class CashOnDelivery extends Option
{
    private float $amount;
    private string $iban;
    private string $bic;

    public function __construct(float $amount, string $iban, string $bic)
    {
        $this->setAmount($amount);
        $this->setIban($iban);
        $this->setBic($bic);
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setBic(string $bic): void
    {
        $this->bic = $bic;
    }

    public function getBic(): string
    {
        return $this->bic;
    }

    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * @throws \DOMException
     */
    public function toXML(DOMDocument $document, ?string $prefix = 'common'): DOMElement
    {
        $cod = $document->createElement(XmlHelper::getPrefixedTagName('cod', $prefix));

        // Montant formaté en 2 décimales (ex: 10 => "10.00")
        $cod->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('codAmount', $prefix),
                sprintf('%.2f', $this->getAmount())
            )
        );

        $cod->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('iban', $prefix),
                $this->getIban()
            )
        );

        $cod->appendChild(
            $document->createElement(
                XmlHelper::getPrefixedTagName('bic', $prefix),
                $this->getBic()
            )
        );

        return $cod;
    }
}