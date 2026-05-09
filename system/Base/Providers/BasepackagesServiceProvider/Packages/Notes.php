<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;

class Notes extends BasePackage
{
    protected $modelToUse = BasepackagesNotes::class;

    protected $packageName = 'notes';

    public $notes;

    public $notesSettings = null;

    public function init(bool $resetCache = false)
    {
        $notesSettings = $this->modules->packages->getPackageByName($this->packageName);

        if ($notesSettings) {
            $this->notesSettings = $this->helper->decode($notesSettings['settings'], true);

            if (!isset($this->notesSettings['noteTypes'])) {
                $this->notesSettings['noteTypes'] = $this->setNotesTypes;
            }
        } else {
            $newSettings['package_name'] = 'Notes';
            $newSettings['settings'] = [];
            $newSettings['settings']['noteTypes'] = $this->setNotesTypes();
            $newSettings['settings']['useStorage'] = 'private';
            $newSettings['settings']['allowedUploads'] = true;

            $this->modules->packages->update($newSettings);

            $this->notesSettings = $newSettings['settings'];
        }

        return $this;
    }

    public function getNotesSettings()
    {
        if (!$this->notesSettings) {
            $this->init();
        }

        return $this->notesSettings;
    }

    protected function setNotesTypes()
    {
        return
            [
                [
                    'id'    => '1',
                    'name'  => 'General'
                ],
                [
                    'id'    => '2',
                    'name'  => 'Phone In'
                ],
                [
                    'id'    => '3',
                    'name'  => 'Phone Out'
                ],
                [
                    'id'    => '4',
                    'name'  => 'Email In'
                ],
                [
                    'id'    => '5',
                    'name'  => 'Email Out'
                ],
            ];
    }

    public function addNote($packageName, array $data)
    {
        //Notes need settings, like:
        //What default permission for notes when added via system.
        //What permission for notes when added via email-in.
        //Duplicate detection for email-in notes.
        //HTML Notes??
        $data = $this->extractData($data);

        if (isset($data['note_type']) && isset($data['note'])) {
            if ($data['note_type'] != 0 && $data['note_type'] != '') {

                if ($data['note'] === '') {
                    return;//Nothing to add
                }

                if (isset($data['note_app_visibility']) && $data['note_app_visibility'] !== '') {
                    if (is_array($data['note_app_visibility'])) {
                        $note['note_app_visibility'] = $this->helper->encode($data['note_app_visibility']['data']);
                    } else {
                        $data['note_app_visibility'] = $this->helper->decode($data['note_app_visibility'], true);
                        $note['note_app_visibility'] = $this->helper->encode($data['note_app_visibility']['data']);
                    }
                }

                $note['note_type'] = $data['note_type'];

                if (isset($this->access->auth)) {
                    $account = $this->access->auth->account();

                    $note['account_id'] = $account['id'];//User
                } else {
                    $note['account_id'] = 0;//System
                }

                $note['package_name'] = $packageName;

                if (isset($data['id'])) {
                    $note['package_row_id'] = $data['id'];
                } else if (isset($data['package_row_id'])) {
                    $note['package_row_id'] = $data['package_row_id'];
                }

                if (isset($data['is_private'])) {
                    $note['is_private'] = $data['is_private'];
                } else {
                    $note['is_private'] = '0';
                }

                $note['note'] = $data['note'];

                if (isset($data['note_attachments']) && $data['note_attachments'] !== '') {
                    if (!is_array($data['note_attachments'])) {
                        $data['note_attachments'] = $this->helper->decode($data['note_attachments'], true);
                    }

                    foreach ($data['note_attachments'] as $attachmentKey => $attachment) {
                        $this->basepackages->storages->changeOrphanStatus($attachment);
                    }

                    $note['note_attachments'] = $this->helper->encode($data['note_attachments']);
                }

                if ($this->add($note, false)) {
                    $this->packagesData->responseCode = 0;

                    $this->packagesData->responseMessage = 'Note Added';
                } else {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error Adding Note';
                }
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = "Notes parameters missing";

            return;
        }
    }

    public function getNotes($packageName, int $packageRowId, bool $newFirst = true, $page = 1)
    {
        $notesArr = [];

        if ($newFirst) {
            $order = 'id desc';
        } else {
            $order = 'id asc';
        }

        $pagedNotes = $this->getPaged(
            [
                'conditions'    => '-|package_name|equals|' . $packageName . '&and|package_row_id|equals|' . $packageRowId . '&',
                'order'         => $order,
                'limit'         => 10,
                'page'          => $page
            ]
        );

        if ($pagedNotes) {
            $notesArr = $pagedNotes->getItems();
        }

        if (count($notesArr) > 0) {
            foreach ($notesArr as $key => &$note) {
                unset($note['id']);
                unset($note['package_name']);
                unset($note['package_row_id']);

                if ($note['account_id'] != 0) {
                    $account = $this->basepackages->accounts->getById($note['account_id']);
                    $note['account_email'] = $account['email'];

                    $profile = $this->basepackages->profiles->getProfile($note['account_id']);

                    if ($profile) {
                        $note['account_full_name'] = $profile['full_name'];
                    } else {
                        $note['account_full_name'] = 'User Unknown';
                    }

                    if ($note['is_private'] == '1' &&
                        $note['account_id'] != $this->access->auth->account()['id']
                    ) {
                        unset($notesArr[$key]);
                    }
                    unset($note['account_id']);
                } else {
                    $note['account_email'] = 'N/A';
                    $note['account_full_name'] = 'System';
                }

                if ($note['note_app_visibility'] &&
                    $note['note_app_visibility'] !== '' &&
                    $note['note_app_visibility'] !== '[]'
                ) {
                    $note['note_app_visibility'] = $this->helper->decode($note['note_app_visibility'], true);

                    if (is_array($note['note_app_visibility']) && count($note['note_app_visibility']) > 0) {
                        foreach ($note['note_app_visibility'] as $appKey => $app) {
                            $appInfo = $this->apps->getAppById($app);
                            $note['note_app_visibility'][$appKey] = $appInfo['name'];
                        }
                    }
                }

                if ($note['note_attachments'] && $note['note_attachments'] !== '') {
                    $note['note_attachments'] = $this->helper->decode($note['note_attachments'], true);
                    if (is_array($note['note_attachments']) && count($note['note_attachments']) > 0) {
                        foreach ($note['note_attachments'] as $attachmentKey => $attachment) {
                            $attachmentInfo = $this->basepackages->storages->getFileInfo($attachment);
                            if ($attachmentInfo) {
                                if ($attachmentInfo['links']) {
                                    $attachmentInfo['links'] = $this->helper->decode($attachmentInfo['links'], true);
                                }
                                $note['note_attachments'][$attachmentKey] = $attachmentInfo;
                            }
                        }
                    }
                }
            }

            if ($this->packagesData->paginationCounters) {
                $notesArr = array_replace($notesArr, ['paginationCounters' => $this->packagesData->paginationCounters]);
            }

            return $notesArr;
        }

        return [];
    }

    public function removeNotes($packageName, $packageRowId)
    {
        //
    }

    protected function extractData(array $data)
    {
        $columns = array_keys($this->getModelsColumnMap()['columns']);

        foreach ($data as $key => $value) {
            if (!in_array($key, $columns)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}