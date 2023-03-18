<?php

namespace Apps\Dash\Packages\System\Tools\Qrcodes;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
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
        $this->qrcodesPackage = $this->modules->packages->get(['name' => 'Qrcodes']);

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

        $this->qrcodesSettings['codeForegroundColor'] = $data['codeForegroundColor'];
        $this->qrcodesSettings['codeBackgroundColor'] = $data['codeBackgroundColor'];
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
        $this->qrcodesSettings['labelColor'] = $data['labelColor'];
        $this->qrcodesSettings['labelText'] = $data['labelText'];

        $this->qrcodesPackage['settings'] = Json::encode($this->qrcodesSettings);


        $this->modules->packages->updatePackage($this->qrcodesPackage);

        $this->packagesData->responseCode = $this->modules->packages->packagesData->responseCode;

        $this->packagesData->responseMessage = 'Updated Qrcodes Settings';
    }

    public function generateQrcode(string $text, $settings = [])
    {
        if (count($settings) === 0) {
            $this->getQrcodesSettings();
        } else {
            $this->qrcodesSettings = array_merge($this->getQrcodesSettings(), $settings);
        }

        $this->qrcodesSettings = $this->extractRGB($this->qrcodesSettings);

        $qrCode = QrCode::create($text);
        $qrCode->setSize($this->qrcodesSettings['defaultSize']);
        $qrCode->setMargin($this->qrcodesSettings['defaultMargin']);
        $qrCode->setEncoding(new Encoding($this->qrcodesSettings['defaultEncoding']));
        $ecl = '\Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel' . ucfirst(strtolower($this->qrcodesSettings['defaultECL']));
        $qrCode->setErrorCorrectionLevel(new $ecl());
        $qrCode->setForegroundColor(
            new Color(
                $this->qrcodesSettings['codeForegroundColor']['r'],
                $this->qrcodesSettings['codeForegroundColor']['g'],
                $this->qrcodesSettings['codeForegroundColor']['b'],
                $this->qrcodesSettings['codeForegroundColor']['a']
            )
        );
        $qrCode->setBackgroundColor(
            new Color(
                $this->qrcodesSettings['codeBackgroundColor']['r'],
                $this->qrcodesSettings['codeBackgroundColor']['g'],
                $this->qrcodesSettings['codeBackgroundColor']['b'],
                $this->qrcodesSettings['codeBackgroundColor']['a']
            )
        );

        if ($this->qrcodesSettings['showLogo'] === 'true') {
            if ($this->qrcodesSettings['logo'] !== '') {
                $logoPath = $this->basepackages->storages->getPublicLink($this->qrcodesSettings['logo'], $this->qrcodesSettings['logoWidth']);

                $logoPath = base_path('public' . $path);
            } else {
                $logoPath = base_path('public/dash/default/images/baz/logo/justlogo110x110.png');
            }

            $logo = Logo::create($logoPath);
            $logo->setResizeToWidth($this->qrcodesSettings['logoWidth']);
        } else {
            $logo = null;
        }

        if ($this->qrcodesSettings['showLabel'] === 'true') {
            $label = Label::create($this->qrcodesSettings['labelText']);
            $alignment = '\Endroid\QrCode\Label\Alignment\LabelAlignment' . ucfirst(strtolower($this->qrcodesSettings['defaultLabelAlignment']));
            $label->setAlignment(new $alignment());
            $label->setFont(new Font(base_path('system/ThirdParty/vendor/endroid/qr-code/assets/noto_sans.otf'), $this->qrcodesSettings['labelFontSize']));
            $label->setTextColor(
                new Color(
                    $this->qrcodesSettings['labelColor']['r'],
                    $this->qrcodesSettings['labelColor']['g'],
                    $this->qrcodesSettings['labelColor']['b'],
                    $this->qrcodesSettings['labelColor']['a']
                )
            );
        } else {
            $label = null;
        }

        $writerClass = '\Endroid\QrCode\Writer\\' . ucfirst(strtolower($this->qrcodesSettings['defaultWriter']) . 'Writer');

        $writer = new $writerClass();

        try {
            $generatedQrcode = $writer->write($qrCode, $logo, $label);

            $this->packagesData->qrcode = $generatedQrcode->getDataUri();

            return $generatedQrcode->getDataUri();
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
        if (isset($settings['codeForegroundColor'])) {
            if (is_string($settings['codeForegroundColor'])) {
                $settings['codeForegroundColor'] = rtrim($settings['codeForegroundColor'], ')');
                $settings['codeForegroundColor'] = str_replace(' ', '', $settings['codeForegroundColor']);
                $settings['codeForegroundColor'] = explode(',', $settings['codeForegroundColor']);
                if (strpos($settings['codeForegroundColor'][0], 'a')) {
                    $settings['codeForegroundColor'][0] = ltrim($settings['codeForegroundColor'][0], 'rgba(');
                    if ($settings['codeForegroundColor'][3] == 0) {
                        $settings['codeForegroundColor'][3] = '1';
                    }
                } else {
                    $settings['codeForegroundColor'][0] = ltrim($settings['codeForegroundColor'][0], 'rgb(');
                    $settings['codeForegroundColor'][3] = '1';
                }
                $settings['codeForegroundColor']['r'] = $settings['codeForegroundColor'][0];
                $settings['codeForegroundColor']['g'] = $settings['codeForegroundColor'][1];
                $settings['codeForegroundColor']['b'] = $settings['codeForegroundColor'][2];
                $settings['codeForegroundColor']['a'] = $settings['codeForegroundColor'][3];

                unset($settings['codeForegroundColor'][0]);
                unset($settings['codeForegroundColor'][1]);
                unset($settings['codeForegroundColor'][2]);
                unset($settings['codeForegroundColor'][3]);
            }
        } else {
            $settings['codeForegroundColor'] = $this->qrcodesSettings['codeForegroundColor'];
        }

        if (isset($settings['codeBackgroundColor'])) {
            if (is_string($settings['codeBackgroundColor'])) {
                $settings['codeBackgroundColor'] = rtrim($settings['codeBackgroundColor'], ')');
                $settings['codeBackgroundColor'] = str_replace(' ', '', $settings['codeBackgroundColor']);
                $settings['codeBackgroundColor'] = explode(',', $settings['codeBackgroundColor']);
                if (strpos($settings['codeBackgroundColor'][0], 'a')) {
                    $settings['codeBackgroundColor'][0] = ltrim($settings['codeBackgroundColor'][0], 'rgba(');
                    if ($settings['codeBackgroundColor'][3] == '0') {
                        $settings['codeBackgroundColor'][3] = '127';
                    }
                } else {
                    $settings['codeBackgroundColor'][0] = ltrim($settings['codeBackgroundColor'][0], 'rgb(');
                    $settings['codeBackgroundColor'][3] = '1';
                }
                $settings['codeBackgroundColor']['r'] = $settings['codeBackgroundColor'][0];
                $settings['codeBackgroundColor']['g'] = $settings['codeBackgroundColor'][1];
                $settings['codeBackgroundColor']['b'] = $settings['codeBackgroundColor'][2];
                $settings['codeBackgroundColor']['a'] = $settings['codeBackgroundColor'][3];

                unset($settings['codeBackgroundColor'][0]);
                unset($settings['codeBackgroundColor'][1]);
                unset($settings['codeBackgroundColor'][2]);
                unset($settings['codeBackgroundColor'][3]);
            }
        } else {
            $settings['codeBackgroundColor'] = $this->qrcodesSettings['codeBackgroundColor'];
        }

        if (isset($settings['labelColor'])) {
            if (is_string($settings['labelColor'])) {
                $settings['labelColor'] = rtrim($settings['labelColor'], ')');
                $settings['labelColor'] = str_replace(' ', '', $settings['labelColor']);
                $settings['labelColor'] = explode(',', $settings['labelColor']);
                if (strpos($settings['labelColor'][0], 'a')) {
                    $settings['labelColor'][0] = ltrim($settings['labelColor'][0], 'rgba(');
                    if ($settings['labelColor'][3] == 0) {
                        $settings['labelColor'][3] = '1';
                    }
                } else {
                    $settings['labelColor'][0] = ltrim($settings['labelColor'][0], 'rgb(');
                    $settings['labelColor'][3] = '1';
                }
                $settings['labelColor']['r'] = $settings['labelColor'][0];
                $settings['labelColor']['g'] = $settings['labelColor'][1];
                $settings['labelColor']['b'] = $settings['labelColor'][2];
                $settings['labelColor']['a'] = $settings['labelColor'][3];

                unset($settings['labelColor'][0]);
                unset($settings['labelColor'][1]);
                unset($settings['labelColor'][2]);
                unset($settings['labelColor'][3]);
            }
        } else {
            $settings['labelColor'] = $this->qrcodesSettings['labelColor'];
        }

        return $settings;
    }
}