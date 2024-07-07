<?php

namespace System\Base\Providers\AppsServiceProvider;

use Phalcon\Filter\Validation\Validator\Ip;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Exceptions\IpFilterBlockedException;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderAppsIpFilter;

class IpFilter extends BasePackage
{
    protected $modelToUse = ServiceProviderAppsIpFilter::class;

    protected $packageName = 'ipfilter';

    protected $packageNameS = 'ipfilter';

    public $clientAddress;

    protected $apps;

    protected $app;

    public function init($apps = null, $app = null)
    {
        $this->apps = $apps;

        $this->app = $app;

        return $this;
    }

    public function getFilters(array $data)
    {
        $filters = [];

        if (!isset($data['app_id'])) {
            return $this->helper->encode([]);//blank array
        }

        if ($this->config->databasetype === 'db') {
            $app = $this->apps->getFirst('id', (int) $data['app_id']);

            $filtersObj = $app->getIpFilters();

            if ($filtersObj && $filtersObj->count() > 0) {
                $filters = $filtersObj->toArray();
            }
        } else {
            $this->apps->setFFRelations(true);

            $this->apps->setFFRelationsConditions(['monitorlist' => ['ip_address', '=', $this->getDi()->getRequest()->getClientAddress()]]);

            $app = $this->apps->getFirst('id', (int) $data['app_id']);

            if ($app->data['ipFilters'] && count($app->data['ipFilters']) > 0) {
                $filters = $app->data['ipFilters'];
            }
        }

        return $filters;
    }

    public function addFilter(array $data)
    {
        if (!isset($data['filter_type']) || !isset($data['ip_address'])) {
            $this->addResponse('Please provide correct address and filter type', 1);

            return false;
        }

        if ($data['filter_type'] !== 'allow' &&
            $data['filter_type'] !== 'block' &&
            $data['filter_type'] !== 'monitor'
        ) {
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

        if ($data['filter_type'] === 'allow') {
            $data['filter_type'] = 1;
        } else if ($data['filter_type'] === 'block') {
            $data['filter_type'] = 2;
        } else if ($data['filter_type'] === 'monitor') {
            $data['filter_type'] = 3;
        }
        $data['added_by'] = $this->auth->account()['id'];

        try {
            $this->add($data);

            $this->addResponse('Filter Added.');
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

        $filter = $this->getById($data['id']);

        if ($this->remove($filter['id'])) {
            $this->addResponse('Filter removed.');
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

        $monitorFilter = $this->getFirst('id', $data['id'], false, true, null, [], true);
        $monitorFilter['filter_type'] = 2;
        $monitorFilter['added_by'] = $this->auth->account()['id'];
        $monitorFilter['incorrect_attempts'] = null;

        if ($this->update($monitorFilter)) {
            $this->addResponse('Filter moved from monitor to block.', 0);
        } else {
            $this->addResponse('Error blocking filter.', 1);
        }
    }

    public function resetAppFilters(array $data)
    {
        if (!isset($data['app_id'])) {
            $this->addResponse('Incorrect App ID', 1);

            return;
        }

        if ($this->config->databasetype === 'db') {
            $app = $this->apps->getFirst('id', $data['app_id']);

            $filtersObj = $app->getIpFilters();

            if ($filtersObj && $filtersObj->count() > 0) {
                $filtersObj->delete();
            }

            $app->assign(['incorrect_login_attempt_block_ip' => 0, 'ip_filter_default_action' => 'allow']);

            $app->update();

            return true;
        } else {
            $this->apps->setFFRelations(true);

            $app = $this->apps->getFirst('id', (int) $data['app_id']);

            if ($app->data['ipFilters'] && count($app->data['ipFilters']) > 0) {
                foreach ($app->data['ipFilters'] as $filter) {
                    $this->removeFilter(['id' => $filter['id']]);
                }
            }

            $app = $app->toArray();

            $app['incorrect_login_attempt_block_ip'] = 0;
            $app['ip_filter_default_action'] = 'allow';

            $this->apps->update($app);

            return true;
        }

        $this->addResponse('Incorrect App ID', 1);

        return;
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
        if (!$this->clientAddress) {
            $this->setClientAddress($clientAddress);
        }

        return $this->clientAddress;
    }

    public function checkList()
    {
        $this->getClientAddress();

        if ($this->clientAddress === "127.0.0.1") {
            return true;
        }

        $this->apps->setFFRelations(true);
        $this->apps->setFFRelationsConditions(['monitorlist' => ['ip_address', '=', $this->getDi()->getRequest()->getClientAddress()]]);

        $app = $this->apps->getFirst('id', $this->app['id']);

        $filterStore = $this->ff->store($this->useModel()->getSource());

        $filter = [];

        if ($this->config->databasetype === 'db') {
            $filterObj = $app->getMonitorlist();

            if ($filterObj) {
                $filter = $filterObj->toArray();
            }
        } else {
            $app = $app->toArray();

            $filter = $app['monitorlist'];
        }

        if ($filter && count($filter) > 0) {
            if ($filter['filter_type'] == '2') {
                if ($this->config->databasetype === 'db') {
                    $this->bumpFilterHitCounter($filterObj);
                } else {
                    $filterStore->findById($filter['id']);

                    $this->bumpFilterHitCounter($filterStore);
                }

                return false;
            } else if ($filter['filter_type'] == '1') {
                return true;
            }
        }

        $filters = [];

        if ($this->config->databasetype === 'db') {
            $filtersObj = $app->getIpFilters(
                [
                    'address_type = :address_type:',
                    'bind' => [
                        'address_type' => '2',
                    ]
                ]
            );

            if ($filtersObj) {
                $filters = $filtersObj->toArray();
            }
        } else {
            $filters = $filterStore->findBy(['address_type', '=', (int) 2]);
        }

        if ($filters && count($filters) > 0) {
            foreach ($filters as $key => $filter) {
                if (\Symfony\Component\HttpFoundation\IpUtils::checkIp($this->clientAddress, $filter['ip_address'])) {
                    if ($this->config->databasetype === 'db') {
                        $filterObj = $this->getFirst('ip_address', $filter['ip_address']);

                        $this->bumpFilterHitCounter($filterObj);

                        if ($filterObj->filter_type == '2') {
                            return false;
                        }
                    } else {
                        if ($filterStore->findOneBy(['ip_address', '=', $filter['ip_address']])) {
                            $this->bumpFilterHitCounter($filterStore);

                            if ($filterStore->data['filter_type'] == '2') {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        if ($this->config->databasetype === 'db') {
            if ($app->ip_filter_default_action === 'block') {
                return false;
            }
        } else {
            if ($app['ip_filter_default_action'] === 'block') {
                return false;
            }
        }

        return true;
    }

    public function validateIp($ip)
    {
        $this->validation->init()->add("ip_address", Ip::class,
            [
                "message"           => "Incorrect ip address.",
                "version"           => Ip::VERSION_4 | Ip::VERSION_6,
                "allowReserved"     => true,
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

    public function bumpFilterHitCounter($filterObj = null, $updateHitCount = true, $updateIncorrectAttempts = false, $appRoute = null)
    {
        if (!$this->ipFilterMiddlewareEnabled($appRoute)) {
            return;
        }

        if ($updateHitCount) {
            $filter = $filterObj->toArray();

            if ($filter['hit_count'] !== null) {
                $hitCount = (int) $filter['hit_count'];

                $hitCount = $hitCount + 1;
            } else {
                $hitCount = 1;
            }

            $filter['hit_count'] = $hitCount;

            $this->update($filter);

            return true;
        }

        if ($updateIncorrectAttempts) {
            if (!$filterObj) {
                $filter = [];

                $filterObj = $this->getFirst('ip_address', $this->clientAddress);

                if ($filterObj) {
                    $filter = $filterObj->toArray();
                }

                if (count($filter) === 0) {
                    $newFilter =
                        [
                            'app_id'                => $this->app['id'],
                            'ip_address'            => $this->clientAddress,
                            'address_type'          => 1,
                            'filter_type'           => 'monitor',
                            'added_by'              => 0,
                            'incorrect_attempts'    => 1
                        ];

                    $this->addFilter($newFilter);
                } else {
                    if ($filter['incorrect_attempts'] !== null) {
                        $incorrectAttempt = (int) $filter['incorrect_attempts'];

                        $incorrectAttempt = $incorrectAttempt + 1;
                    } else {
                        $incorrectAttempt = 1;
                    }

                    if ($incorrectAttempt >= (int) $this->app['incorrect_login_attempt_block_ip']) {
                        $filter['filter_type'] = 2;
                    }

                    $filter['incorrect_attempts'] = $incorrectAttempt;

                    $this->update($filter);
                }
            }

            return true;
        }
    }

    public function removeFromMonitoring()
    {
        if (!$this->ipFilterMiddlewareEnabled()) {
            return;
        }

        $this->apps->setFFRelations(true);

        $app = $this->apps->getFirst('id', $this->app['id']);

        $filterStore = $this->ff->store($this->useModel()->getSource());

        if ($this->config->databasetype === 'db') {
            $filter = $app->getMonitorlist();

            if ($filter && $filter->count() > 0) {
                $filter->delete();
            }
        } else {
            $filter = $filterStore->findOneBy(['ip_address', '=', $this->getDi()->getRequest()->getClientAddress()]);

            if ($filter && count($filter) > 0) {
                $filterStore->deleteById($filter['id']);
            }
        }
    }

    private function ipFilterMiddlewareEnabled($appRoute = null)
    {
        $middleware = $this->modules->middlewares->getMiddlewareByNameForAppId('IpFilter', $this->apps->getAppInfo($appRoute)['id']);

        if (isset($middleware['apps'][$this->app['id']]['enabled']) &&
            $middleware['apps'][$this->app['id']]['enabled'] === true
        ) {
            return true;
        }

        return false;
    }
}