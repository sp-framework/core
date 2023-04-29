<?php

namespace Apps\Core\Packages\AdminLTETags\Tags\Fields;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;

class Flatpickr
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

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        $this->fieldParams['fieldFlatpickrSize'] =
            isset($this->params['fieldFlatpickrSize']) ?
            $this->params['fieldFlatpickrSize'] :
            'sm';

        $this->fieldParams['fieldSize'] =
            isset($this->params['fieldSize']) ?
            'form-control-' . $this->params['fieldSize'] :
            'form-control-sm';

        $this->fieldParams['fieldFlatpickrPreAddonTextAdditionalClass'] =
            isset($this->params['fieldFlatpickrPreAddonTextAdditionalClass']) ?
            $this->params['fieldFlatpickrPreAddonTextAdditionalClass'] :
            '';

        if (isset($this->params['fieldFlatpickrPreAddonText']) ||
            isset($this->params['fieldFlatpickrPreAddonIcon']) ||
            isset($this->params['fieldFlatpickrPreAddonButtonId']) ||
            isset($this->params['fieldFlatpickrPostAddonText']) ||
            isset($this->params['fieldFlatpickrPostAddonIcon']) ||
            isset($this->params['fieldFlatpickrPostAddonButtonId']) ||
            isset($this->params['fieldFlatpickrClearButton'])
        ) {
            $this->content .=
                '<div class="input-group input-group-' . $this->fieldParams['fieldFlatpickrSize'] . '">';

            if (isset($this->params['fieldFlatpickrPreAddonText']) ||
                isset($this->params['fieldFlatpickrPreAddonIcon']) ||
                isset($this->params['fieldFlatpickrPreAddonButtonId'])
            ) {
                $this->preAddon();
            }

            $this->Input();

            if (isset($this->params['fieldFlatpickrPostAddonText']) ||
                isset($this->params['fieldFlatpickrPostAddonIcon']) ||
                isset($this->params['fieldFlatpickrPostAddonButtonId']) ||
                isset($this->params['fieldFlatpickrClearButton'])
            ) {
                $this->postAddon();

                $this->content .= '</div>';
            } else {
                $this->content .= '</div>';
            }
        } else {
            $this->Input();
        }
    }

    protected function preAddon()
    {
        if (isset($this->params['fieldFlatpickrPreAddonText'])) {
            $this->content .=
                '<div class="input-group-prepend">
                    <span class="input-group-text rounded-0 ' . $this->fieldParams['fieldFlatpickrPreAddonTextAdditionalClass'] . '">' . $this->params['fieldFlatpickrPreAddonText'] . '</span>
                </div>';

        }

        if (isset($this->params['fieldFlatpickrPreAddonIcon'])) {
            $this->content .=
                '<div class="input-group-prepend">
                    <span class="input-group-text rounded-0">
                        <i class="fas fa-fw fa-' . $this->params['fieldFlatpickrPreAddonIcon'] . '"></i>
                    </span>
                </div>';

        }

        if (isset($this->params['fieldFlatpickrPreAddonButtonId']) &&
                   isset($this->params['fieldFlatpickrPreAddonButtonValue'])
        ) {
            $this->fieldParams['fieldFlatpickrPreAddonButtonClass'] =
                isset($this->params['fieldFlatpickrPreAddonButtonClass']) ?
                $this->params['fieldFlatpickrPreAddonButtonClass'] :
                'primary';

            $this->fieldParams['fieldFlatpickrPreAddonButtonTooltipPosition'] =
                isset($this->params['fieldFlatpickrPreAddonButtonTooltipPosition']) ?
                $this->params['fieldFlatpickrPreAddonButtonTooltipPosition'] :
                'auto';

            $this->fieldParams['fieldFlatpickrPreAddonButtonTooltipTitle'] =
                isset($this->params['fieldFlatpickrPreAddonButtonTooltipTitle']) ?
                $this->params['fieldFlatpickrPreAddonButtonTooltipTitle'] :
                'Tooltip Title missing';

            $this->fieldParams['fieldFlatpickrPreAddonButtonDisabled'] =
                isset($this->params['fieldFlatpickrPreAddonButtonDisabled']) &&
                $this->params['fieldFlatpickrPreAddonButtonDisabled'] === true ?
                'disabled' :
                '';

            if (isset($this->params['fieldFlatpickrPreAddonButtonIcon'])) {
                if ($this->params['fieldFlatpickrPreAddonButtonIcon'] === 'after') {
                    $this->params['fieldFlatpickrPreAddonButtonValue'] =
                        strtoupper($this->params['fieldFlatpickrPreAddonButtonValue']) . ' ' .
                        '<i class="fas fa-fw fa-' . $this->params['fieldFlatpickrPreAddonButtonIcon'] . ' ml-1"></i>';
                } else {
                    $this->params['fieldFlatpickrPreAddonButtonValue'] =
                        '<i class="fas fa-fw fa-' . $this->params['fieldFlatpickrPreAddonButtonIcon'] . ' mr-1"></i>' . ' ' .
                        strtoupper($this->params['fieldFlatpickrPreAddonButtonValue']);
                }
            }

            $this->content .=
                '<div class="input-group-prepend">
                    <button ' . $this->fieldParams['fieldId'] . '-' . $this->params['fieldFlatpickrPreAddonButtonId'] . '" class="btn btn-'. $this->fieldParams['fieldFlatpickrPreAddonButtonClass'] . ' rounded-0" type="button" data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldFlatpickrPreAddonButtonTooltipPosition']. '" title="' . $this->fieldParams['fieldFlatpickrPreAddonButtonTooltipTitle'] . '" ' . $this->fieldParams['fieldFlatpickrPreAddonButtonDisabled'] . '>' . $this->params['fieldFlatpickrPreAddonButtonValue'] . '</button>
                </div>' ;
        }
    }

    protected function Input()
    {
        $this->content .=
            '<input '. $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' type="text" class="form-control ' . $this->fieldParams['fieldSize'] . ' rounded-0 ' . $this->fieldParams['fieldInputAdditionalClass'] .'" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '"  placeholder="' . strtoupper($this->fieldParams['fieldPlaceholder']) . '" ' . $this->fieldParams['fieldDataAttributes'] . ' ' . $this->fieldParams['fieldDisabled'] . ' value="' . $this->fieldParams['fieldValue'] . '" />';
    }

    protected function postAddon()
    {
        if (isset($this->params['fieldFlatpickrPostAddonText'])) {

            $this->content .=
                '<div class="input-group-append">
                    <span class="input-group-text rounded-0">{{fieldFlatpickrPostAddonText|raw}}</span>
                </div>';
        }

        if (isset($this->params['fieldFlatpickrPostAddonIcon'])) {

            $this->content .=
                '<div class="input-group-append">
                    <span class="input-group-text rounded-0">
                        <i class="fas fa-fw fa-' . $this->params['fieldFlatpickrPostAddonIcon'] . '"></i>
                    </span>
                </div>';
        }

        if (isset($this->params['fieldFlatpickrPostAddonButtonId']) &&
            isset($this->params['fieldFlatpickrPostAddonButtonValue'])
        ) {
            $this->fieldParams['fieldFlatpickrPostAddonButtonClass'] =
                isset($this->params['fieldFlatpickrPostAddonButtonClass']) ?
                $this->params['fieldFlatpickrPostAddonButtonClass'] :
                'primary';

            $this->fieldParams['fieldFlatpickrPostAddonButtonTooltipPosition'] =
                isset($this->params['fieldFlatpickrPostAddonButtonTooltipPosition']) ?
                $this->params['fieldFlatpickrPostAddonButtonTooltipPosition'] :
                'auto';

            $this->fieldParams['fieldFlatpickrPostAddonButtonTooltipTitle'] =
                isset($this->params['fieldFlatpickrPostAddonButtonTooltipTitle']) ?
                $this->params['fieldFlatpickrPostAddonButtonTooltipTitle'] :
                'Tooltip Title missing';

            $this->fieldParams['fieldFlatpickrPostAddonButtonDisabled'] =
                isset($this->params['fieldFlatpickrPostAddonButtonDisabled']) &&
                $this->params['fieldFlatpickrPostAddonButtonDisabled'] === true ?
                'disabled' :
                '';

            if (isset($this->params['fieldFlatpickrPostAddonButtonIcon'])) {
                $iconHidden =
                    (isset($this->params['fieldFlatpickrPostAddonButtonIconHidden'])) &&
                        $this->params['fieldFlatpickrPostAddonButtonIconHidden'] === true ?
                    'hidden' :
                    '';

                if ($this->params['fieldFlatpickrPostAddonButtonIcon'] === 'after') {
                    $this->params['fieldFlatpickrPostAddonButtonValue'] =
                        strtoupper($this->params['fieldFlatpickrPostAddonButtonValue']) . ' ' .
                        '<i class="fas fa-fw fa-' . $this->params['fieldFlatpickrPostAddonButtonIcon'] . ' ml-1" ' . $iconHidden . '></i>';
                } else {
                    $this->params['fieldFlatpickrPostAddonButtonValue'] =
                        '<i class="fas fa-fw fa-' . $this->params['fieldFlatpickrPostAddonButtonIcon'] . ' mr-1" ' . $iconHidden . '></i>' . ' ' .
                        strtoupper($this->params['fieldFlatpickrPostAddonButtonValue']);
                }
            }

            $this->content .=
                '<div class="input-group-append">
                    <button ' . $this->fieldParams['fieldId'] . '-' . $this->params['fieldFlatpickrPostAddonButtonId'] . '" class="btn btn-'. $this->fieldParams['fieldFlatpickrPostAddonButtonClass'] . ' rounded-0" type="button" data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldFlatpickrPostAddonButtonTooltipPosition']. '" title="' . $this->fieldParams['fieldFlatpickrPostAddonButtonTooltipTitle'] . '" ' . $this->fieldParams['fieldFlatpickrPostAddonButtonDisabled'] . '>' . $this->params['fieldFlatpickrPostAddonButtonValue'] . '</button>
                </div>';
        }

        if (isset($this->params['fieldFlatpickrClearButton']) &&
            $this->params['fieldFlatpickrClearButton'] === true
        ) {

            $this->fieldParams['fieldFlatpickrClearAddonButtonDisabled'] =
                isset($this->params['fieldFlatpickrClearAddonButtonDisabled']) ?
                $this->params['fieldFlatpickrClearAddonButtonDisabled'] :
                '';

            $this->fieldParams['fieldFlatpickrClearAddonButtonValue'] =
                isset($this->params['fieldFlatpickrClearAddonButtonValue']) ?
                $this->params['fieldFlatpickrClearAddonButtonValue'] :
                '';

            $this->content .=
                '<div class="input-group-append">
                    <button ' . $this->fieldParams['fieldId'] . '-clear" class="btn btn-danger rounded-0" type="button" data-toggle="tooltip" data-html="true" data-placement="auto" title="clear" ' . $this->fieldParams['fieldFlatpickrClearAddonButtonDisabled'] . '>' . $this->fieldParams['fieldFlatpickrClearAddonButtonValue'] . '<i class="fas fa-fw fa-eraser"></i></button>
                </div>';
        }
    }
}