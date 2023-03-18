<?php

namespace System\Base\Providers\AppsServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Ip;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Exceptions\IpFilterBlockedException;
use System\Base\Providers\AppsServiceProvider\Model\AppsIpFilter;

class IpFilter extends BasePackage
{
    protected $modelToUse = AppsIpFilter::class;

    protected $packageName = 'ipfilter';

    protected $packageNameS = 'ipfilter';

    public $clientAddress;

    protected $app;

    public function init($app = null)
    {
        $this->app = $app;

        return $this;
    }

    public function getFilters(array $data)
    {
        if (!isset($data['app_id'])) {
            return Json::encode([]);//blank array
        }

        $app = $this->app->getFirst('id', $data['app_id']);

        $filtersObj = $app->getIpFilters();

        if ($filtersObj && $filtersObj->count() > 0) {
            $filters = $filtersObj->toArray();
        } else {
            $filters = [];
        }

        return $filters;
    }

    public function addFilter(array $data)
    {
        if (!isset($data['filter_type']) || !isset($data['ip_address'])) {
            $this->addResponse('Please provide correct address and filter type', 1);

            return false;
        }

        if ($data['filter_type'] !== 'allow' && $data['filter_type'] !== 'block') {
            $this->addResponse('Please provide correct filter type', 1);

            return false;
        }

        $address = explode('/', $data['ip_address']);

        if (count($address) === 2 || count($address) === 1) {
            //validate address
            $validateIP = $this->validateIp($address[0]);

            if ($validateIP !== true) {
                $this->addResponse($validateIP, 1);

                return;
            }
            if (count($address) === 2) {
                $data['address_type'] = 2;
            } else if (count($address) === 1) {
                $data['address_type'] = 1;
            }
        } else {
            $this->addResponse('Please enter correct address', 1);

            return false;
        }

        // $filtersObj = new AppsIpFilter();

        // $newFilter['app_id'] = $data['app_id'];
        // $newFilter['ip_address'] = $data['ip_address'];
        // $newFilter['address_type'] = $data['address_type'];
        $data['filter_type'] = $data['filter_type'] === 'allow' ? 1 : 2;
        $data['added_by'] = $this->auth->account()['id'];

        // $filtersObj->assign($newFilter);

        try {
            $this->add($data);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $this->addResponse('Duplicate Entry!', 3);

                return false;
            }

            throw $e;
        }
    }

    public function updateFilter(array $data)
    {
        //
    }

    public function removeFilter(array $data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide correct filter ID', 1);

            return false;
        }

        // $filtersObj = new AppsIpFilter();

        // $filter = $filtersObj->findFirst('id = ' . $data['id']);

        $filter = $this->getById($data['id']);

        if ($filter->remove()) {
            $this->addResponse('Filter removed.', 0);
        } else {
            $this->addResponse('Error removing filter.', 1);
        }
    }

    public function blockMonitorFilter(array $data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide correct filter ID', 1);

            return false;
        }

        // $filtersObj = new AppsIpFilter();

        // $monitorFilterObj = $filtersObj->findFirst('id = ' . $data['id']);

        // if ($monitorFilterObj) {
        //     $monitorFilter = $monitorFilterObj->toArray();
            $monitorFilter = $this->getFirst('id', $data['id'], false, true, null, [], true);
            $monitorFilter['filter_type'] = 2;
            $monitorFilter['added_by'] = $this->auth->account()['id'];
            $monitorFilter['incorrect_attempts'] = null;

            // $monitorFilterObj->assign($monitorFilter);

            if ($this->updateToDb($monitorFilter)) {
                $this->addResponse('Filter moved from monitor to block.', 0);
            } else {
                $this->addResponse('Error blocking filter.', 1);
            }
        // }
    }

    public function resetAppFilters(array $data)
    {
        if (!isset($data['app_id'])) {
            $this->addResponse('Incorrect App ID', 1);

            return;
        }

        $app = $this->app->getFirst('id', $data['app_id']);

        $filtersObj = $app->getIpFilters();

        if ($filtersObj && $filtersObj->count() > 0) {
            $filtersObj->delete();
        }

        $app->assign(['incorrect_login_attempt_block_ip' => 0, 'ip_filter_default_action' => 0]);

        $app->update();
    }

    public function setClientAddress($clientAddress = null)
    {
        if (!$clientAddress) {
            $this->clientAddress = $this->request->getClientAddress();
        } else {
            $this->clientAddress = $clientAddress;
        }
    }

    public function getClientAddress($clientAddress = null)
    {
        $this->setClientAddress($clientAddress);

        return $this->clientAddress;
    }

    public function checkList()
    {
        $this->getClientAddress();

        if ($this->clientAddress === "127.0.0.1") {
            return true;
        }

        $app = $this->apps->getFirst('id', $this->app['id']);

        $filter = $app->getBlacklist();

        if ($filter && $filter->count() > 0) {
            if ($filter->filter_type == '2') {
                $this->bumpFilterHitCounter($filter);

                return false;
            } else if ($filter->filter_type == '1') {
                return true;
            }
        }

        $filtersObj = $app->getIpFilters(
            [
                'address_type = :address_type:',
                'bind' => [
                    'address_type' => '2',
                ]
            ]
        );

        if ($filtersObj && $filtersObj->count() > 0) {
            $filters = $filtersObj->toArray();

            foreach ($filters as $key => $filter) {
                if (\Symfony\Component\HttpFoundation\IpUtils::checkIp($this->clientAddress, $filter['ip_address'])) {
                    $appsIpFilter = new AppsIpFilter;

                    $filterObj = $appsIpFilter->findFirst(
                        [
                            'ip_address = :ip_address:',
                            'bind' => [
                                'ip_address' => $filter['ip_address'],
                            ]
                        ]
                    );

                    $this->bumpFilterHitCounter($filterObj);

                    return false;
                }
            }
        }

        if ($app->ip_filter_default_action == '1') {
            return false;
        }

        return true;
    }

    public function validateIp($ip)
    {
        $this->validation->add("ip_address", Ip::class,
            [
                "message"           => "Incorrect ip address.",
                "version"           => Ip::VERSION_4 | Ip::VERSION_6,
                "allowReserved"     => false,
                "allowPrivate"      => true,
                "allowEmpty"        => false
            ]
        );

        $validated = $this->validation->validate(["ip_address" => $ip])->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'];
            }

            return $messages;
        } else {
            return true;
        }
    }

    public function bumpFilterHitCounter($filter = null, $updateHitCount = true, $updateIncorrectAttempts = false)
    {
        if (!$this->ipFilterMiddlewareEnabled()) {
            return;
        }

        if ($updateHitCount) {
            if ($filter->hit_count !== null) {
                $hitCount = (int) $filter->hit_count;

                $hitCount = $hitCount + 1;
            } else {
                $hitCount = 1;
            }

            $filter->assign(['hit_count' => $hitCount]);

            $filter->update();

            return true;
        }

        if ($updateIncorrectAttempts) {
            if (!$filter) {
                $appsIpFilter = new AppsIpFilter;

                $filterObj = $appsIpFilter->findFirst(
                    [
                        'ip_address = :ip_address:',
                        'bind' => [
                            'ip_address' => $this->clientAddress,
                        ]
                    ]
                );

                if (!$filterObj) {
                    $newFilter =
                        [
                            'app_id'                => $this->app['id'],
                            'ip_address'            => $this->clientAddress,
                            'address_type'          => 1,
                            'filter_type'           => 3,
                            'added_by'              => 0,
                            'incorrect_attempts'    => 1
                        ];

                    $appsIpFilter->assign($newFilter);

                    $appsIpFilter->create();
                } else {
                    if ($filterObj->incorrect_attempts !== null) {
                        $incorrectAttempt = (int) $filterObj->incorrect_attempts;

                        $incorrectAttempt = $incorrectAttempt + 1;
                    } else {
                        $incorrectAttempt = 1;
                    }

                    if ($incorrectAttempt >= (int) $this->app['incorrect_login_attempt_block_ip']) {
                        $filterObj->assign(
                            [
                                'filter_type'           => 2,
                                'incorrect_attempts'    => $incorrectAttempt
                            ]
                        );
                    } else {
                        $filterObj->assign(['incorrect_attempts' => $incorrectAttempt]);
                    }

                    $filterObj->update();
                }
            }

            return true;
        }
    }

    private function ipFilterMiddlewareEnabled()
    {
        $middleware = $this->modules->middlewares->get(['name' => 'IpFilter', 'app_id' => $this->app['id']]);

        if (isset($middleware['apps'][$this->app['id']]['enabled']) &&
            $middleware['apps'][$this->app['id']]['enabled'] === true
        ) {
            return true;
        }

        return false;
    }
}