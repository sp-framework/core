<?php

namespace Apps\Core\Components\System\Geo\Timezones;

use System\Base\Providers\ModulesServiceProvider\Modules\Components\ComponentsWidgets;

class Widgets extends ComponentsWidgets
{
    public function worldClock($widget, $dashboardWidget)
    {
        return $this->getWidgetContent($widget, $dashboardWidget);
    }
}