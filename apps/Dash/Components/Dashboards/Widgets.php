<?php

namespace Apps\Dash\Components\Dashboards;

use Apps\Dash\Components\Dashboards\WidgetsComponent;

class Widgets extends WidgetsComponent
{
    public function worldClock($route, $widgetMethod)
    {
        return $this->getWidgetContent($route, $widgetMethod, ['name' => 'Guru']);
    }
}