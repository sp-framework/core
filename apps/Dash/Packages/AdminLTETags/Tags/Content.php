<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

class Content extends AdminLTETags
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
                } else if ($this->params['contentType'] === 'sectionWithFormToDatatable') {
                    $this->content .= $this->getContentTypeSectionWithFormToDatatable();
                } else if ($this->params['contentType'] === 'sectionWithWizard') {
                    $this->content .= $this->getContentTypeSectionWithWizard();
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
                        <h5>No ' . $error['componentName'] . ' data found!</h5>
                        <p>Component ' . $this->params['component']['name'] . ' needs data from component ' . $error['componentName'] . '. ';

                if ($error['componentRoute']) {
                    $cardContent .=
                        'Please <a href="' . $this->links->url($error['componentRoute']) . '" class="contentAjaxLink text-primary">click here</a> to add new ' . $error['componentName'] . ' or contact systems administrator for further instructions.</p>
                    </div>';
                } else {
                    $cardContent .=
                        'Please contact systems administrator for further instructions.</p>
                    </div>';
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

        $this->params['cardFooterContent'] =
            $this->useTag('buttons',
                [
                    'componentId'            => $this->params['componentId'],
                    'sectionId'              => $this->params['sectionId'],
                    'buttonLabel'            => false,
                    'buttonType'             => 'sectionWithFormButtons',
                    'formButtons'            => $this->params['formButtons']
                ]
            );

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

    protected function getContentTypeSectionWithWizard()
    {
        // <section id="{{cardParams['componentId']}}-{{cardParams['sectionId']}}" class="sectionWithWizard">
        //     {% include 'thelpers/card/card.html' with
        //         [
        //             'cardFooterContent' : view.partial('thelpers/buttons',
        //                                     [
        //                                         'buttonType'    : 'sectionWithWizard-buttons'
        //                                     ]),
        //             'cardBodyInclude'   : 'thelpers/content/wizard/wizard'
        //         ]
        //     %}
        // </section>
        // <script>
        //     window['dataCollection']['env']['currentComponentId'] = '{{cardParams['componentId']}}';
        //     window['dataCollection']['env']['parentComponentId'] = '{{parentComponentId}}';
        // </script>
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