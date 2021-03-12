<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Fields;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

class Counters
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $params;

    protected $fieldParams;

    protected $content;

    protected $adminLTETags;

    public function __construct($view, $tag, $links, $escaper, $params, $fieldParams)
    {
        $this->adminLTETags = new AdminLTETags();

        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->params = $params;

        $this->fieldParams = $fieldParams;

        $this->fieldParams['fieldCountersAdditionalClass'] =
            isset($this->params['fieldCountersAdditionalClass']) ?
            $this->params['fieldCountersAdditionalClass'] :
            '';

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        if (!isset($this->params['fieldCounters']) || !is_array($this->params['fieldCounters'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">fieldCounters missing</span>';
            return;
        }

        if (!isset($this->params['fieldCountersPlacementType']) ||
            isset($this->params['fieldCountersPlacementType']) &&
            $this->params['fieldCountersPlacementType'] === 'vertical'
        ) {
            $col = 12;
        } else if (isset($this->params['fieldCountersPlacementType']) &&
                   $this->params['fieldCountersPlacementType'] === 'horizontal'
        ) {
            if (count($this->params['fieldCounters']) > 4) {
                $col = 3;
            } else if (count($this->params['fieldCounters']) <= 4) {
                $col = 12 / count($this->params['fieldCounters']);
            }
        }

        if ($col === 12) {
            $this->content .=
                '<div ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' class="row ' . $this->fieldParams['fieldCountersAdditionalClass'] . '" ' . $this->fieldParams['fieldId'] . '">
                    <div id="' . $counter['id'] . '" ' . $hasDataValue . ' class="col-md-12 pt-2">';

        } else {
            $this->content .=
                '<div ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' class="row vdivide ' . $this->fieldParams['fieldCountersAdditionalClass'] . '" ' . $this->fieldParams['fieldId'] . '">';
        }

        foreach ($this->params['fieldCounters'] as $counterKey => $counter) {
            if (isset($counter['value'])) {
                $counterValue = $counter['value'];
                $hasDataValue = 'data-value="' . $counter['value'] . '"';
            } else {
                $counterValue = 0;
            }

            if (isset($counter['type'])) {
                $hasType = $counter['type'];
            } else {
                $hasType = 'primary';
            }

            if ($col === 12) {
                $this->content .=
                    '<label class="text-uppercase">' . $counter['title'] . '</label>
                    <span class="badge badge-' . $hasType . ' float-right mt-1">' . $counterValue . '</span>';
            } else {
                $this->content .=
                    '<div id="' . $counter['id'] . '" ' . $hasDataValue . ' class="col-md-' . $col . ' pt-2">
                        <label class="text-uppercase">' . $counter['title'] . '</label>
                        <span class="badge badge-' . $hasType . ' float-right mt-1">' . $counterValue . '</span>
                    </div>';
            }
        }

        if ($col === 12) {
            $this->content .=
                '</div>
            </div>';
        } else {
            $this->content .=
                '</div>';
        }
    }
}