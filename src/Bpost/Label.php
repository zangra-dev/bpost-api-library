<?php
declare(strict_types=1);

namespace Bpost\BpostApiClient\Bpost;

use Bpost\BpostApiClient\Bpost\Label\Barcode;
use Bpost\BpostApiClient\Exception\BpostLogicException\BpostInvalidValueException;
use SimpleXMLElement;

/**
 * bPost Label class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class Label
{
    public const LABEL_MIME_TYPE_IMAGE_PNG        = 'image/png';
    public const LABEL_MIME_TYPE_IMAGE_PDF        = 'image/pdf';
    public const LABEL_MIME_TYPE_APPLICATION_PDF  = 'application/pdf';

    private array $barcodes = [];
    private ?string $mimeType = null;
    private string $bytes = '';

    public function addBarcode(Barcode $barcode): void
    {
        $this->barcodes[] = $barcode;
    }

    public function setBarcodes(array $barcodes): void
    {
        $this->barcodes = $barcodes;
    }

    /** @return Barcode[] */
    public function getBarcodes(): array
    {
        return $this->barcodes;
    }

    public function getBarcode(): string
    {
        if (!empty($this->barcodes)) {
            $first = $this->barcodes[0];
            return $first->getBarcode() ?? '';
        }

        return '';
    }

    public function setBytes(string $bytes): void
    {
        $this->bytes = $bytes;
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public function setMimeType(string $mimeType): void
    {
        if (!in_array($mimeType, self::getPossibleMimeTypeValues(), true)) {
            throw new BpostInvalidValueException('mimeType', $mimeType, self::getPossibleMimeTypeValues());
        }

        $this->mimeType = $mimeType;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /** @return string[] */
    public static function getPossibleMimeTypeValues(): array
    {
        return [
            self::LABEL_MIME_TYPE_IMAGE_PNG,
            self::LABEL_MIME_TYPE_IMAGE_PDF,
            self::LABEL_MIME_TYPE_APPLICATION_PDF,
        ];
    }


    /**
     * Output the bytes directly to the screen
     */
    public function output()
    {
        header('Content-type: ' . $this->getMimeType());
        echo $this->getBytes();
        exit;
    }

    /**
     * @throws BpostInvalidValueException
     */
    public static function createFromXML(SimpleXMLElement $xml): self
    {
        $label = new self();

        if (isset($xml->barcodeWithReference)) {
            foreach ($xml->barcodeWithReference as $barcodeWithReference) {
                $label->addBarcode(Barcode::createFromXML($barcodeWithReference));
            }
        }

        if (!empty($xml->mimeType)) {
            $label->setMimeType((string) $xml->mimeType);
        }

        if (!empty($xml->bytes)) {
            $label->setBytes((string) base64_decode((string) $xml->bytes));
        }

        return $label;
    }
}