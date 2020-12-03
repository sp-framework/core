<?php

namespace Applications\Ecom\Admin\Packages\AdminLTETags\Tags;

use Applications\Ecom\Admin\Packages\AdminLTETags\AdminLTETags;

class Tree extends AdminLTETags
{
    protected $treeMode;

    protected $fieldParams;

    protected $content = '';

    protected $childsContent = '';

    public function getContent(array $treeParams)
    {
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
            $groupAdditionalClass =
                isset($items['groupAdditionalClass']) ?
                'class="' . $items['groupAdditionalClass'] . '"' :
                '';

            $itemsId =
                isset($items['id']) ?
                $items['id'] :
                '';

            $this->content .=
                '<li ' . $groupAdditionalClass . ' data-id="' . $itemsId . '" data-jstree=\'' . $groupIcon . '\'>';

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
                'circle';

            $itemTitle =
                isset($items['title']) ?
                $items['title'] :
                $key;

            $this->content .=
                '<li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
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

        } else if ($this->treeMode === 'select2') {

            if (isset($items['value'])) {
                $this->content .=
                    '<optgroup label="' . $items['value'] . '">';
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
                foreach ($items as $itemKey => $itemValue) {

                    if (isset($itemValue['childs'])) {
                        $this->content .= $this->treeGroup($itemKey, $itemValue, '{"icon" : "fa fa-fw fa-plus text-sm"}', '{"icon" : "fa fa-fw fa-circle text-sm"}', null);

                    } else {
                        $itemId =
                            isset($itemValue['id']) ?
                            $itemValue['id'] :
                            '';

                        if (isset($itemValue['type']) &&
                            $itemValue['type'] === 'pdf'
                        ) {

                            $itemType = $itemValue['type'];
                            $itemIcon = "{'icon' : 'fa fa-fw fa-file-pdf text-sm'}";

                        } else if ((isset($itemValue['type']) && $itemValue['type'] === 'jpg') ||
                                   (isset($itemValue['type']) && $itemValue['type'] === 'png')
                        ) {

                            $itemType = $itemValue['type'];
                            $itemIcon = "{'icon' : 'fa fa-fw fa-file-image text-sm'}";

                        } else {

                            $itemType = '';
                        }

                        $this->content .=
                            '<li class="' . $itemAdditionalClass . '" data-id="' . $itemId . '" data-file-type="' . $itemType . '" data-jstree=\'' . $itemIcon . '\'>';

                            if (isset($itemValue['title'])) {
                                $this->content .= $itemValue['title'];
                            } else if (isset($itemValue['name'])) {
                                $this->content .= $itemValue['name'];
                            } else if (isset($itemValue['entry'])) {
                                $this->content .= $itemValue['entry'];
                            }

                        $this->content .= '</li>';
                    }
                }
            }

        } else if ($this->treeMode === 'sideMenu') {
            if (is_array($items)) {
                if (!$children) {
                    $itemIcon =
                        isset($items['icon']) ?
                        $items['icon'] :
                        'circle';

                    $this->content .=
                        '<li class="nav-item">
                            <a class="nav-link contentAjaxLink" href="' . $this->links->url($items['link']) . '">
                                <i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
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
                                'circle';

                            $this->content .=
                                '<li class="nav-item">
                                    <a class="nav-link contentAjaxLink" href="' . $this->links->url($itemValue['link']) . '">
                                        <i class="fa fa-fw fa-' . $itemIcon . ' nav-icon"></i>
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

        } else if ($this->treeMode === 'select' || $this->treeMode === 'select2') {
            if ($this->treeMode === 'select') {
                $selectType = '';
            } else {
                $selectType = '2';
            }

            foreach ($items as $itemKey => $itemValue) {
                // var_dump($itemKey, $itemValue);
                // $hasKeyValue = '';
                // $hasValue = '';
                // $hasValueText = '';

                if (isset($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsKey']) &&
                    isset($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue'])
                ) {
                    $key = $itemValue[$this->fieldParams['fieldDataSelect' . $selectType . 'OptionsKey']];
                    $value = $itemValue[$this->fieldParams['fieldDataSelect' . $selectType . 'OptionsValue']];
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
                    foreach ($this->fieldParams['fieldDataSelect' . $selectType . 'OptionsSelected'] as $selectArrKey => $selectArrValue) {
                        if ($key == $selectArrValue) {
                            $this->content .=
                                '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '" selected>' . $value . '</option>';
                        } else {
                            $this->content .=
                                '<option ' . $dataAttr . ' data-value="' . $key . '" value="' . $key . '">' . $value . '</option>';
                        }
                    }
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
// 3 layer Menu Example
// {"users":{"title":"users","icon":"users","childs":{"accounts":{"title":"accounts","childs":{"account":{"title":"account","link":"account"}}}}}}