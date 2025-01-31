<?php

namespace Apps\Core\Components\System\Geo\Timezones;

use Carbon\Carbon;
use System\Base\Providers\ModulesServiceProvider\Modules\Components\ComponentsWidgets;

class Widgets extends ComponentsWidgets
{
    public function worldClock($widget, $dashboardWidget)
    {
        $timezonesArr = $this->componentObj->getDi()->getShared('basepackages')->geoTimezones->getAll()->geoTimezones;
        $timezones = [];

        foreach ($timezonesArr as $timezone) {
            $tzId = strtolower(str_replace('/', '', $timezone['zone_name']));

            $tzname = &$timezones[$tzId];
            $tzname['id'] = $tzId;
            $tzname['name'] = $timezone['zone_name'];
            $tzname['gmt_offset'] = $timezone['gmt_offset'];
            $tzname['gmt_offset_dst'] = $timezone['gmt_offset_dst'];
            $tzname['abbreviation'] = $timezone['abbreviation'];

            $tzname['isDST'] = false;
            $tzname['abbreviation_dst'] = '-';

            if ($timezone['gmt_offset'] != $timezone['gmt_offset_dst']) {
                $tzname['isDST'] = Carbon::now($timezone['zone_name'])->isDST();
                $tzname['abbreviation_dst'] = $timezone['abbreviation_dst'];
            }
        }

        $widget['settings']['clocks'] = $timezones;

        return $this->getWidgetContent($widget, $dashboardWidget);
    }
}