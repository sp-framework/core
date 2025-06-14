<?php

namespace Apps\Core\Components\System\Geo\Regions;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class RegionsComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoRegions;

    public function initialize()
    {
        $this->geoRegions = $this->basepackages->geoRegions->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $regionsArr = $this->basepackages->geoRegions->getAll()->geoRegions;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $region = $this->basepackages->geoRegions->getById($this->getData()['id']);

                $this->view->region = $region;
            }

            if (!$this->view->region) {
                return $this->throwIdNotFound();
            }

            $this->view->regions = [$regionsArr[$region['parent_region_id']]];

            $this->view->pick('regions/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'      => 'system/geo/regions',
                ]
            ];


        if ($this->request->isPost()) {
            $replaceColumns =
                function ($dataArr) use ($regionsArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        return $this->replaceColumns($regionsArr, $dataArr);
                    }

                    return $dataArr;
                };
        } else {
            $replaceColumns = [];
        }

        $this->generateDTContent(
            $this->geoRegions,
            'system/geo/regions/view',
            null,
            ['name', 'parent_region_id'],
            true,
            ['name', 'parent_region_id'],
            $controlActions,
            ['parent_region_id' => 'Parent Region'],
            $replaceColumns,
            'name',
        );

        $this->view->pick('regions/list');
    }

    protected function replaceColumns($regionsArr, $dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            if (isset($data['parent_region_id']) && isset($regionsArr[$data['parent_region_id']])) {
                $data['parent_region_id'] = $regionsArr[$data['parent_region_id']]['name'];
            } else {
                $data['parent_region_id'] = '-';
            }
        }

        return $dataArr;
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
}