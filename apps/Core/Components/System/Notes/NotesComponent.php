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
        $this->requestIsPost();

        $this->notes->addNote($this->postData()['package_name'], $this->postData());

        $this->addResponse(
            $this->notes->packagesData->responseMessage,
            $this->notes->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->notes->updateNote($this->postData());

        $this->addResponse(
            $this->notes->packagesData->responseMessage,
            $this->notes->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->app['id'] === 1) {
            $this->requestIsPost();

            $this->notes->removeNote($this->postData());

            $this->addResponse(
                $this->notes->packagesData->responseMessage,
                $this->notes->packagesData->responseCode
            );
        }
    }
}