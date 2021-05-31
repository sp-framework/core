<?php

namespace Apps\Dash\Packages\System\Tools\Qrcodes;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Qrcodes extends BasePackage
{
    protected $qrcode;

    protected $qrcodesPackage;

    protected $qrcodesSettings = null;

    public function init()
    {
        $this->qrcodesPackage = $this->modules->packages->getNamePackage('Qrcodes');

        if ($this->qrcodesPackage) {

            $this->qrcodesSettings = Json::decode($this->qrcodesPackage['settings'], true);

            return $this;
        }
    }

    public function getQrcodesSettings()
    {
        if ($this->qrcodesSettings) {
            return $this->qrcodesSettings;
        } else {
            $this->init();
        }

        return $this->qrcodesSettings;
    }

    public function updateQrcodesSettings($data)
    {
        $this->getQrcodesSettings();

        $data = $this->extractRGB($data);

        $this->qrcodesSettings['foregroundColor'] = $data['foregroundColor'];
        $this->qrcodesSettings['backgroundColor'] = $data['backgroundColor'];
        if ($data['showLogo'] == '0') {
            $this->qrcodesSettings['showLogo'] = false;
        } else if ($data['showLogo'] == '1') {
            $this->qrcodesSettings['showLogo'] = true;
        }
        $this->qrcodesSettings['logoWidth'] = $data['logoWidth'];
        $this->basepackages->storages->changeOrphanStatus($data['logo'], $this->qrcodesSettings['logo']);
        $this->qrcodesSettings['logo'] = $data['logo'];
        $this->qrcodesSettings['defaultEncoding'] = $data['defaultEncoding'];
        $this->qrcodesSettings['defaultWriter'] = $data['defaultWriter'];
        $this->qrcodesSettings['defaultECL'] = $data['defaultECL'];
        $this->qrcodesSettings['defaultSize'] = $data['defaultSize'];
        $this->qrcodesSettings['defaultMargin'] = $data['defaultMargin'];
        if ($data['showLabel'] == '0') {
            $this->qrcodesSettings['showLabel'] = false;
        } else if ($data['showLabel'] == '1') {
            $this->qrcodesSettings['showLabel'] = true;
        }
        $this->qrcodesSettings['defaultLabelAlignment'] = $data['defaultLabelAlignment'];
        $this->qrcodesSettings['labelFontSize'] = $data['labelFontSize'];
        $this->qrcodesSettings['labelText'] = $data['labelText'];

        $this->qrcodesPackage['settings'] = Json::encode($this->qrcodesSettings);


        $this->modules->packages->updatePackage($this->qrcodesPackage);

        $this->packagesData->responseCode = $this->modules->packages->packagesData->responseCode;

        $this->packagesData->responseMessage = 'Updated Qrcodes Settings';
    }

    public function generateQrcode(string $text, $settings = [])
    {
        $this->qrcode = new QrCode($text);

        $this->getQrcodesSettings();

        $settings = $this->extractRGB($settings);

        if (isset($settings['defaultSize'])) {
            $this->qrcode->setSize($settings['defaultSize']);
        } else {
            $this->qrcode->setSize($this->qrcodesSettings['defaultSize']);
        }

        if (isset($settings['defaultMargin'])) {
            $this->qrcode->setMargin($settings['defaultMargin']);
        } else {
            $this->qrcode->setMargin($this->qrcodesSettings['defaultMargin']);
        }

        if (isset($settings['defaultWriter'])) {
            $this->qrcode->setWriterByName($settings['defaultWriter']);
        } else {
            $this->qrcode->setWriterByName($this->qrcodesSettings['defaultWriter']);
        }

        if (isset($settings['defaultEncoding'])) {
            $this->qrcode->setEncoding($settings['defaultEncoding']);
        } else {
            $this->qrcode->setEncoding($this->qrcodesSettings['defaultEncoding']);
        }

        if (!isset($settings['defaultECL'])) {
            $settings['defaultECL'] = $this->qrcodesSettings['defaultECL'];
        }

        $this->qrcode->setErrorCorrectionLevel(ErrorCorrectionLevel::{$settings['defaultECL']}());

        if (isset($settings['foregroundColor'])) {
            $this->qrcode->setForegroundColor($settings['foregroundColor']);
        } else {
            $this->qrcode->setForegroundColor($this->qrcodesSettings['foregroundColor']);
        }

        if (isset($settings['backgroundColor'])) {
            $this->qrcode->setBackgroundColor($settings['backgroundColor']);
        } else {
            $this->qrcode->setBackgroundColor($this->qrcodesSettings['backgroundColor']);
        }

        if (!isset($settings['showLabel'])) {
            $settings['showLabel'] = $this->qrcodesSettings['showLabel'];
        }
        if ($settings['showLabel'] === 'true') {
            if (!isset($settings['labelText'])) {
                $settings['labelText'] = $this->qrcodesSettings['labelText'];
            }

            if (!isset($settings['labelFontSize'])) {
                $settings['labelFontSize'] = $this->qrcodesSettings['labelFontSize'];
            }

            if (!isset($settings['defaultLabelAlignment'])) {
                $settings['defaultLabelAlignment'] = $this->qrcodesSettings['defaultLabelAlignment'];
            }

            $this->qrcode->setLabel(
                $settings['labelText'],
                $settings['labelFontSize'],
                null,
                $settings['defaultLabelAlignment']
            );
        }

        if (!isset($settings['showLogo'])) {
            $settings['showLogo'] = $this->qrcodesSettings['showLogo'];
        }
        if ($settings['showLogo'] === 'true') {
            if (!isset($settings['logoWidth'])) {
                $settings['logoWidth'] = $this->qrcodesSettings['logoWidth'];
            }
            $this->qrcode->setLogoSize($settings['logoWidth']);

            if (!isset($settings['logo'])) {
                $settings['logo'] = $this->qrcodesSettings['logo'];
            }
            if ($settings['logo'] !== '') {
                $path = $this->basepackages->storages->getPublicLink($settings['logo'], $settings['logoWidth']);

                $this->qrcode->setLogoPath(base_path('public' . $path));
            } else {
                $this->qrcode->setLogoPath(base_path('public/dash/default/images/baz/logo/justlogo110x110.png'));
            }
        }

        // var_dump($this->qrcode);die();
        try {
            $generatedQrcode = $this->qrcode->writeDataUri();

            $this->packagesData->qrcode = $generatedQrcode;

            return $generatedQrcode;
        } catch (\Exception $e) {
            if ($e->getMessage() !== '') {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $e->getMessage();

                return;
            }

            throw $e;
        }
    }

    protected function extractRGB($settings)
    {
        if (isset($settings['foregroundColor'])) {
            if (is_string($settings['foregroundColor'])) {
                $settings['foregroundColor'] = rtrim($settings['foregroundColor'], ')');
                $settings['foregroundColor'] = str_replace(' ', '', $settings['foregroundColor']);
                $settings['foregroundColor'] = explode(',', $settings['foregroundColor']);
                if (strpos($settings['foregroundColor'][0], 'a')) {
                    $settings['foregroundColor'][0] = ltrim($settings['foregroundColor'][0], 'rgba(');
                    if ($settings['foregroundColor'][3] == 0) {
                        $settings['foregroundColor'][3] = '1';
                    }
                } else {
                    $settings['foregroundColor'][0] = ltrim($settings['foregroundColor'][0], 'rgb(');
                    $settings['foregroundColor'][3] = '1';
                }
                $settings['foregroundColor']['r'] = $settings['foregroundColor'][0];
                $settings['foregroundColor']['g'] = $settings['foregroundColor'][1];
                $settings['foregroundColor']['b'] = $settings['foregroundColor'][2];
                $settings['foregroundColor']['a'] = $settings['foregroundColor'][3];

                unset($settings['foregroundColor'][0]);
                unset($settings['foregroundColor'][1]);
                unset($settings['foregroundColor'][2]);
                unset($settings['foregroundColor'][3]);
            }
        } else {
            $settings['foregroundColor'] = $this->qrcodesSettings['foregroundColor'];
        }

        if (isset($settings['backgroundColor'])) {
            if (is_string($settings['backgroundColor'])) {
                $settings['backgroundColor'] = rtrim($settings['backgroundColor'], ')');
                $settings['backgroundColor'] = str_replace(' ', '', $settings['backgroundColor']);
                $settings['backgroundColor'] = explode(',', $settings['backgroundColor']);
                if (strpos($settings['backgroundColor'][0], 'a')) {
                    $settings['backgroundColor'][0] = ltrim($settings['backgroundColor'][0], 'rgba(');
                    if ($settings['backgroundColor'][3] == '0') {
                        $settings['backgroundColor'][3] = '127';
                    }
                } else {
                    $settings['backgroundColor'][0] = ltrim($settings['backgroundColor'][0], 'rgb(');
                    $settings['backgroundColor'][3] = '1';
                }
                $settings['backgroundColor']['r'] = $settings['backgroundColor'][0];
                $settings['backgroundColor']['g'] = $settings['backgroundColor'][1];
                $settings['backgroundColor']['b'] = $settings['backgroundColor'][2];
                $settings['backgroundColor']['a'] = $settings['backgroundColor'][3];

                unset($settings['backgroundColor'][0]);
                unset($settings['backgroundColor'][1]);
                unset($settings['backgroundColor'][2]);
                unset($settings['backgroundColor'][3]);
            }
        } else {
            $settings['backgroundColor'] = $this->qrcodesSettings['backgroundColor'];
        }

        return $settings;
    }
}