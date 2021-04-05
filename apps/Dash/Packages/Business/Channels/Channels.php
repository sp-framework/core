<?php

namespace Apps\Dash\Packages\Business\Channels;

use Apps\Dash\Packages\Business\Channels\Model\BusinessChannels;
use Apps\Dash\Packages\Business\Channels\Model\BusinessChannelsEbay;
use Apps\Dash\Packages\Business\Channels\Model\BusinessChannelsEshop;
use Apps\Dash\Packages\Business\Channels\Model\BusinessChannelsPos;
use Apps\Dash\Packages\System\Api\Api;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Exceptions\IdNotFoundException;

class Channels extends BasePackage
{
    protected $modelToUse = BusinessChannels::class;

    protected $packageName = 'channels';

    public $channels;

    public function getChannelById(int $id, bool $resetCache = false, bool $enableCache = true)
    {
        if ($id) {
            if ($enableCache) {
                $parameters = $this->paramsWithCache($this->getIdParams($id));
            } else {
                $parameters = $this->getIdParams($id);
            }

            if (!$this->config->cache->enabled) {
                $parameters = $this->getIdParams($id);
            }

            $this->model = $this->modelToUse::find($parameters);

            $channel = $this->getDbData($parameters, $enableCache);

            if ($channel) {
                $channel = $this->initChannelType($channel);

                $this->model = $this->modelToUse::find($channel['channel_id']);

                $channelData = $this->getDbData($parameters, $enableCache);

                if ($channelData) {
                    unset($channelData['id']);
                    $channel = array_merge($channel, $channelData);
                }

                return $channel;
            } else {
                throw new IdNotFoundException;
            }
        }

        throw new \Exception('getById needs id parameter to be set.');
    }

    public function init()
    {
        $this->modelToUse = BusinessChannels::class;

        $this->packageName = 'channels';

        return $this;
    }

    protected function initChannelType($data)
    {
        if ($data) {
            if ($data['channel_type'] === 'eshop') {
                $this->modelToUse = BusinessChannelsEshop::class;

                $this->packageName = 'channelEshop';

            } else if ($data['channel_type'] === 'ebay') {
                $this->modelToUse = BusinessChannelsEbay::class;

                $this->packageName = 'channelEbay';
            } else if ($data['channel_type'] === 'pos') {
                $this->modelToUse = BusinessChannelsPos::class;

                $this->packageName = 'channelPos';
            }

            return $data;
        }
    }

    public function addChannel(array $data)
    {
        $data['channel_type'] = strtolower($data['channel_type']);

        $data = $this->initChannelType($data);

        $channelData = $data;

        if ($this->add($channelData)) {
            $data['channel_id'] = $this->packagesData->last['id'];

            $this->init();

            if ($this->add($data)) {

                if ($data['channel_type'] === 'ebay') {
                    $apiPackage = $this->usePackage(Api::class);

                    $api = $apiPackage->getById($channelData['api_id']);

                    $api['in_use'] = 1;

                    $api['used_by'] = 'Channel (' . $data['name'] . ')';

                    $apiPackage->update($api);
                }

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' Channel';

                return true;
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error adding new Channel.';
            }
        }
    }

    public function updateChannel(array $data)
    {
        $data['channel_type'] = strtolower($data['channel_type']);

        $channelData = $this->getById($data['id']);

        $channelData = array_merge($channelData, $data);

        if ($this->update($channelData)) {
            $channelData = $this->initChannelType($channelData);

            $channelData['id'] = $channelData['channel_id'];

            if ($this->update($channelData)) {

                if ($data['channel_type'] === 'ebay') {
                    $apiPackage = $this->usePackage(Api::class);

                    $api = $apiPackage->getById($channelData['api_id']);

                    $api['in_use'] = 1;

                    $api['used_by'] = 'Channel (' . $data['name'] . ')';

                    $apiPackage->update($api);
                }

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' Channel';

                return true;
            }

        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating Channel.';
        }
    }

    public function removeChannel(array $data)
    {
        $channel = $this->getById($data['id']);

        if ($channel['product_count'] || $channel['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Channel has products assigned to it. Error removing channel.';

            return;
        } else if ($channel['order_count'] || $channel['order_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Channel has orders assigned to it. Error removing channel.';

            return;
        }

        $this->initChannelType($channel);

        if ($channel['channel_type'] === 'ebay') {
            $apiPackage = $this->usePackage(Api::class);

            $channelData = $this->getById($channel['channel_id']);

            $api = $apiPackage->getById($channelData['api_id']);

            $api['in_use'] = 0;

            $api['used_by'] = '';

            $apiPackage->update($api);
        }

        if ($this->remove($channel['channel_id'])) {

            $this->init();

            if ($this->remove($data['id'])) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed Channel';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing Channel.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing Channel.';
        }
    }

    public function getChannelByType($type)
    {
        $this->getAll();

        $filter =
            $this->model->filter(
                function($channel) use ($type) {
                    $channel = $channel->toArray();

                    if ($channel['channel_type'] === $type) {
                        return $channel;
                    }
                }
            );

        return $filter;
    }
}