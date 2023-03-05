<?php

namespace System\Base\Providers\AppsServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Ip;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Exceptions\IpFilterBlockedException;
use System\Base\Providers\AppsServiceProvider\Model\AppsIpFilter;

class IpFilter extends BasePackage
{
    public $clientAddress;

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

        // if ($this->clientAddress === "127.0.0.1") {
        //     return true;
        // }

        $app = $this->apps->getFirst('id', $this->apps->app['id']);

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
                            'app_id'                => $this->apps->app['id'],
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

                    if ($incorrectAttempt >= (int) $this->apps->app['incorrect_login_attempt_block_ip']) {
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
        $middleware = $this->modules->middlewares->getNamedMiddlewareForApp('IpFilter', $this->apps->app['id']);

        if (isset($middleware['apps'][$this->apps->app['id']]['enabled']) &&
            $middleware['apps'][$this->apps->app['id']]['enabled'] === true
        ) {
            return true;
        }

        return false;
    }
}