<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Barcodes extends BasePackage
{
    protected $generator;

    protected $barcodesPackage;

    protected $barcodesSettings = null;

    public function init()
    {
        $this->barcodesPackage = $this->modules->packages->getNamePackage('Barcodes');

        if ($this->barcodesPackage) {

            $this->barcodesSettings = Json::decode($this->barcodesPackage['settings'], true);

            return $this;
        }
    }

    public function getBarcodesSettings()
    {
        if ($this->barcodesSettings) {
            return $this->barcodesSettings;
        } else {
            $this->init();
        }

        return $this->barcodesSettings;
    }

    public function updateBarcodesSettings($data)
    {
        $this->getBarcodesSettings();

        $data['enabledCodes'] = Json::decode($data['enabledCodes'], true);
        $this->barcodesSettings['enabledCodes'] = $data['enabledCodes'];
        $this->barcodesSettings['defaultGenerator'] = $data['defaultGenerator'];
        $this->barcodesSettings['widthFactor'] = $data['widthFactor'];
        $this->barcodesSettings['height'] = $data['height'];
        $this->barcodesSettings['foreground'] = $data['foreground'];
        if ($data['showText'] == '0') {
            $this->barcodesSettings['showText'] = false;
        } else if ($data['showText'] == '1') {
            $this->barcodesSettings['showText'] = true;
        }
        $this->barcodesSettings['defaultTextPlacement'] = $data['defaultTextPlacement'];

        $this->barcodesPackage['settings'] = Json::encode($this->barcodesSettings);

        $this->modules->packages->updatePackages($this->barcodesPackage);

        $this->packagesData->responseCode = $this->modules->packages->packagesData->responseCode;

        $this->packagesData->responseMessage = 'Updated Barcodes Settings';
    }

    public function generateBarcode(
        string $barcode,
        $barcodeType = 'EAN13',
        array $settings = [],
        bool $test = false
    ) {
        $this->getBarcodesSettings();

        if (!$test) {
            if (isset($barcodeType)) {
                if (!in_array($barcodeType, $this->barcodesSettings['enabledCodes'])) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Requested barcode type not enabled';

                    return;
                }
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'barcodeType not set.';

                return;
            }

            if (isset($settings['generatorName'])) {
                if (!in_array($settings['generatorName'], $this->barcodesSettings['availableGenerators'])) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Requested barcode generator not defined';

                    return;
                }
            } else {
                $settings['generatorName'] = $this->barcodesSettings['defaultGenerator'];
            }

            if (!isset($settings['widthFactor'])) {
                $settings['widthFactor'] = $this->barcodesSettings['widthFactor'];
            }

            if (!isset($settings['height'])) {
                $settings['height'] = $this->barcodesSettings['height'];
            }

            if (!isset($settings['foreground'])) {
                $settings['foreground'] = $this->barcodesSettings['foreground'];
            }

            if (!isset($settings['showText'])) {
                $settings['showText'] = $this->barcodesSettings['showText'];
            }

            if (!isset($settings['textPlacement'])) {
                $settings['textPlacement'] = $this->barcodesSettings['defaultTextPlacement'];
            }
        }

        if ($settings['generatorName'] === 'PNG') {
            $settings['foreground'] = $this->hex2rgb($settings['foreground']);
        }

        $generatorClass = 'Picqer\\Barcode\\BarcodeGenerator' . $settings['generatorName'];

        try {
            $this->generator = new $generatorClass();

            $generatedBarcode = '';

            if ($settings['textPlacement'] === 'TOP') {
                if ($settings['showText'] == 'true') {
                    $generatedBarcode .= '<div class="text-center"><p class="font-weight-bold mb-1" style="color: ' . $settings['foreground'] . '">' . $barcode . '</p></div>';
                }
            }
            if ($settings['generatorName'] === 'HTML' || $settings['generatorName'] === 'SVG') {
                $generatedBarcode .=
                    $this->generator->getBarcode(
                        $barcode,
                        $barcodeType,
                        $settings['widthFactor'],
                        $settings['height'],
                        $settings['foreground']
                    );
            } else if ($settings['generatorName'] === 'PNG') {
                $generatedBarcode .=
                    base64_encode(
                        $this->generator->getBarcode(
                            $barcode,
                            $barcodeType,
                            $settings['widthFactor'],
                            $settings['height'],
                            $settings['foreground']
                        )
                    );
            }
            if ($settings['textPlacement'] === 'BOTTOM') {
                if ($settings['showText'] == 'true') {
                    $generatedBarcode .= '<div class="text-center"><p class="font-weight-bold mt-1" style="color: ' . $settings['foreground'] . '">' . $barcode . '</p></div>';
                }
            }

            $this->packagesData->barcode = $generatedBarcode;

            return $generatedBarcode;

        } catch (\Exception $e) {
            $reflection = new \ReflectionClass($e);
            $classnameArr = explode('\\', $reflection->name);

            if (Arr::last($classnameArr) === 'InvalidCheckDigitException') {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Invalid Barcode. Please enter correct barcode and try again.';

                return;
            }

            if ($e->getMessage() !== '') {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $e->getMessage();

                return;
            }
            throw $e;
        }
    }

    protected function hex2rgb($hex_color)
    {
        $values = str_replace('#', '', $hex_color);

        switch (strlen($values)) {
            case 3;
                list( $r, $g, $b ) = sscanf($values, "%1s%1s%1s");
                return [hexdec("$r$r"), hexdec("$g$g"), hexdec("$b$b")];
            case 6;
                return array_map('hexdec', sscanf($values, "%2s%2s%2s"));
            default:
                return false;
        }
    }

    // $this->barcodes->getBarcodeWithChecksum('020001001001', 'EAN13');//0200010010017
    // $this->barcodes->getBarcodeWithChecksum('020001000001', 'EAN2');//0200010000011
    // $this->barcodes->getBarcodeWithChecksum('020001000001', 'EAN13');//0200010000018
    // $this->barcodes->getBarcodeWithChecksum('020001000002', 'EAN13');//0200010000025
    // $this->barcodes->getBarcodeWithChecksum('020000100100', 'UPCA');//0200001001004
    public function getBarcodeWithChecksum($barcode, $barcodeType)
    {
        $checksum = $this->generateBarcodeChecksum($barcode, $barcodeType);

        if ($checksum !== false) {
            return $barcode . $checksum;
        }
    }

    public function generateBarcodeChecksum($barcode, $barcodeType)
    {
        if (method_exists($this, $method = "checksum" . strtoupper("{$barcodeType}"))) {
            $checksum = $this->{$method}($barcode);

            return $checksum;
        }

        return false;
    }

    protected function checksumEAN2(string $code, $length = 2)
    {
        return $this->checksumEAN($code, $length);
    }

    protected function checksumEAN5(string $code, $length = 5)
    {
        return $this->checksumEAN($code, $length);
    }

    protected function checksumEAN8(string $code, $length = 8)
    {
        return $this->checksumEAN($code, $length);
    }

    protected function checksumEAN13(string $code, $length = 13)
    {
        return $this->checksumEAN($code, $length);
    }

    protected function checksumUPCA(string $code, $length = 12)
    {
        return $this->checksumEAN($code, $length);
    }

    protected function checksumUPCE(string $code, $length = 12)
    {
        return $this->checksumEAN($code, $length);
    }

    protected function checksumEAN($code, $length)
    {
        if ($length === 2 ||
            $length === 5
        ) {
            if ($length === 2) {
                return $code % 4;
            } else if ($length === 5) {
                $r = (3 * ($code[0] + $code[2] + $code[4])) + (9 * ($code[1] + $code[3]));
                $r %= 10;
                return $r;
            }
        }

        $sum_a = 0;
        for ($i = 1; $i < $length - 1; $i += 2) {
            $sum_a += $code[$i];
        }
        if ($length > 12) {
            $sum_a *= 3;
        }
        $sum_b = 0;
        for ($i = 0; $i < $length - 1; $i += 2) {
            $sum_b += intval(($code[$i]));
        }
        if ($length < 13) {
            $sum_b *= 3;
        }
        $checksumDigit = ($sum_a + $sum_b) % 10;
        if ($checksumDigit > 0) {
            $checksumDigit = (10 - $checksumDigit);
        }

        return $checksumDigit;
    }
}