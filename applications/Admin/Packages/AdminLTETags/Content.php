<?php

namespace Applications\Admin\Packages\AdminLTETags;

use Applications\Admin\Packages\AdminLTETags;

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
                } else if ($this->params['contentType'] === 'sectionsListing') {
                    $this->content .= $this->getContentTypeSectionsListing();
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

    protected function getContentTypeSectionsListing()
    {
        $sectionsListing = '';

        if (isset($this->params['dtColumns']) || isset($this->params['dtRows'])) {
            if (isset($this->params['dtFilter']) && $this->params['dtFilter'] === true) {

                isset($this->params['dtFilterCardType']) ?
                $dtFilterCardType = $this->params['dtFilterCardType'] :
                $dtFilterCardType = 'primary';

                isset($this->params['dtFilterCardHeader']) ?
                $dtFilterCardHeader = $this->params['dtFilterCardHeader'] :
                $dtFilterCardHeader = true;

                isset($this->params['dtFilterCardCollapsed']) ?
                $dtFilterCardCollapsed = $this->params['dtFilterCardCollapsed'] :
                $dtFilterCardCollapsed = false;

                $sectionsListing .=
                    '<section id="' . $this->params['componentId'] .
                    '-listing-filter" class="sectionWithListingFilter mb-1">' .
                        $this->useTag('card',
                            [
                                'componentId'         => $this->params['componentId'],
                                'sectionId'           => $this->params['sectionId'],
                                'cardType'            => $dtFilterCardType,
                                'cardHeader'          => $dtFilterCardHeader,
                                'cardCollapsed'       => $dtFilterCardCollapsed,
                                'cardIcon'            => 'filter',
                                'cardTitle'           => 'Filters',
                                'cardShowTools'       => ['collapse'],
                                'cardBodyContent'     => $this->useTag('content/listing/filter', $this->params)
                            ]
                        );
            }

            $this->params['cardBodyContent'] = $this->useTag('content/listing/table', $this->params);

            $sectionsListing .=
                '<section id="' . $this->params['componentId'] .
                '-listing" class="sectionWithListingDatatable">' .
                $this->useTag('card', $this->params) .
                '</section>';

        } else {

            $this->params['cardBodyContent'] = $this->useTag('content/listing/table', $this->params);

            $sectionsListing .=
                '<section id="' . $this->params['componentId'] .
                '-listing" class="sectionWithListingDatatable">' .
                $this->useTag('card', $this->params) .
                '</section>';
        }

        $sectionsListing .=
            '<script>
                window["dataCollection"]["env"]["currentComponentId"] = "' . $this->params['componentId'] . '";
                window["dataCollection"]["env"]["parentComponentId"] = "' . $this->params['parentComponentId'] . '";
            </script>';

        return $sectionsListing;
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