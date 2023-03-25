<?php

namespace Apps\Dash\Components\Dashboards;

use Apps\Dash\Components\Dashboards\WidgetsComponent;

class Widgets extends WidgetsComponent
{
    public function worldClock($route, $widget, $dashboardWidget)
    {
        return $this->getWidgetContent($route, $widget, $dashboardWidget);
    }
}