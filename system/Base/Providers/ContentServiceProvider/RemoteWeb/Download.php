<?php

namespace System\Base\Providers\ContentServiceProvider\RemoteWeb;

class Download
{
    protected $remoteWebContent;

    protected $basepackages;

    protected $requestMethod = 'GET';

    protected $verify = false;

    protected $connectTimeout = 5;

    protected $download;

    public $trackCounter = 0;

    public $method;

    public function __construct($remoteWebContent, $basepackages)
    {
        $this->remoteWebContent = $remoteWebContent;

        $this->basepackages = $basepackages;
    }

    public function init($options = null)
    {
        return $this;
    }

    public function downloadData($url, $sink, $method = null)
    {
        $this->method = $method;

        $this->download = $this->remoteWebContent->request(
            $this->requestMethod,
            $url,
            [
                'progress' => function(
                    $downloadTotal,
                    $downloadedBytes,
                    $uploadTotal,
                    $uploadedBytes
                ) {
                    $counters =
                            [
                                'downloadTotal'     => $downloadTotal,
                                'downloadedBytes'   => $downloadedBytes,
                                'uploadTotal'       => $uploadTotal,
                                'uploadedBytes'     => $uploadedBytes
                            ];

                    if ($downloadedBytes === 0) {
                        return;
                    }

                    //Trackcounter is needed as guzzelhttp runs this in a while loop causing too many updates with same download count.
                    //So this way, we only update progress when there is actually an update.
                    if ($downloadedBytes === $this->trackCounter) {
                        return;
                    }

                    $this->trackCounter = $downloadedBytes;

                    if ($this->method) {
                        $this->basepackages->progress->updateProgress($this->method, ($downloadedBytes === $downloadTotal) ? true : null, false, null, $counters);
                    }
                },
                'verify'            => $this->verify,
                'connect_timeout'   => $this->connectTimeout,
                'sink'              => $sink,
                'headers'           => [
                    'User-Agent'    => 'Mozilla/5.0 (X11; Linux i686; rv:150.0) Gecko/20100101 Firefox/150.0'
                ]
            ]
        );

        $this->trackCounter = 0;

        if ($this->download->getStatusCode() === 200) {
            return true;
        }

        return false;
    }

    public function getDownload()
    {
        return $this->download;
    }

    public function setVerify($verify = false)
    {
        $this->verify = $verify;

        return $this;
    }

    public function setConnectTimeout($connectTimeout = 5)
    {
        $this->connectTimeout = $connectTimeout;

        return $this;
    }
}