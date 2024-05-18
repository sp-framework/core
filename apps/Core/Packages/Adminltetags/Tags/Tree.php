<?php

namespace Apps\Core\Packages\Adminltetags\Tags;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Tree extends Adminltetags
{
    protected $treeMode;

    protected $fieldParams;

    protected $content = '';

    protected $childsContent = '';

    public function getContent(array $treeParams)
    {
        if (!$this->app) {
            $this->init();
        }

        $this->fieldParams =
            isset($treeParams['fieldParams']) ?
            $treeParams['fieldParams'] :
            null;

        if (isset($treeParams['treeMode'])) {
            $this->treeMode = $treeParams['treeMode'];
        } else {
            throw new \Exception('treeMode not set.');
        }
        if ($treeParams['treeMode'] === 'jstree') {
            $this->generateContent(
                $treeParams['treeData'],
                $treeParams['groupIcon'],
                $treeParams['itemIcon']
            );
        } else {
            $this->generateContent($treeParams['treeData']);
        }

        return $this->content;
    }

    protected function getChildsContent()
    {
        return $this->childsContent;
    }

    protected function generateContent(array $treeData, string $groupIcon = null, string $itemIcon = null)
    {
        foreach ($treeData as $key => $items) {
            if (isset($treeData['children']) && $treeData['children'] === true) {
                $this->childsContent = '';

                if (isset($items['childs'])) {
                    $this->childsContent .= $this->treeGroup($key, $items, $groupIcon, $itemIcon, true);
                } else {
                    $this->childsContent .= $this->treeItem($key, $items, $itemIcon, true);
                }
            } else {
                if (isset($items['childs'])) {
                    $this->content .= $this->treeGroup($key, $items, $groupIcon, $itemIcon);
                } else {
                    $this->content .= $this->treeItem($key, $items, $itemIcon);
                }
            }
        }
    }

    protected function treeGroup($key, $items, $groupIcon, $itemIcon, $children = null)
    {
        if ($this->treeMode === 'jstree') {
            if (isset($items['icon'])) {
                $groupIcon = '{"icon" : "fa fa-fw fa-' . $items['icon'] . ' text-sm"}';
            } else {
                $groupIcon = '{"icon" : "fa fa-fw fa-plus text-sm"}';
            }

            $groupAdditionalClass =
                isset($items['groupAdditionalClass']) ?
                'class="' . $items['groupAdditionalClass'] . '"' :
                '';

            $itemsId =
                isset($items['id']) ?
                $items['id'] :
                $key;

            if (isset($items['data'])) {
                $dataAttr = '';
                foreach ($items['data'] as $dataKey => $dataValue) {
                    $dataAttr .= 'data-' . $dataKey . '="' . $dataValue . '" ';
                }
            } else {
                $dataAttr = '';
            }

            $this->content .=
                '<li ' . $groupAdditionalClass . ' ' . $dataAttr . ' data-id="' . $itemsId . '" data-jstree=\'' . $groupIcon . '\'>';

                if (isset($items['title'])) {
                    $this->content .= $items['title'];
                } else if (isset($items['name'])) {
                    $this->content .= $items['name'];
                } else if (isset($items['entry'])) {
                    $this->content .= $items['entry'];
                }

                $this->content .=
                    '<ul>';

                if (isset($items['childs'])) {
                    $this->generateContent(
                        [
                            'treeData' => $items['childs'],
                            'children' => true
                        ],
                        $groupIcon,
                        $itemIcon
                    );

                    $this->content .= $this->getChildsContent();
                }

                $this->content .=
                    '</ul>';

            $this->content .=
                '</li>';
        } else if ($this->treeMode === 'sideMenu') {
            $itemIcon =
                isset($items['icon']) ?
                $items['icon'] :
                'circle-dot';

            $itemTitle =
                isset($items['title']) ?
                $items['title'] :
                $key;

            $this->content .=
                '<li class="nav-item has-treeview">
                    <a href="/#" class="nav-link">
                        <i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
                    <p class="text-uppercase">' . $itemTitle . '
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">';

                $this->generateContent(
                    [
                        'treeData' => $items['childs'],
                        'children' => true
                    ]
                );

                $this->content .= $this->getChildsContent();

                $this->content .= '</ul></li>';
        } else if ($this->treeMode === 'topMenu') {
            $itemIcon =
                isset($items['icon']) ?
                $items['icon'] :
                'circle-dot';

            $itemTitle =
                isset($items['title']) ?
                $items['title'] :
                $key;

            $this->content .=
                '<li class="nav-item dropdown">
                    <a href="/#" class="dropdown-item dropdown-toggle">
                        <i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
                    <span class="text-uppercase mb-0">' . $itemTitle . '</span>
                </a>
                <ul class="dropdown-menu">';

                $this->generateContent(
                    [
                        'treeData' => $items['childs'],
                        'children' => true
                    ]
                );

                $this->content .= $this->getChildsContent();

                $this->content .= '</ul></li>';
        } else if ($this->treeMode === 'select2') {
            if (isset($items['value'])) {
                $this->content .=
                    '<optgroup label="' . strtoupper($items['value']) . '">';
            }

            $this->generateContent(
                [
                    'treeData' => $items['childs'],
                    'children' => true
                ]
            );

            $this->content .= $this->getChildsContent();

            if (isset($items['value'])) {
                $this->content .=
                    '</optgroup>';
            }
        }
    }

    protected function treeItem($key, $items, $itemIcon, $children = null)
    {
        $itemAdditionalClass =
            isset($items['itemAdditionalClass']) ?
            $items['itemAdditionalClass'] :
            '';

        if ($this->treeMode === 'jstree') {
            if (is_array($items)) {
                if (isset($items['title'])) {
                    $this->generateItemContent($key, $items, $itemAdditionalClass);
                } else {
                    foreach ($items as $itemKey => $itemValue) {
                        if (isset($itemValue['childs'])) {
                            $this->content .=
                                $this->treeGroup($itemKey, $itemValue, '{"icon" : "fa fa-fw fa-plus text-sm"}', '{"icon" : "fa fa-fw fa-circle-dot text-sm"}', null);
                        } else {
                            $this->generateItemContent($itemKey, $itemValue, $itemAdditionalClass);
                        }
                    }
                }
            }
        } else if ($this->treeMode === 'sideMenu') {
            if (is_array($items)) {
                if (!$children) {
                    $itemIcon =
                        isset($items['icon']) ?
                        $items['icon'] :
                        'circle-dot';


                    $this->content .=
                        '<li class="nav-item">';

                        if ($this->app['id'] == $this->domains->domain['exclusive_to_default_app']) {
                            $this->content .=
                                '<a class="nav-link contentAjaxLink" href="/' . $items['link'] . '">';
                        } else {
                            $this->content .=
                                '<a class="nav-link contentAjaxLink" href="/' . $this->app['route'] . '/' . $items['link'] . '">';
                        }

                    $this->content .=
                                '<i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
                                <p class="text-uppercase">';

                    if (isset($items['title'])) {
                        $this->content .= $items['title'];
                    } else if (isset($items['name'])) {
                        $this->content .= $items['name'];
                    } else if (isset($items['entry'])) {
                        $this->content .= $items['entry'];
                    }

                    $this->content .= '</p></a></li>';
                } else if ($children) {
                    foreach ($items as $itemKey => $itemValue) {
                        if (isset($itemValue['childs'])) {
                            $this->content .= $this->treeGroup($itemKey, $itemValue, null, null, null);
                        } else {
                            $itemIcon =
                                isset($itemValue['icon']) ?
                                $itemValue['icon'] :
                                'circle-dot';

                            $this->content .=
                                '<li class="nav-item">';

                                if ($this->app['id'] == $this->domains->domain['exclusive_to_default_app']) {
                                    $this->content .=
                                        '<a class="nav-link contentAjaxLink" href="/' . $itemValue['link'] . '">';
                                } else {
                                    $this->content .=
                                        '<a class="nav-link contentAjaxLink" href="/' . $this->app['route'] . '/' . $itemValue['link'] . '">';
                                }

                            $this->content .=
                                '<i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
                                <p class="text-uppercase">';

                            if (isset($itemValue['title'])) {
                                $this->content .= $itemValue['title'];
                            } else if (isset($itemValue['name'])) {
                                $this->content .= $itemValue['name'];
                            } else if (isset($itemValue['entry'])) {
                                $this->content .= $itemValue['entry'];
                            }

                            $this->content .= '</p></a></li>';
                        }
                    }
                }
            }
        } else if ($this->treeMode === 'topMenu') {
            if (is_array($items)) {
                if (!$children) {
                    $itemIcon =
                        isset($items['icon']) ?
                        $items['icon'] :
                        'circle-dot';

                    $this->content .=
                        '<li class="nav-item">';

                        if ($this->app['id'] == $this->domains->domain['exclusive_to_default_app']) {
                            $this->content .=
                                '<a class="nav-link contentAjaxLink text-center" href="/' . $items['link'] . '">';
                        } else {
                            $this->content .=
                                '<a class="nav-link contentAjaxLink text-center" href="/' . $this->app['route'] . '/' . $items['link'] . '">';
                        }

                    $this->content .=
                                '<i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
                                <span class="text-uppercase mb-0">';

                    if (isset($items['title'])) {
                        $this->content .= $items['title'];
                    } else if (isset($items['name'])) {
                        $this->content .= $items['name'];
                    } else if (isset($items['entry'])) {
                        $this->content .= $items['entry'];
                    }

                    $this->content .= '</span></a></li>';
                } else if ($children) {
                    foreach ($items as $itemKey => $itemValue) {
                        if (isset($itemValue['childs'])) {
                            $this->content .= $this->treeGroup($itemKey, $itemValue, null, null, null);
                        } else {
                            $itemIcon =
                                isset($itemValue['icon']) ?
                                $itemValue['icon'] :
                                'circle-dot';

                            $this->content .=
                                '<li class="nav-item">';

                                if ($this->app['id'] == $this->domains->domain['exclusive_to_default_app']) {
                                    $this->content .=
                                        '<a class="nav-link contentAjaxLink" href="/' . $itemValue['link'] . '">';
                                } else {
                                    $this->content .=
                                        '<a class="nav-link contentAjaxLink" href="/' . $this->app['route'] . '/' . $itemValue['link'] . '">';
                                }

                            $this->content .=
                                '<i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
                                <span class="text-uppercase mb-0">';

                            if (isset($itemValue['title'])) {
                                $this->content .= $itemValue['title'];
                            } else if (isset($itemValue['name'])) {
                                $this->content .= $itemValue['name'];
                            } else if (isset($itemValue['entry'])) {
                                $this->content .= $itemValue['entry'];
                            }

                            $this->content .= '</span></a></li>';
                        }
                    }
                }
            }
        } else if ($this->treeMode === 'select' || $this->treeMode === 'select2') {
            if ($this->treeMode === 'select') {
                $selectType = '';
            } else {
                $selectType = '2';
            }

            if (is_array($items)) {
                foreach ($items as $itemKey => $itemValue) {
                    // $hasKeyValue = '';
                    // $hasValue = '';
                    // $hasValueText = '';

                    if (is_array($itemValue)) {
                        if (isset($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsKey']) &&
                            isset($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue'])
                        ) {
                            if (is_string($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue'])) {
                                $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue'] = explode('|', $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue']);
                            }

                            $key = $itemValue[$this->fieldParams['fieldDataSelect' . $selectType . 'OptionsKey']];

                            if (count($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue']) === 1) {
                                $value = $itemValue[$this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue'][0]];
                            } else {
                                foreach ($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue'] as $optionsValueKey) {
                                    if (is_string($optionsValueKey)) {
                                        $optionsValueKey = explode(':', $optionsValueKey);
                                    }

                                    if (count($optionsValueKey) === 1) {
                                        $optionsValueKey = $optionsValueKey[0];

                                        if (isset($itemValue[$optionsValueKey])) {
                                            $value = $itemValue[$optionsValueKey];

                                            break;
                                        }
                                    } else {
                                        if (isset($itemValue[$optionsValueKey[0]]) && isset($itemValue[$optionsValueKey[1]])) {
                                            $value = $itemValue[$optionsValueKey[0]] . ' (' . $itemValue[$optionsValueKey[1]] . ')';

                                            break;
                                        }
                                    }
                                }

                                if (!isset($value)) {
                                    $value = 'INCORRECT KEYS SET FOR fieldDataSelect2OptionsValue';
                                }
                            }
                        // }
                        // if ($itemKey === $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsKey']) {

                        //     $hasKeyValue = $itemValue;
                        // } else if ($itemKey === $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue']) {

                        //     $hasValue = $itemValue;

                        //     $hasValueText = $itemValue;
                        // } else {
                            // $hasKeyValue = $itemKey;
                            // $hasValue = $itemValue;
                            // $hasValueText = $itemValue;
                        }
                        // var_dump($itemValue['data']);
                        if (isset($itemValue['data'])) {
                            $dataAttr = '';
                            foreach ($itemValue['data'] as $dataKey => $dataValue) {
                                $dataAttr .= 'data-' . $dataKey . '="' . $dataValue . '" ';
                            }
                        } else {
                            $dataAttr = '';
                        }
                        // if (isset($itemValue['dataType'])) {
                        //     $dataType = 'data-datatype="' . $itemValue['dataType'] . '"';
                        // } else {
                        //     $dataType = '';
                        // }

                        // if (isset($itemValue['numeric'])) {
                        //     $numeric = 'data-numeric="' . $itemValue['numeric'] . '"';
                        // } else {
                        //     $numeric = '';
                        // }
                        // var_dump($key, $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected']);

                        if (is_array($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected']) &&
                            count($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected']) > 0
                        ) {
                            if (in_array($key, $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected'])) {
                                $this->content .=
                                    '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '" selected>' . $value . '</option>';
                            } else {
                                $this->content .=
                                    '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '">' . $value . '</option>';
                            }

                            // foreach ($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected'] as $selectArrKey => $selectArrValue) {
                            //     if ($key == $selectArrValue) {
                                // } else {
                                //     $this->content .=
                                //         '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '">' . $value . '</option>';
                            //     }
                            // }
                        } else {
                            if ($key == $this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected']) {
                                $this->content .=
                                    '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '" selected>' . $value . '</option>';
                            } else {
                                $this->content .=
                                    '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '">' . $value . '</option>';
                            }
                        }
                    }
                }
            }
        }
    }

    protected function generateItemContent($key, array $item, $itemAdditionalClass)
    {
        $itemId =
            isset($item['id']) ?
            $item['id'] :
            $key;

        if (isset($item['type']) &&
            $item['type'] === 'pdf'
        ) {
            $itemType = $item['type'];
            $itemIcon = "{'icon' : 'fa fa-fw fa-file-pdf text-sm'}";

        } else if ((isset($item['type']) && $item['type'] === 'jpg') ||
                   (isset($item['type']) && $item['type'] === 'png')
        ) {
            $itemType = $item['type'];
            $itemIcon = "{'icon' : 'fa fa-fw fa-file-image text-sm'}";

        } else {
            $itemType = '';
        }

        if (isset($item['icon'])) {
            $itemIcon = '{"icon" : "fa fa-fw fa-' . $item["icon"] . ' text-sm"}';
        } else {
            $itemIcon = '{"icon" : "fa fa-fw fa-circle-dot text-sm"}';
        }

        if (isset($item['link'])) {
            $itemLink = $item['link'];
        } else {
            $itemLink = '#';
        }

        if (isset($item['data'])) {
            $dataAttr = '';
            foreach ($item['data'] as $dataKey => $dataValue) {
                $dataAttr .= 'data-' . $dataKey . '="' . $dataValue . '" ';
            }
        } else {
            $dataAttr = '';
        }

        $this->content .=
            '<li class="' . $itemAdditionalClass . '" ' . $dataAttr . ' data-link="' . $itemLink . '" data-id="' . $itemId . '" data-file-type="' . $itemType . '" data-jstree=\'' . $itemIcon . '\'>';

            if (isset($item['title'])) {
                $this->content .= $item['title'];
            } else if (isset($item['name'])) {
                $this->content .= $item['name'];
            } else if (isset($item['entry'])) {
                $this->content .= $item['entry'];
            }

        $this->content .= '</li>';
    }
}
// 3 layer Menu Example
// {"users":{"title":"users","icon":"users","childs":{"accounts":{"title":"accounts","childs":{"account":{"title":"account","link":"account"}}}}}}