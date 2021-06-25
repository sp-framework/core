<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Arr;

class Activitylogs extends AdminLTETags
{
    protected $params;

    protected $activityLogsParams;

    protected $content = '';

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->activityLogsParams = [];

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        $this->content .=
            '<div class="row">
                <div class="col">
                    <div class="timeline">';

        foreach ($this->params['activityLogs'] as $logsKey => $logs) {
            if ($logs['activity_type'] === '1') {
                $icon = 'plus';
                $bg = 'primary';
                $title = $logs['account_full_name'] . ' (' . $logs['account_email'] . ')';
            } else if ($logs['activity_type'] === '2') {
                $icon = 'edit';
                $bg = 'warning';
                $title = $logs['account_full_name'] . ' (' . $logs['account_email'] . ')';
            }

            $logContent = '<dl class="row">';

            foreach ($logs['logs'] as $logKey => $log) {
                $logContent .=
                    '<dt class="col-md-4 text-uppercase">' . $logKey . '</dt>
                    <dd class="col-md-8">: ' . $log . '</dd>';
            }

            $logContent .= '</dl>';

            $this->content .=
                '<div>
                    <i class="fas fa-fw fa-' . $icon . ' bg-' . $bg . '"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> ' . $logs['created_at'] .'</span>
                        <h3 class="timeline-header">' .  $title . '</h3>
                        <div class="timeline-body">' . $logContent . '</div>
                        <div class="timeline-footer"></div>
                    </div>
                </div>';
        }

        $this->content .=
                        '<div>
                            <i class="fas fa-fw fa-clock bg-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>';
    }
}