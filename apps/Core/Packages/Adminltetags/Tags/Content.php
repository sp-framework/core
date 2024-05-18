<?php

namespace Apps\Core\Packages\Adminltetags\Tags;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Content extends Adminltetags
{
    protected $params;

    protected $compSecId;

    protected $content = '';

    public function getContent($params)
    {
        $this->params = $params;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        if (isset($this->params['componentId']) && isset($this->params['parentComponentId'])) {
            if (isset($this->params['contentType'])) {

                $this->content .=
                    '<div id="' . $this->params['componentId'] . '" class="component" data-component_id="' . $this->params['component']['id'] . '">';

                if ($this->params['contentType'] === 'section') {
                    $this->content .= $this->getContentTypeSection();
                } else if ($this->params['contentType'] === 'sectionWithForm') {
                    if (isset($this->params['dataDependencies']) &&
                        count($this->params['dataDependencies']) > 0
                    ) {
                        $this->content .= $this->checkDataDependency();
                    } else {
                        $this->content .= $this->getContentTypeSectionWithForm();
                    }
                } else if ($this->params['contentType'] === 'sectionWithWizard') {
                    $this->content .= $this->getContentTypeSectionWithWizard();
                } else if ($this->params['contentType'] === 'sectionWithFormToDatatable') {
                    $this->content .= $this->getContentTypeSectionWithFormToDatatable();
                } else if ($this->params['contentType'] === 'sectionWithListing') {
                    $this->content .= $this->getContentTypeSectionWithListing();
                } else if ($this->params['contentType'] === 'sectionWithStorage') {
                    $this->content .= $this->getContentTypeSectionWithStorage();
                }

                $this->content .=
                    '</div>';

            } else {
                $this->content .=
                    '<span class="text-uppercase text-danger">ERROR: contentType missing</span>';
            }
        } else {
            $this->content .=
                '<span class="text-uppercase text-danger">ERROR: componentId OR parentComponentId missing</span>';
        }
    }

    protected function getContentTypeSection()
    {
        return
            '<section id="' . $this->compSecId . '" class="section">' .
                $this->useTag('card', $this->params) .
            '</section>
            <script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';
    }

    protected function checkDataDependency()
    {
        $hasError = false;

        $cardContent = '';

        foreach ($this->params['dataDependencies'] as $errorKey => $error) {
            if ((is_array($error['componentVar']) && count($error['componentVar']) === 0) ||
                $error['componentVar'] === false
            ) {
                $hasError = true;
                $cardContent .=
                    '<div class="callout callout-danger">
                        <h5>No ' . strtolower($error['componentName']) . ' data found!</h5>
                        <p>';

                if (isset($error['componentErrorMessage']) && $error['componentErrorMessage'] !== '') {
                    $cardContent .= $error['componentErrorMessage'] . '</p></div>';
                } else {
                    $cardContent .= 'Component ' . $this->params['component']['name'] . ' needs data from component ' . strtolower($error['componentName']) . '. ';

                    if ($error['componentRoute']) {
                        $cardContent .=
                            'Please <a href="' . $this->links->url($error['componentRoute']) . '" class="contentAjaxLink text-primary">click here</a> to add new ' . strtolower($error['componentName']) . ' or contact systems administrator for further instructions.</p>
                        </div>';
                    } else {
                        $cardContent .=
                            'Please contact systems administrator for further instructions.</p>
                        </div>';
                    }
                }
            }
        }

        $cardParams =
            [
                'componentId'                   => $this->params['componentId'],
                'sectionId'                     => $this->params['sectionId'],
                'cardHeader'                    => true,
                'cardType'                      => 'danger',
                'cardIcon'                      => 'ban',
                'cardTitle'                     => 'Data Dependency Error',
                'cardAdditionalClass'           => 'rounded-0',
                'cardShowTools'                 => [],
                'cardBodyContent'               => $cardContent
            ];

        if ($hasError) {
            return
                '<section id="' . $this->compSecId . '" class="section">' .
                    $this->useTag('card', $cardParams) .
                '</section>
                <script>
                    window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                    window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
                </script>';
        } else {
            return $this->getContentTypeSectionWithForm();
        }
    }

    protected function getContentTypeSectionWithForm()
    {
        $sectionForm = '';

        if (isset($this->params['formSecondaryButtons']) && is_array($this->params['formSecondaryButtons'])) {
            $formSecondaryButtons = $this->params['formSecondaryButtons'];
        } else {
            $formSecondaryButtons = [];
        }

        if (isset($this->params['formButtons']) && is_array($this->params['formButtons'])) {
            $formButtons =
                [
                    'componentId'            => $this->params['componentId'],
                    'sectionId'              => $this->params['sectionId'],
                    'buttonLabel'            => false,
                    'buttonType'             => 'sectionWithFormButtons',
                    'formButtons'            => $this->params['formButtons'],
                    'formSecondaryButtons'   => $formSecondaryButtons
                ];

            $this->params['cardFooterContent'] = $this->useTag('buttons', $formButtons);
        }

        $sectionForm .=
            '<section id="' . $this->compSecId . '" class="sectionWithForm">' .
                $this->useTag('card', $this->params) .
            '</section>';

        $sectionForm .=
            '<script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';

        return $sectionForm;
    }

    protected function getContentTypeSectionWithWizard()
    {
        $sectionWizard = '';

        $this->params['cardFooterContent'] =
            $this->useTag('buttons',
                [
                    'componentId'            => $this->params['componentId'],
                    'sectionId'              => $this->params['sectionId'],
                    'buttonLabel'            => false,
                    'buttonType'             => 'sectionWithWizardButtons',
                    'buttonParams'           => $this->params
                ]
            );

        $this->params['cardBodyContent'] = $this->useTag('wizard', $this->params);

        $sectionWizard .=
            '<section id="' . $this->compSecId . '" class="sectionWithWizard">' .
                $this->useTag('card', $this->params) .
            '</section>';

        $sectionWizard .=
            '<script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';

        return $sectionWizard;
    }

    protected function getContentTypeSectionWithFormToDatatable()
    {
        return
            '<section id="' . $this->compSecId . '" class="sectionWithFormToDatatable">' .
                $this->useTag('card', $this->params) .
            '</section>
            <script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';
    }

    protected function getContentTypeSectionWithListing()
    {
        $sectionListing = '';

        $this->params['cardBodyContent'] = $this->useTag('content/listing/table', $this->params);

        $sectionListing .=
            '<section id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] .
            '" class="sectionWithListingDatatable">' .
            $this->useTag('card', $this->params) .
            '</section>';

        $sectionListing .=
            '<script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';

        return $sectionListing;
    }

    protected function getContentTypeSectionWithStorage()
    {
        // {% if cardParams['sectionWithStorageMode'] == 'full' %}
        //     {% set sectionId = 'full' %}
        //     <section id="{{cardParams['componentId']}}-{{sectionId}}" class="sectionWithStorage">
        //         {% include 'thelpers/card/card.html' with
        //             [
        //                 'cardIcon'            : 'hdd',
        //                 'cardTitle'           : 'Storage',
        //                 'cardShowTools'       : ['collapse'],
        //                 'cardBodyInclude'     : 'thelpers/content/storage/full'
        //             ]
        //         %}
        //     </section>
        // {% elseif sectionWithStorageMode === 'mini' %}
        //     {% set sectionId = 'mini' %}
        //     <section id="{{cardParams['componentId']}}-{{sectionId}}" class="sectionWithStorage">
        //         {% include 'thelpers/content/storage/mini.html' %}
        //     </section>
        // {% else %}
        //     <span class="text-uppercase text-danger">{{('TEMPLATE ERROR: sectionWithStorageMode missing')}}</span>
        // {% endif %}
        // <script>
        //     window['dataCollection']['env']['currentComponentId'] = '{{cardParams['componentId']}}';
        //     window['dataCollection']['env']['parentComponentId'] = '{{parentComponentId}}';
        // </script>
    }
}