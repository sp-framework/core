<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Api\BasepackagesApiCalls;

class ApiStats extends BasePackage
{
    protected $statsDirectory = 'var/api/callStats/';

    public function init()
    {
        return $this;
    }

    //Change this to OPCache
    public function initApiCallStats(array $data)
    {
        if ($this->localContent->fileExists($this->statsDirectory . $data['provider'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['provider'] . '.json'), true);
        }

        if (!isset($callStats[$data['id']])) {
            $callStats[$data['id']] = [];
        }

        $this->localContent->write($this->statsDirectory . $data['provider'] . '.json', Json::encode($callStats));
    }

    public function removeApiCallStats(array $data)
    {
        $callStats = [];

        if ($this->localContent->fileExists($this->statsDirectory . $data['provider'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['provider'] . '.json'), true);
        }

        if (isset($callStats[$data['id']])) {
            unset($callStats[$data['id']]);
        }

        $this->localContent->write($this->statsDirectory . $data['provider'] . '.json', Json::encode($callStats));
    }

    public function getApiCallStats(array $data)
    {
        if ($this->localContent->fileExists($this->statsDirectory . $data['provider'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['provider'] . '.json'), true);
        }

        if (isset($callStats[$data['api_id']])) {
            return $callStats[$data['api_id']];
        }

        return [];
    }

    public function setApiCallStats($data, array $callData)
    {
        if ($this->localContent->fileExists($this->statsDirectory . $data['provider'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['provider'] . '.json'), true);
        }

        $callStats[$data['api_id']]['timestamp'] = new \DateTime('now');

        $callStats[$data['api_id']]['rateLimits'] = $callData['rateLimits'];

        $this->localContent->write($this->statsDirectory . $data['provider'] . '.json', Json::encode($callStats));
    }

    public function updateApiCallStats($callMethod, $apiId, $callStats)
    {
        $this->modelToUse = BasepackagesApiCalls::class;

        $data['call_method'] = $callMethod;
        $data['api_id'] = $apiId;
        $data['call_exec_time'] = $callStats['total_time'];
        $data['call_response_code'] = $callStats['http_code'];
        $data['api_id'] = $apiId;
        $data['call_stats'] = Json::encode($callStats);

        $this->add($data);
    }

    public function getApiCallMethodStat($callMethod, $apiId)
    {
        $api = new BasepackagesApiCalls;

        $methodEntry = $api::findFirst(
            [
                'conditions' => 'call_method = :cm: AND api_id = :aid: AND call_response_code = :crc:',
                'bind'       =>
                    [
                        'cm'    => $callMethod,
                        'aid'   => $apiId,
                        'crc'   => 200
                    ],
                'order'     => 'id desc'
            ]
        );

        if ($methodEntry) {

            $methodEntry = $methodEntry->toArray();

            if ($this->apiConfig['provider'] === 'xero') {
                return \Carbon\Carbon::parse($methodEntry['called_at'])->setTimezone('UTC')->toDateTimeString();
            }

            return $methodEntry['called_at'];
        }

        return false;
    }
}