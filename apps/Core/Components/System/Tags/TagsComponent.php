<?php

namespace Apps\Core\Components\System\Tags;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class TagsComponent extends BaseComponent
{
    use DynamicTable;

    protected $tags;

    public function initialize()
    {
        $this->tags = $this->basepackages->tags->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->packages = $this->modules->packages->packages;

            if ($this->getData()['id'] != 0) {
                $tag = $this->basepackages->tags->getById((int) $this->getData()['id']);

                if (!$tag) {
                    return $this->throwIdNotFound();
                }

                $package = $this->modules->packages->getPackageByClass(str_replace('_', '\\', $tag['package_class']));

                if ($package) {
                    $tag['package_class'] = $package['name'];
                }

                $this->view->tag = $tag;
            }

            $this->view->pick('tags/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/tags',
                    'remove'    => 'system/tags/remove',
                ]
            ];

        if ($this->request->isPost()) {
            $numberOfTimesUsed = 0;

            $replaceColumns =
                function ($dataArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        foreach ($dataArr as &$data) {
                            $package = $this->modules->packages->getPackageByClass(str_replace('_', '\\', $data['package_class']));

                            if ($package) {
                                $data['package_class'] = $package['display_name'];
                            }

                            $data['package_row_ids'] = count($data['package_row_ids']);
                            $data['name'] = '<span class="badge text-sm" style="background-color: ' . $data['swatch'] . '">' . $data['name'] . '</span>';
                        }
                    }

                    return $dataArr;
                };
        } else {
            $replaceColumns = [];
        }

        $this->generateDTContent(
            package : $this->tags,
            postUrl : 'system/tags/view',
            postUrlParams : null,
            columnsForTable : ['name', 'package_class', 'swatch', 'package_row_ids'],
            columnsForFilter : ['name', 'package_class', 'swatch'],
            controlActions : $controlActions,
            dtReplaceColumnsTitle : ['package_row_ids' => '# of times used'],
            dtReplaceColumns :$replaceColumns,
            dtNotificationTextFromColumn :'name',
            excludeColumns : ['swatch']
        );

        $this->view->pick('tags/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->tags->addTag($this->postData());

        $this->addResponse(
            $this->tags->packagesData->responseMessage,
            $this->tags->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->tags->updateTag($this->postData());

        $this->addResponse(
            $this->tags->packagesData->responseMessage,
            $this->tags->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->tags->removeTag($this->postData());

        $this->addResponse(
            $this->tags->packagesData->responseMessage,
            $this->tags->packagesData->responseCode
        );
    }
}