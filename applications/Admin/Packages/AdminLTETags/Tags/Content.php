<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Content extends AdminLTETags
{
    protected $params;

    protected $content = '';

    public function getContent($params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        if (isset($this->params['componentId']) && isset($this->params['parentComponentId'])) {
            if (isset($this->params['contentType'])) {

                $this->content .=
                    '<div id="' . $this->params['componentId'] . '" class="component">';

                if ($this->params['contentType'] === 'section') {
                    $this->content .= $this->getContentTypeSection();
                } else if ($this->params['contentType'] === 'sectionWithForm') {
                    $this->content .= $this->getContentTypeSectionWithForm();
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
            '<section id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '" class="section">' . $this->useTag('card', $this->params) .
            '</section>
            <script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';
    }

    protected function getContentTypeSectionWithForm()
    {
        $sectionForm = '';

        $sectionForm .=
            '<section id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] .
            '" class="sectionWithForm">' . $this->useTag('card', $this->params) .
            '</section>';

        $sectionForm .=
            '<script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';

        return $sectionForm;

            // <section id="{{cardParams['componentId']}}-{{cardParams['sectionId']}}" data-bazdevmodetools="{{devModeTools|default('true')}}" class="sectionWithForm">
            //     {% include 'thelpers/card/card.html' with
            //         [
            //             'cardFooterContent'   : view.partial('thelpers/buttons',
            //                                     [
            //                                         'cardParams'    : cardParams,
            //                                         'buttonType'    : 'sectionWithForm-buttons'
            //                                     ])
            //         ]
            //     %}
            // </section>
            // <script>
            //     window['dataCollection']['env']['currentComponentId'] = '{{cardParams['componentId']}}';
            //     window['dataCollection']['env']['parentComponentId'] = '{{parentComponentId}}';
            // </script>
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