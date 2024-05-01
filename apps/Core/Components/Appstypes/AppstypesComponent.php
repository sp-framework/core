<?php

namespace Apps\Core\Components\Appstypes;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;
use System\Base\Providers\AppsServiceProvider\Types;

class AppstypesComponent extends BaseComponent
{
    use DynamicTable;

    public function initialize()
    {
        $this->appsTypes = $this->usePackage(Types::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $appsType = $this->appsTypes->getAppTypeById($this->getData()['id']);

                if (!$appsType) {
                    return $this->throwIdNotFound();
                }

                $this->view->appsType = $appsType;
            }

            $this->view->pick('appstypes/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'appstypes'
                ]
            ];

        $this->generateDTContent(
            $this->appsTypes,
            'appstypes/view',
            null,
            ['name', 'app_type'],
            true,
            [],
            $controlActions,
            null,
            [],
            'name',
            null
        );

        $this->view->pick('appstypes/list');
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

            $this->appsTypes->updateAppsType($this->postData());

            $this->addResponse(
                $this->apps->packagesData->responseMessage,
                $this->apps->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}