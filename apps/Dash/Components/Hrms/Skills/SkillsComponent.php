<?php

namespace Apps\Dash\Components\Hrms\Skills;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Hrms\Skills\Skills;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class SkillsComponent extends BaseComponent
{
    use DynamicTable;

    protected $skills;

    public function initialize()
    {
        $this->skills = $this->usePackage(Skills::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $this->view->skill = $this->skills->getById($this->getData()['id']);
            }

            $this->view->pick('skills/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'hrms/skills',
                    'remove'    => 'hrms/skills/remove',
                ]
            ];

        $this->generateDTContent(
            $this->skills,
            'hrms/skills/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('skills/list');
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

            $this->skills->addSkills($this->postData());

            $this->view->responseCode = $this->skills->packagesData->responseCode;

            $this->view->responseMessage = $this->skills->packagesData->responseMessage;

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

            $this->skills->updateSkills($this->postData());

            $this->view->responseCode = $this->skills->packagesData->responseCode;

            $this->view->responseMessage = $this->skills->packagesData->responseMessage;

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

            $this->skills->removeSkills($this->postData());

            $this->view->responseCode = $this->skills->packagesData->responseCode;

            $this->view->responseMessage = $this->skills->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchSkillAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 2) {
                    return;
                }

                $searchSkill = $this->skills->searchSkills($searchQuery);

                if ($searchSkill) {
                    $this->view->responseCode = $this->skills->packagesData->responseCode;

                    $this->view->skills = $this->skills->packagesData->skills;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}