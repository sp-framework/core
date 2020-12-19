<?php

namespace Applications\Ecom\Dashboard\Components\Channels;

use Applications\Ecom\Admin\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Admin\Packages\Channels\Channels;
use Applications\Ecom\Admin\Packages\Locations\Model\Locations;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ChannelsComponent extends BaseComponent
{
    use DynamicTable;

    protected $channels;

    public function initialize()
    {
        $this->channels = $this->usePackage(Channels::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // $locations = $this->modelsManager->executeQuery('SELECT * FROM Applications\Ecom\Admin\Packages\Locations\Model\Locations');

        // foreach ($locations as $key => $value) {
        //     var_dump($value->toArray());
        // }

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $channel = $this->channels->getById($this->getData()['id']);

                $channel['settings'] = Json::decode($channel['settings'], true);

                $this->view->channel = $channel;

                $this->view->channelType = $channel['type'];
            } else {
                $this->view->channelType = $this->getData()['type'];
            }

            $this->view->domains = $this->basepackages->domains->domains;

            $this->view->applications = $this->modules->applications->applications;

            $this->view->responseCode = $this->channels->packagesData->responseCode;

            $this->view->responseMessage = $this->channels->packagesData->responseMessage;

            $this->view->pick('channels/view');

            return;
        }

        $channels = $this->channels->init();

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'channels',
                    'remove'    => 'channels/remove'
                ]
            ];

        $this->generateDTContent(
            $channels,
            'channels/view',
            null,
            ['name', 'type'],
            true,
            ['name', 'type'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('channels/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->channels->addChannel($this->postData());

            $this->view->responseCode = $this->channels->packagesData->responseCode;

            $this->view->responseMessage = $this->channels->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->channels->updateChannel($this->postData());

            $this->view->responseCode = $this->channels->packagesData->responseCode;

            $this->view->responseMessage = $this->channels->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->channels->removeChannel($this->postData());

            $this->view->responseCode = $this->channels->packagesData->responseCode;

            $this->view->responseMessage = $this->channels->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}