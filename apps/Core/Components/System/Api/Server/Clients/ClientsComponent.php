<?php

namespace Apps\Core\Components\System\Api\Server\Clients;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class ClientsComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->availableAPIServices =
                array_merge(
                    $this->api->getEnabledAPIByType('password'),
                    $this->api->getEnabledAPIByType('client_credentials')
                );

            if ($this->getData()['id'] != 0) {
                $client = $this->api->clients->getById($this->getData()['id']);

                if (!$client) {
                    return $this->throwIdNotFound();
                }

                $this->view->client = $client;
            }

            $this->view->pick('clients/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                function ($dataArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        return $this->replaceColumns($dataArr);
                    }

                    return $dataArr;
                };
        } else {
            $replaceColumns = null;
        }

       $conditions =
            [
                'order'         => 'id desc'
            ];

        $this->generateDTContent(
            $this->api->clients,
            'system/api/server/clients/view',
            $conditions,
            ['revoked', 'concurrent_calls_count', 'client_id', 'device_id', 'api_id', 'email', 'last_used'],
            true,
            ['revoked', 'concurrent_calls_count', 'client_id', 'device_id', 'api_id', 'email', 'last_used'],
            null,
            ['api_id' => 'api', 'concurrent_calls_count' => 'Calls Count'],
            $replaceColumns,
            'client_id'
        );

        $this->view->pick('clients/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $api = $this->api->getById($data['api_id']);
            if ($api) {
                $data['api_id'] = $api['name'];
            }

            if (!$data['device_id']) {
                $data['device_id'] = '-';
            }

            if ($api['is_public'] == true) {
                $data['revoked'] = '-';
                $dataRevoked = false;
            } else {
                if ($data['revoked'] == '1') {
                    $dataRevoked = true;
                    $data['revoked'] = '<span class="badge badge-primary text-uppercase">Revoked</span>';
                } else if ($data['revoked'] == '0') {
                    $dataRevoked = false;
                    $data['revoked'] =
                        '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-revoke-' . $dataKey . '" href="' . $this->links->url('system/api/server/clients/forceRevoke') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $dataKey . '" class="ml-1 mr-1 text-white btn btn-danger btn-xs rowRevoke text-uppercase" data-toggle="tooltip" data-placement="auto" title="Revoke">
                            <i class="fas fa-fw fa-xs fa-times-circle"></i>
                        </a>' .
                        '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-revokeregen-' . $dataKey . '" href="' . $this->links->url('system/api/server/clients/forceRevoke') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $dataKey . '" class="ml-1 mr-1 text-white btn btn-warning btn-xs rowRevokeRegen text-uppercase" data-toggle="tooltip" data-placement="auto" title="Revoke & Regenerate">
                            <i class="fas fa-fw fa-xs fa-sync-alt"></i>
                        </a>';
                }
            }

            $callsCounter = '';
            if ((int) $api['concurrent_calls_limit'] > 0) {
                $percent = (int) ((int) $data['concurrent_calls_count'] * 100) / (int) $api['concurrent_calls_limit'];
                if ($percent <= 50) {
                    $badge = 'success';
                } else if ($percent > 50) {
                    $badge = 'warning';
                }
                if ((int) $data['concurrent_calls_count'] === (int) $api['concurrent_calls_limit']) {
                    $badge = 'danger';
                }
                if (!$data['concurrent_calls_count']) {
                    $badge = 'secondary';
                    $data['concurrent_calls_count'] = '-';
                }
                $callsCounter = $callsCounter . '<span class="badge badge-' . $badge . ' mr-1">' . $data['concurrent_calls_count'] . '</span>';
            } else {
                $callsCounter = $callsCounter . '<span class="badge badge-secondary mr-1">-</span>';
            }
            if ((int) $api['per_minute_calls_limit'] > 0) {
                $percent = (int) ((int) $data['per_minute_calls_count'] * 100) / (int) $api['per_minute_calls_limit'];
                if ($percent <= 50) {
                    $badge = 'success';
                } else if ($percent > 50) {
                    $badge = 'warning';
                }
                if ((int) $data['per_minute_calls_count'] === (int) $api['per_minute_calls_limit']) {
                    $badge = 'danger';
                }
                if (!$data['per_minute_calls_count']) {
                    $badge = 'secondary';
                    $data['per_minute_calls_count'] = '-';
                }
                $callsCounter = $callsCounter . '<span class="badge badge-' . $badge . ' mr-1">' . $data['per_minute_calls_count'] . '</span>';
            } else {
                $callsCounter = $callsCounter . '<span class="badge badge-secondary mr-1">-</span>';
            }
            if ((int) $api['per_hour_calls_limit'] > 0) {
                $percent = (int) ((int) $data['per_hour_calls_count'] * 100) / (int) $api['per_hour_calls_limit'];
                if ($percent <= 50) {
                    $badge = 'success';
                } else if ($percent > 50) {
                    $badge = 'warning';
                }
                if ((int) $data['per_hour_calls_count'] === (int) $api['per_hour_calls_limit']) {
                    $badge = 'danger';
                }
                if (!$data['per_hour_calls_count']) {
                    $badge = 'secondary';
                    $data['per_hour_calls_count'] = '-';
                }
                $callsCounter = $callsCounter . '<span class="badge badge-' . $badge . ' mr-1">' . $data['per_hour_calls_count'] . '</span>';
            } else {
                $callsCounter = $callsCounter . '<span class="badge badge-secondary mr-1">-</span>';
            }
            if ((int) $api['per_day_calls_limit'] > 0) {
                $percent = (int) ((int) $data['per_day_calls_count'] * 100) / (int) $api['per_day_calls_limit'];
                if ($percent <= 50) {
                    $badge = 'success';
                } else if ($percent > 50) {
                    $badge = 'warning';
                }
                if ((int) $data['per_day_calls_count'] === (int) $api['per_day_calls_limit']) {
                    $badge = 'danger';
                }
                if (!$data['per_day_calls_count']) {
                    $badge = 'secondary';
                    $data['per_day_calls_count'] = '-';
                }
                $callsCounter = $callsCounter . '<span class="badge badge-' . $badge . ' mr-1">' . $data['per_day_calls_count'] . '</span>';
            } else {
                $callsCounter = $callsCounter . '<span class="badge badge-secondary mr-1">-</span>';
            }

            if (((int) $api['concurrent_calls_limit'] !== 0 ||
                (int) $api['per_minute_calls_limit'] !== 0 ||
                (int) $api['per_hour_calls_limit'] !== 0 ||
                (int) $api['per_day_calls_limit'] !== 0) &&
                $callsCounter !== '' && $dataRevoked === false
            ) {
                $callsCounter = $callsCounter .
                        '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-resetcallscounter-' . $dataKey . '" href="' . $this->links->url('system/api/server/clients/resetCallsCount') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $dataKey . '" class="ml-1 mr-1 text-white btn btn-danger btn-xs rowResetCallsCount text-uppercase" data-toggle="tooltip" data-placement="auto" title="Reset Calls Count">
                            <i class="fas fa-fw fa-xs fa-sync-alt"></i>
                        </a>';
            }

           $data['concurrent_calls_count'] = $callsCounter;
        }

        return $dataArr;
    }

    /**
     *
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->api->clients->addClient($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
    }

    /**
     *
     */
    public function updateAction()
    {
        return;
    }

    public function removeAction()
    {
        return;
    }

    public function forceRevokeAction()
    {
        $this->requestIsPost();

        $this->api->clients->forceRevoke($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
    }

    public function generateClientKeysAction()
    {
        if (isset($this->checkPermissions()['update']) && $this->checkPermissions()['update'] == 0) {
            $this->addResponse('Permission Denied', 1);

            return;
        }

        $this->requestIsPost();

        $this->api->clients->generateClientKeys($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode,
            $this->api->clients->packagesData->responseData
        );
    }

    public function generateClientIdAndSecretAction()
    {
        $this->requestIsPost();

        $this->api->clients->generateClientIdAndSecret($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode,
            $this->api->clients->packagesData->responseData
        );
    }

    public function resetCallsCountAction()
    {
        $this->requestIsPost();

        $client = null;
        $this->api->clients->resetCallsCount([], $client, $this->postData()['id']);

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
    }
}