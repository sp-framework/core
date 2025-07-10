<?php

namespace Apps\Core\Components\System\Geo\Timezones;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class TimezonesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoTimezones;

    public function initialize()
    {
        $this->geoTimezones = $this->basepackages->geoTimezones->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $timezone = $this->basepackages->geoTimezones->getById($this->getData()['id']);

                $this->view->timezone = $timezone;
            }

            if (!$this->view->timezone) {
                return $this->throwIdNotFound();
            }

            $this->view->pick('timezones/view');

            return;
        }

        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'view'      => 'system/geo/timezones',
                ]
            ];

        $this->generateDTContent(
            $this->geoTimezones,
            'system/geo/timezones/view',
            null,
            ['zone_name', 'tz_name', 'gmt_offset', 'gmt_offset_name', 'abbreviation', 'gmt_offset_dst', 'gmt_offset_name_dst', 'abbreviation_dst'],
            true,
            ['zone_name', 'tz_name', 'gmt_offset', 'gmt_offset_name', 'abbreviation', 'gmt_offset_dst', 'gmt_offset_name_dst', 'abbreviation_dst'],
            $controlActions,
            ['gmt_offset'=>'gmt offset (secs)','gmt_offset_dst'=>'gmt offset DST (secs)','tz_name'=>'time zone name'],
            null,
            'zone_name'
        );

        $this->view->pick('timezones/list');
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