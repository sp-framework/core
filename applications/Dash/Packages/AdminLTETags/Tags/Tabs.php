<?php

namespace Applications\Dash\Packages\AdminLTETags\Tags;

use Applications\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Arr;

class Tabs extends AdminLTETags
{
    protected $params;

    protected $tabsParams;

    protected $content = '';

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->tabsParams = [];

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        if (!isset($this->params['tabsId'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">tabsId missing</span>';
            return;
        }

        if (!isset($this->params['tabsData'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">tabsData missing</span>';
            return;
        }

        if (!isset($this->params['tabsStyle'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">
                tabsStyle missing.<br>
                Available Styles: <span class="text-lowercase">vertical-left, vertical-right, card-tabs, card-outline, card-outline-tabs, none</span>
                </span>';
            return;
        }

        $this->tabsParams['tabsId'] =
            $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $this->params['tabsId'];

        $this->tabsParams['tabsHidden'] =
            isset($this->params['tabsHidden']) && $this->params['tabsHidden'] === true ?
            'hidden' :
            '';

        if ($this->params['tabsStyle'] === 'vertical-left' || $this->params['tabsStyle'] === 'vertical-right') {
            $this->tabsParams['verticalTabsSize'] =
                isset($this->params['verticalTabsSize']) && isset($this->params['verticalTabsContentSize']) ?
                $this->params['verticalTabsSize'] :
                'col-md-3 col-sm-1';

            $this->tabsParams['verticalTabsContentSize'] =
                isset($this->params['verticalTabsSize']) && isset($this->params['verticalTabsContentSize']) ?
                $this->params['verticalTabsContentSize'] :
                'col-md-9 col-sm-11';

            $this->content .=
                '<div class="row">';

            if ($this->params['tabsStyle'] === 'vertical-left') {
                $this->content .=
                    '<div class="' . $this->tabsParams['verticalTabsSize'] . '">
                        <div class="nav flex-column nav-tabs h-100" role="tablist" id="' . $this->tabsParams['tabsId'] . '-tabLinks" aria-orientation="vertical">';

                            $this->content .= $this->generateTabs(false);

                $this->content .=
                        '</div>
                    </div>
                    <div class="' . $this->tabsParams['verticalTabsContentSize'] . '">
                        <div class="tab-content" id="' . $this->tabsParams['tabsId'] . '-tabContent">';

                            $this->content .= $this->generateTabsContent();

                $this->content .=
                        '</div>
                    </div>';
            } else if ($this->params['tabsStyle'] === 'vertical-right') {
                $this->content .=
                    '<div class="' . $this->tabsParams['verticalTabsContentSize'] . '">
                        <div class="tab-content" id="' . $this->tabsParams['tabsId'] . '-tabContent">';

                            $this->content .= $this->generateTabsContent();

                $this->content .=
                        '</div>
                    </div>
                    <div class="' . $this->tabsParams['verticalTabsSize'] . '">
                        <div class="nav flex-column nav-tabs nav-tabs-right h-100" role="tablist" id="' . $this->tabsParams['tabsId'] . '-tabLinks" aria-orientation="vertical">';

                            $this->content .= $this->generateTabs(false);

                $this->content .=
                        '</div>
                    </div>';
            }

            $this->content .=
                '</div>';

        } else {
            $this->tabsParams['tabsType'] =
                isset($this->params['tabsType']) ?
                $this->params['tabsType'] :
                'primary';

            if ($this->params['tabsStyle'] === 'card-tabs') {
                $this->content .=
                    '<div class="card card-' . $this->tabsParams['tabsType'] . ' card-tabs">
                        <div class="card-header" style="padding: 5px 0 0 0 !important;">';
            } else if ($this->params['tabsStyle'] === 'card-outline') {
                $this->content .=
                    '<div class="card card-' . $this->tabsParams['tabsType'] . ' card-tabs card-outline">
                        <div class="card-header p-0 pt-1 border-bottom-0">';
            } else if ($this->params['tabsStyle'] === 'card-outline-tabs') {
               $this->content .=
                    '<div class="card card-' . $this->tabsParams['tabsType'] . ' card-outline-tabs card-outline">
                        <div class="card-header p-0 border-bottom-0">';
            } else if ($this->params['tabsStyle'] === 'none') {
               $this->content .=
                    '<div class="row ' . $this->tabsParams['tabsHidden'] . '" id="' . $this->tabsParams['tabsId'] . '">
                        <div class="col">';
            }

                    $this->content .=
                        '<ul class="nav nav-tabs" role="tablist" id="' . $this->tabsParams['tabsId'] . '-tabLinks">';

                            $this->content .= $this->generateTabs(true);

                    $this->content .=
                        '</ul>';

                if ($this->params['tabsStyle'] === 'card-tabs' ||
                    $this->params['tabsStyle'] === 'card-outline' ||
                    $this->params['tabsStyle'] === 'card-outline-tabs'
                ) {
                    $this->content .= '</div>';//Card Header

                    $this->content .= '<div class="card-body">';

                }
                    if (isset($this->params['tabsAboveContent'])) {
                        $this->content .=
                            '<div id="' . $this->tabsParams['tabsId'] .'-above-content" class="tab-custom-content-above">'
                                . $this->params['tabsAboveContent'] . '
                            </div><hr>';
                    }

                    $this->content .=
                        '<div class="tab-content mt-2 mb-2" id="' . $this->tabsParams['tabsId'] . '-tabContent">';

                    $this->content .= $this->generateTabsContent();

                    $this->content .=
                        '</div>';

                    if (isset($this->params['tabsBelowContent'])) {
                        $this->content .=
                            '<hr><div id="' . $this->tabsParams['tabsId'] .'-below-content" class="tab-custom-content-below">'
                                . $this->params['tabsBelowContent'] . '
                            </div>';
                    }

                $this->content .=
                    '</div>
                </div>';//CardBody & Card Close || row & col Close
        }
    }

    protected function generateTabs($horizontal)
    {
        $content = '';

        foreach ($this->params['tabsData'] as $tabTitleKey => $tabTitle) {
            $title =
                isset($tabTitle['tabTitle']) ?
                $tabTitle['tabTitle'] :
                'Title Missing';

            if (isset($this->params['tabActiveOnInit'])) {
                if ($tabTitleKey === $this->params['tabActiveOnInit']) {
                    $aria = 'aria-selected="true"';
                    $active = 'active';
                } else {
                    $aria = 'aria-selected="false"';
                    $active = '';
                }
            } else if ($tabTitleKey === Arr::firstKey($this->params['tabsData'])) {
                $aria = 'aria-selected="true"';
                $active = 'active';
            } else {
                $aria = 'aria-selected="false"';
                $active = '';
            }

            if ($horizontal) {
                $content .= '<li class="nav-item">';
            }
            $content .=
                '<a class="nav-link text-uppercase ' . $active . '" data-toggle="pill" href="#' . $this->tabsParams['tabsId'] . '-' . $tabTitleKey . '" role="tab" aria-controls="' . $this->tabsParams['tabsId'] . '-' . $tabTitleKey . '" ' . $aria . '>' . $title . '</a>';
            if ($horizontal) {
                $content .= '</li>';
            }
        }

        return $content;
    }

    protected function generateTabsContent()
    {
        $content = '';

        foreach ($this->params['tabsData'] as $tabLinkKey => $tabLinkData) {
            if (isset($tabLinkData['tabInclude']) || isset($tabLinkData['tabContent'])) {
                if (isset($this->params['tabActiveOnInit'])) {
                    if ($tabLinkKey === $this->params['tabActiveOnInit']) {
                        $aria = 'aria-selected="true"';
                        $active = 'active show';
                    } else {
                        $aria = 'aria-selected="false"';
                        $active = '';
                    }
                } else if ($tabLinkKey === Arr::firstKey($this->params['tabsData'])) {
                    $aria = 'aria-selected="true"';
                    $active = 'active show';
                } else {
                    $aria = 'aria-selected="false"';
                    $active = '';
                }

                $content .=
                    '<div class="tab-pane fade ' . $active . '" id="'. $this->tabsParams['tabsId'] . '-' . $tabLinkKey . '" role="tabpanel" aria-labelledby="'. $this->tabsParams['tabsId'] . '-' . $tabLinkKey . '-tab" ' . $aria . '>';

                if (isset($tabLinkData['tabInclude']) && isset($tabLinkData['tabIncludeParams'])) {
                    $content .=
                        $this->view->getPartial($tabLinkData['tabInclude'], $tabLinkData['tabIncludeParams']);
                } else if (isset($tabLinkData['tabInclude'])) {
                    $content .=
                        $this->view->getPartial($tabLinkData['tabInclude']);
                } else if (isset($tabLinkData['tabContent'])) {
                    $content .= $tabLinkData['tabContent'];
                } else {
                    $content .=
                        '<span class="text-uppercase text-danger">TEMPLATE ERROR: tabInclude/tabContent missing</span>';
                }

                $content .= '</div>';
            }
        }

        return $content;
    }
}