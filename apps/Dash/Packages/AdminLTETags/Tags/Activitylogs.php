<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;

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
        if (!isset($this->params['activityLogs'])) {
            throw new \Exception('Error: activityLogs (array) missing');
        }

        $this->content .=
            '<div class="row">
                <div class="col">
                    <div class="timeline">';

        foreach ($this->params['activityLogs'] as $logsKey => $logs) {
            if ($logs['activity_type'] === '1') {
                $icon = 'plus';
                $bg = 'primary';
            } else if ($logs['activity_type'] === '2') {
                $icon = 'edit';
                $bg = 'warning';
            }

            if (isset($logs['account_id']) && $logs['account_id'] == 0) {
                $title = '<span><i class="fas fa-fw fa-robot"></i> ' . $logs['account_full_name'] . ' </span>';
            } else {
                $title = '<span><i class="fas fa-fw fa-user"></i> ' . $logs['account_full_name'] . ' (' . $logs['account_email'] . ') </span>';
            }

            $logContent = '<dl class="row">';

            foreach ($logs['log'] as $logKey => $log) {
                if (!in_array($logKey, $this->params['disableKeys'])) {
                    if (array_key_exists($logKey, $this->params['replaceValues'])) {
                        $log = $this->params['replaceValues'][$logKey][$log];
                    }
                    if (array_key_exists($logKey, $this->params['replaceKeys'])) {
                        $logKey = $this->params['replaceKeys'][$logKey];
                    }

                    $logKey = str_replace('_', ' ', $logKey);

                    if (is_array($log)) {
                        $log = Json::encode($log);

                        if ($log === '[]') {
                            $log = '';
                        }
                    }

                    $logContent .=
                        '<dt class="col-md-4 text-uppercase">' . $logKey . '</dt>
                        <dd class="col-md-8">: ' . $log . '</dd>';
                }
            }

            $logContent .= '</dl>';

            $this->content .=
                '<div>
                    <i class="fas fa-fw fa-' . $icon . ' bg-' . $bg . '"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> ' . $logs['created_at'] .'</span>
                        <h6 class="timeline-header text-secondary">' .  $title . '</h6>
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