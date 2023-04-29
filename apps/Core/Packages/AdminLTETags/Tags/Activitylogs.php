<?php

namespace Apps\Core\Packages\AdminLTETags\Tags;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;
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

        if (isset($this->params['activityLogs']['paginationCounters'])) {
            if ($this->params['activityLogs']['paginationCounters']['first'] === $this->params['activityLogs']['paginationCounters']['current']) {
                $start = 1;
                $to = $this->params['activityLogs']['paginationCounters']['limit'];
            } else {
                $start = (($this->params['activityLogs']['paginationCounters']['current'] * $this->params['activityLogs']['paginationCounters']['limit']) - $this->params['activityLogs']['paginationCounters']['limit']) + 1;
                $to = $this->params['activityLogs']['paginationCounters']['current'] * $this->params['activityLogs']['paginationCounters']['limit'];
            }

            if ($to > $this->params['activityLogs']['paginationCounters']['filtered_items'] &&
                $this->params['activityLogs']['paginationCounters']['last'] === $this->params['activityLogs']['paginationCounters']['current']
            ) {
                $start = (($this->params['activityLogs']['paginationCounters']['current'] * $this->params['activityLogs']['paginationCounters']['limit']) - $this->params['activityLogs']['paginationCounters']['limit']) + 1;
                $to = $this->params['activityLogs']['paginationCounters']['filtered_items'];
            }

            $this->content .=
                '<div class="row ml-4 mr-4">
                    <div class="col">
                        Showing <span class="activityLogs-shown">' . $start . ' to ' . $to . '</span> out of <span class="active-logs-total">' . $this->params['activityLogs']['paginationCounters']['filtered_items'] . '</span>
                    </div>
                    <div class="col">';

            $leftDisabled = '';
            $rightDisabled = '';

            if ($this->params['activityLogs']['paginationCounters']['first'] === 1 &&
                $this->params['activityLogs']['paginationCounters']['current'] === 1 &&
                $this->params['activityLogs']['paginationCounters']['last'] === 1
            ) {
                $leftDisabled = 'disabled';
                $rightDisabled = 'disabled';
            } else if ($this->params['activityLogs']['paginationCounters']['first'] === 1 &&
                       $this->params['activityLogs']['paginationCounters']['current'] === 1 &&
                       $this->params['activityLogs']['paginationCounters']['last'] > 1
            ) {
                $leftDisabled = 'disabled';
                $rightDisabled = '';
            } else if ($this->params['activityLogs']['paginationCounters']['current'] === $this->params['activityLogs']['paginationCounters']['last']) {
                $leftDisabled = '';
                $rightDisabled = 'disabled';
            }

            $this->content .=
                '<ul class="pagination pagination-sm float-right">
                    <li class="page-item">
                        <a class="page-link activity-logs-previous ' . $leftDisabled . '" href="#"><i class="fas fa-chevron-left"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link activity-logs-next ' . $rightDisabled . '" href="#"><i class="fas fa-chevron-right"></i></a>
                    </li>
                </ul>';

            $this->content .=
                    '</div>
                </div>';
        }

        $this->content .=
            '<div class="row">
                <div class="col">
                    <div class="timeline">';

        foreach ($this->params['activityLogs'] as $logsKey => $logs) {
            if ($logsKey === 'paginationCounters') {
                continue;
            }

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

                        $jsonLog = $log;

                        $log = $log . '<br><br><button class="btn btn-xs btn-info mr-1" id="format-data-' . $logsKey . '" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title=""><i class="fas fa-fw fa-magic"></i> FORMAT DATA</button>';

                            $log .=
                                '<script type="text/javascript">
                                    $("#format-data-' . $logsKey . '").click(function(e) {
                                        e.preventDefault();

                                        var html = \'' . $jsonLog . '\';
                                        var regex = /{.*}/g;
                                        var found = html.match(regex);

                                        if (found) {
                                            var data = found[0].replaceAll(\'\"\', \'"\');

                                            var obj = JSON.parse(data);

                                            if (obj) {
                                                $("#' . $logKey . '-' . $logsKey . '").html(BazHelpers.createHtmlList({"obj": obj}));
                                            }
                                        }
                                    });
                                </script>';
                    }

                    $logContent .=
                        '<dt class="col-md-4 text-uppercase">' . $logKey . '</dt>
                        <dd id="' . $logKey . '-' . $logsKey . '" class="col-md-8">: ' . $log . '</dd>';
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

        if (!isset($this->params['activityLogs']['paginationCounters'])) {
            return;
        }

        $this->content .=
            '<script type="text/javascript">
                var paginationCounters = JSON.parse(\'' . Json::encode($this->params['activityLogs']['paginationCounters']) . '\');

                $(".activity-logs-previous, .activity-logs-next").click(function(e) {
                    e.preventDefault();

                    var url = "' . $this->links->url('crypto/trades/getActivityLogs') . '";

                    var postData = { };
                    postData[$("#security-token").attr("name")] = $("#security-token").val();
                    postData["id"] = $("#' . $this->params['componentId'] . '-main-id").val();

                    if ($(this).is(".activity-logs-previous")) {
                        postData["page"] = paginationCounters["previous"];
                    } else if ($(this).is(".activity-logs-next")) {
                        postData["page"] = paginationCounters["next"];
                    }

                    $.post(url, postData, function(response) {
                        if (response.responseCode == 0) {
                            if (response.responseData) {
                                $("#activity-logs").empty().html(response.responseData.logs);
                            }
                        }
                    }, "json");
                });
            </script>';
    }
}