<?php

namespace Applications\Ecom\Common\Packages\AdminLTETags\Tags\Fields;

use Applications\Ecom\Common\Packages\AdminLTETags\AdminLTETags;

class Radio
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
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->params = $params;

        $this->fieldParams = $fieldParams;

        $this->adminLTETags = new AdminLTETags();

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {

        $this->content .=
            '<div ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' ' . $this->fieldParams['fieldId'] . '">';

        $this->fieldParams['fieldRadioPlacementType'] =
            isset($this->params['fieldRadioPlacementType']) ?
            $this->params['fieldRadioPlacementType'] :
            'vertical';

        if ($this->fieldParams['fieldRadioPlacementType'] === 'horizontal') {
            $col = 12/count($this->params['fieldRadioButtons']);
        }

        $this->content .=
            '<div class="row">';

        if ($this->fieldParams['fieldRadioPlacementType'] === 'vertical') {
            $this->content .= '<div class="col-md-12">';
        }

        foreach ($this->params['fieldRadioButtons'] as $radioButtonKey => $radioButton) {
            if ($radioButton['dataValue'] === $this->params['fieldRadioChecked']) {
                $radioChecked = 'checked';
            } else {
                $radioChecked = '';
            }
            if ($this->fieldParams['fieldRadioPlacementType'] === 'horizontal') {
                $this->content .= '<div class="col-md-' . $col . '">';
            }

            $this->content .=
                '<div class="icheck-' . $radioButton['type'] . '">
                    <input type="radio" id="' . $radioButtonKey . '" data-value="' . $radioButton['dataValue'] . '" ' . $this->fieldParams['fieldName'] . '" ' . $radioChecked . '>';

                    if (isset($radioButton['icon'])) {
                        if (!isset($radioButton['iconPosition']) ||
                            (isset($radioButton['iconPosition']) && $radioButton['iconPosition'] === 'before')
                        ) {
                            $this->content .=
                                '<label for="' . $radioButtonKey . '"><i class="' . $radioButton['icon'] . '"></i><span class="ml-1">' . $radioButton['title'] . '</span></label>';
                        } else if ($radioButton['iconPosition'] === 'after') {
                            $this->content .=
                                '<label for="' . $radioButtonKey . '"><span class="mr-1">' . $radioButton['title'] . '</span><i class="' . $radioButton['icon'] . '"></i></label>';
                        }
                    } else {
                        $this->content .=
                            '<label for="' . $radioButtonKey . '"><span>' . $radioButton['title'] . '</span></label>';
                    }

            $this->content .=
                '</div>';

            if ($this->fieldParams['fieldRadioPlacementType'] === 'horizontal') {
                $this->content .= '</div>';
            }
        }
        $this->content .=
                '</div>
            </div>
        </div>';
    }
}