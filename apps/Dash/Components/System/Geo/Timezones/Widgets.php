<?php

namespace Apps\Dash\Components\System\Geo\Timezones;

use Apps\Dash\Components\Dashboards\WidgetsComponent;

class Widgets extends WidgetsComponent
{
    public function worldClock($widget, $dashboardWidget)
    {
        return $this->getWidgetContent($widget, $dashboardWidget);
    }
}