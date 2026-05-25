<?php

namespace Apps\Core\Components\System\Menus;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class MenusComponent extends BaseComponent
{
    use DynamicTable;

    protected $menus;

    public function initialize()
    {
        $this->menus = $this->basepackages->menus;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->appTypes = $this->apps->types->getInstalledAppTypes();

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $menu = $this->menus->getById($this->getData()['id']);

                if (!$menu) {
                    return $this->throwIdNotFound();
                }

                $this->view->menu = $menu;
            }

            $this->view->pick('menus/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'              => 'system/menus',
                    'remove'            => 'system/menus/remove',
                ]
            ];

        $this->generateDTContent(
            $this->menus,
            'system/menus/view',
            null,
            ['route', 'app_type'],
            true,
            ['route', 'app_type'],
            $controlActions,
            null,
            null,
            'route'
        );

        $this->view->pick('menus/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        //
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        //
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $removeMenu = $this->menus->removeMenu($this->postData());

        $this->addResponse(
            $this->menus->packagesData->responseMessage,
            $this->menus->packagesData->responseCode
        );
    }
}