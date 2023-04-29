<?php

namespace Apps\Core\Components\System\Geo\Timezones;

use Apps\Core\Components\Dashboards\WidgetsComponent;

class Widgets extends WidgetsComponent
{
    public function worldClock($widget, $dashboardWidget)
    {
        return $this->getWidgetContent($widget, $dashboardWidget);
    }
}