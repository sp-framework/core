<?php

namespace System\Base\Providers\AppsServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderAppsTypes;

class Types extends BasePackage
{
    protected $modelToUse = ServiceProviderAppsTypes::class;

    protected $packageName = 'types';

    public $types;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function getAppTypeById($id)
    {
        foreach($this->types as $type) {
            if ($type['id'] == $id) {
                return $type;
            }
        }

        return false;
    }

    public function getAppTypeByType($app_type)
    {
        foreach($this->types as $type) {
            if ($type['app_type'] === strtolower($app_type)) {
                return $type;
            }
        }

        return false;
    }

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function updateAppsType(array $data)
    {
        $appType = $this->getAppTypeById($data['id']);

        $appType['name'] = $data['name'];
        $appType['description'] = $data['description'];

        if ($this->update($appType)) {
            $this->addResponse('Updated ' . $appType['name'] . ' app type');
        } else {
            $this->addResponse('Error updating app type.', 1);
        }
    }
}