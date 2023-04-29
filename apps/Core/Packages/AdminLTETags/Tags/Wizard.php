<?php

namespace Apps\Core\Packages\AdminLTETags\Tags;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Json;

class Wizard extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $wizardParams = [];

    protected $compSecId;

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->wizardParams['componentId'] =
            isset($this->params['wizardComponentId']) ?
            $this->params['wizardComponentId'] :
            $this->params['componentId'];

        $this->wizardParams['sectionId'] =
            isset($this->params['wizardSectionId']) ?
            $this->params['wizardSectionId'] :
            $this->compSecId;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        if (!isset($this->params['wizardSteps']) || !is_array($this->params['wizardSteps'])) {
            throw new \Exception('wizardSteps Array Missing');
        }

        if (!isset($this->params['wizardCanCancel'])) {
            $this->params['wizardCanCancel'] = false;
        }

        $truncated = '';
        $lis = '';
        $description = '';
        $data = '';

        if ($this->params['wizardType'] === 'vertical') {
            $type = 'wizard vertical';
            $truncated = 'text-truncate';
        } else if ($this->params['wizardType'] === 'triangle') {
            $type = 'wizard triangle';
        } else if ($this->params['wizardType'] === 'multi-text-center') {
            $type = 'wizard multi-steps text-center';
        } else if ($this->params['wizardType'] === 'multi-text-top') {
            $type = 'wizard multi-steps text-top';
        } else if ($this->params['wizardType'] === 'multi-text-bottom') {
            $type = 'wizard multi-steps text-bottom';
        } else if ($this->params['wizardType'] === 'multi-count-text-top') {
            $type = 'wizard multi-steps text-top count';
        } else if ($this->params['wizardType'] === 'multi-count-text-bottom') {
            $type = 'wizard multi-steps text-bottom count';
        } else {
            $type = 'wizard';
        }

        if (isset($this->params['wizardShowReview']) && $this->params['wizardShowReview'] === true) {
            if (!isset($this->params['wizardReviewDescription'])) {
                throw new \Exception('wizardShowReview is set to true but wizardReviewDescription is missing');
            }
            $this->params['wizardSteps'] =
                array_merge(
                    $this->params['wizardSteps'],
                    [(string) count($this->params['wizardSteps']) + 1 => ['title' => 'Review']]
                );
        } else {
            $this->params['wizardShowReview'] = false;
        }

        if (!isset($this->params['wizardStartAtStep'])) {
            $this->params['wizardStartAtStep'] = 0;
        }


        foreach ($this->params['wizardSteps'] as $wizardStepKey => $wizardStep) {
            if ($this->params['wizardType'] === 'vertical') {
                if (isset($wizardStep['icon'])) {
                    $lis .=
                        '<li id="' . $this->compSecId .  '-' . $wizardStepKey . '-step" class="">
                            <span class="' . $truncated . '">
                                <i class="fas fa-fw fa-' . $wizardStep['icon'] . ' mr-1"></i>' . $wizardStep['title'] .
                            '</span>
                        </li>';
                } else {
                    $lis .=
                        '<li id="' . $this->compSecId .  '-' . $wizardStepKey . '-step" class="">
                            <span>' . $wizardStep['title'] . '</span>
                        </li>';
                }
            } else {
                if (isset($wizardStep['icon'])) {
                    $lis .=
                        '<li id="' . $this->compSecId .  '-' . $wizardStepKey . '-step" class="">
                            <span>
                                <i class="fas fa-fw fa-' . $wizardStep['icon'] . ' mr-1"></i>' . $wizardStep['title'] .
                            '</span>
                        </li>';
                } else {
                    $lis .=
                        '<li id="' . $this->compSecId .  '-' . $wizardStepKey . '-step" class=""><span>' . $wizardStep['title'] . '</span></li>';
                }
            }
            if (isset($wizardStep['description'])) {
                $description .=
                    '<div class="rounded-0 callout callout-info" id="' . $this->compSecId .  '-' . $wizardStepKey . '-description" hidden>' .
                        $wizardStep['description'] .
                    '</div>';
            }
            if (isset($wizardStep['content'])) {
                $data .=
                    '<div id="' . $this->compSecId .  '-' . $wizardStepKey . '-data" hidden>' .
                        '<div class="card">
                            <div class="card-body">' .
                                $wizardStep['content'] .
                            '</div>
                        </div>
                    </div>';

                //Remove content as it will cause problem with JS Encoding
                unset($this->params['wizardSteps'][$wizardStepKey]['content']);

            } else if (isset($wizardStep['ajax'])) {
                $data .= '<div id="' . $this->compSecId .  '-' . $wizardStepKey . '-data" hidden></div>';
            } else {
                throw new \Exception('content for wizardStep key ' . $wizardStepKey . ' not set');
            }
        }

        if (isset($this->params['wizardShowReview']) && $this->params['wizardShowReview'] === true) {
            $count = count($this->params['wizardSteps']) - 1;
            $description .=
                '<div class="rounded-0 callout callout-info" id="' . $this->compSecId .  '-' . $count . '-description" hidden>'
                    . $this->params['wizardReviewDescription'] .
                '</div>';

            $data .= '<div id="' . $this->compSecId .  '-' . $count . '-data" hidden></div>';
        }

        if ($this->params['wizardType'] === 'vertical') {
            $this->content .=
                '<div class="row" id="' . $this->compSecId . '-steps">
                    <div class="col-md-3">
                        <ol class="' . $type . '">' .
                            $lis .
                        '</ol>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col">' .
                                $description .
                            '</div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div id="' . $this->compSecId . '-data">' . $data . '</div>
                            </div>
                        </div>
                    </div>
                </div>';

        } else {
            $this->content .=
                '<div class="row" id="'. $this->compSecId .'-steps">
                    <div class="col">
                        <ol class="' . $type . '">
                            ' . $lis . '
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        ' . $description . '
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div id="' . $this->compSecId .'-data">' . $data . '</div>
                    </div>
                </div>';
        }

        $this->content .= $this->inclJs();
    }

    protected function inclJs()
    {
        $inclJs =
            '<script type="text/javascript">
            var dataCollectionComponent, dataCollectionSection, dataCollectionSectionForm;

            if (!window["dataCollection"]["' . $this->params['componentId'] . '"]) {
                dataCollectionComponent =
                    window["dataCollection"]["' . $this->params['componentId'] . '"] = { };
            } else {
                dataCollectionComponent =
                    window["dataCollection"]["' . $this->params['componentId'] . '"];
            }
            if (!dataCollectionComponent["' . $this->compSecId . '"]) {
                dataCollectionSection =
                    dataCollectionComponent["' . $this->compSecId . '"] = { };
            } else {
                dataCollectionSection =
                    dataCollectionComponent["' . $this->compSecId . '"];
            }
            // if (!dataCollectionSection["' . $this->compSecId . '-form"]) {
            //     dataCollectionSectionForm =
            //         dataCollectionSection["' . $this->compSecId . '-form"] = { };
            // } else {
            //     dataCollectionSectionForm =
            //         dataCollectionSection["' . $this->compSecId . '-form"];
            // }

            dataCollectionSection =
                $.extend(dataCollectionSection, {
                    "showReview"                    : "' . $this->params['wizardShowReview'] . '",
                    "startAtStep"                   : "' . $this->params['wizardStartAtStep'] . '",
                    "canCancel"                     : "'. $this->params['wizardCanCancel'] .'",
                    "componentId"                   : "'. $this->wizardParams['componentId'] .'",
                    "sectionId"                     : "'. $this->wizardParams['sectionId'] .'",
                    "steps"                         : JSON.parse("' . $this->escaper->escapeJs(Json::encode($this->params["wizardSteps"])) . '"),
                });
            </script>';

        return $inclJs;
    }
}