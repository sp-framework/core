<?php

namespace Apps\Core\Components\System\Notes;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class NotesComponent extends BaseComponent
{
    use DynamicTable;

    protected $notes;

    public function initialize()
    {
        $this->notes = $this->basepackages->notes;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $notes = $this->notes->getNotes($this->getData()['id']);

                $this->view->notes = $notes;
            }
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/notes',
                    'remove'    => 'system/notes/remove'
                ]
            ];

        $this->generateDTContent(
            $this->notes,
            'system/notes/view',
            null,
            ['[note_type]', '[note_app_visibility]', 'account_id', 'is_private'],
            true,
            ['[note_type]', '[note_app_visibility]', 'account_id', 'is_private'],
            $controlActions,
            null,
            null,
            'id'
        );

        $this->view->pick('notes/list');
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

            $this->notes->addNote($this->postData()['package_name'], $this->postData());

            $this->view->responseCode = $this->notes->packagesData->responseCode;

            $this->view->responseMessage = $this->notes->packagesData->responseMessage;

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

            $this->notes->updateNote($this->postData());

            $this->view->responseCode = $this->notes->packagesData->responseCode;

            $this->view->responseMessage = $this->notes->packagesData->responseMessage;

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
        if ($this->app['id'] === 1) {
            if ($this->request->isPost()) {
                if (!$this->checkCSRF()) {
                    return;
                }

                $this->notes->removeNote($this->postData());

                $this->view->responseCode = $this->notes->packagesData->responseCode;

                $this->view->responseMessage = $this->notes->packagesData->responseMessage;

            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'Method Not Allowed';
            }
        }
    }
}