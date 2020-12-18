<?php

namespace Applications\Ecom\Dashboard\Packages\Channels;

use Applications\Ecom\Dashboard\Packages\Channels\Model\Channels as ChannelsModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Channels extends BasePackage
{
    protected $modelToUse = ChannelsModel::class;

    protected $packageName = 'channels';

    public $channels;

    public function addChannel(array $data)
    {
        if ($data['type'] === 'eshop') {
            $data = $this->getEcomChannelSettings($data);
        }

        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' channel';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new channel.';
        }
    }

    public function getEcomChannelSettings(array $data)
    {
        $data['settings']['application_id'] = $data['application_id'];
        $data['settings']['domain_id'] = $data['domain_id'];
        $data['settings'] = Json::encode($data['settings']);

        return $data;
    }

    public function updateChannel(array $data)
    {
        if ($data['type'] === 'eshop') {
            $data = $this->getEcomChannelSettings($data);
        }

        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' channel';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating channel.';
        }
    }


    public function removeChannel(array $data)
    {
        //Check relations before removing.
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed channel';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing channel.';
        }
    }
}