<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost\Order\Box;

use Bpost\BpostApiClient\Bpost\Order\Receiver;
use Bpost\BpostApiClient\Common\XmlHelper;
use Bpost\BpostApiClient\Exception\XmlException\BpostXmlInvalidItemException;
use DOMDocument;
use DOMElement;
use SimpleXMLElement;

/**
 * Class BpostOnAppointment
 */
class BpostOnAppointment extends National
{
    private ?Receiver $receiver = null;
    protected ?string $inNetworkCutOff = null;

    public function setReceiver(?Receiver $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getReceiver(): ?Receiver
    {
        return $this->receiver;
    }

    public function getInNetworkCutOff(): ?string
    {
        return $this->inNetworkCutOff;
    }

    public function setInNetworkCutOff(?string $inNetworkCutOff): void
    {
        $this->inNetworkCutOff = $inNetworkCutOff;
    }

    public function toXML(DOMDocument $document, ?string $prefix = null, ?string $type = null): DOMElement
    {
        $nationalElement = $document->createElement(XmlHelper::getPrefixedTagName('nationalBox', $prefix));
        $boxElement = parent::toXML($document, null, 'bpostOnAppointment');
        $nationalElement->appendChild($boxElement);

        $this->addToXmlReceiver($document, $boxElement);
        $this->addToXmlRequestedDeliveryDate($document, $boxElement, $prefix);

        return $nationalElement;
    }

    protected function addToXmlReceiver(DOMDocument $document, DOMElement $typeElement): void
    {
        if ($this->receiver !== null) {
            $typeElement->appendChild($this->receiver->toXML($document));
        }
    }

    /**
     * @throws \DOMException
     */
    protected function addToXmlRequestedDeliveryDate(DOMDocument $document, DOMElement $typeElement, ?string $prefix): void
    {
        if ($this->inNetworkCutOff !== null && $this->inNetworkCutOff !== '') {
            $typeElement->appendChild(
                $document->createElement(
                    XmlHelper::getPrefixedTagName('inNetworkCutOff', $prefix),
                    $this->inNetworkCutOff
                )
            );
        }
    }

    public static function createFromXML(SimpleXMLElement $xml, National $self = null): BpostOnAppointment
    {
        $self = new self();

        if (!isset($xml->bpostOnAppointment)) {
            throw new BpostXmlInvalidItemException();
        }

        $bpostOnAppointmentXml = $xml->bpostOnAppointment;

        if (isset($bpostOnAppointmentXml->receiver)) {
            $self->setReceiver(
                Receiver::createFromXML(
                    $bpostOnAppointmentXml->receiver->children('http://schema.post.be/shm/deepintegration/v3/common')
                )
            );
        }

        if (isset($bpostOnAppointmentXml->inNetworkCutOff) && (string)$bpostOnAppointmentXml->inNetworkCutOff !== '') {
            $self->setInNetworkCutOff((string)$bpostOnAppointmentXml->inNetworkCutOff);
        }

        return $self;
    }
}