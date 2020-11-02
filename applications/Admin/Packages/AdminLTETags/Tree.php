<?php

namespace Applications\Admin\Packages\AdminLTETags;

use Applications\Admin\Packages\AdminLTETags;

class Tree extends AdminLTETags
{
    protected $treeMode;

    protected $fieldParams;

    protected $content = '';

    protected $childsContent = '';

    public function getContent(array $treeParams)
    {
        $this->treeMode = $treeParams['treeMode'];

        $this->fieldParams =
            isset($treeParams['fieldParams']) ?
            $treeParams['fieldParams'] :
            null;

        $this->generateContent($treeParams['treeData']);

        return $this->content;
    }

    protected function getChildsContent()
    {
        return $this->childsContent;
    }

    protected function generateContent(array $treeData)
    {
        foreach ($treeData as $key => $items) {
            if (isset($items['children'])) {
                if ($items['children'] === true) {

                    $this->childsContent = '';

                    if (isset($items['childs'])) {
                        $this->childsContent .= $this->treeGroup($key, $items);
                    } else {
                        $this->childsContent .= $this->treeItem($key, $items);
                    }
                }
            } else {
                if (isset($items['childs'])) {
                    $this->content .= $this->treeGroup($key, $items);
                } else {
                    $this->content .= $this->treeItem($key, $items);
                }
            }
        }
    }

    protected function treeGroup($key, $items)
    {
        if ($this->treeMode === 'jstree') {
            $groupAdditionalClass =
                isset($items['groupAdditionalClass']) ?
                'class="' . $items['groupAdditionalClass'] . '"' :
                '';

            $groupIcon =
                isset($items['groupIcon']) ?
                $items['groupIcon'] :
                '';

            $this->content .=
                '<li ' . $groupAdditionalClass . ' data-id="' . $items['id'] . '" data-jstree="' . $groupIcon . '">';

            if (isset($items['title'])) {
                $this->content .= $items['title'];
            } else if (isset($items['name'])) {
                $this->content .= $items['name'];
            } else if (isset($items['entry'])) {
                $this->content .= $items['entry'];
            }

            $this->contnet .=
                '<ul>';

            if (isset($items['childs'])) {
                $this->generateContent(
                    'select2',
                    [
                        'treeData' => $items['childs'],
                        'children' => true
                    ]
                );

                $this->content .= $this->getChildsContent();
            }

            $this->contnet .=
                '</ul>';

            $this->contnet .=
                '</li>';
        } else if ($this->treeMode === 'sideMenu') {
            $icon =
                isset($items['icon']) ?
                $items['icon'] :
                'circle';

            $this->content .=
                '<li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fas fa-fw fa-' . $icon . ' nav-icon"></i>
                    <p class="text-uppercase">' . $key . '
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">';

                $this->generateContent(
                    'sideMenu',
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
                'select2',
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

    protected function treeItem($key, $items)
    {
        $itemAdditionalClass =
            isset($items['itemAdditionalClass']) ?
            $items['itemAdditionalClass'] :
            '';

        if ($this->treeMode === 'jstree') {
            if ($items['type'] === 'pdf') {
                $itemIcon = '{"icon" : "fas fa-fw fa-file-pdf text-sm"}';
            } else if ($items['type'] === 'jpg' || $items['type'] === 'png') {
                $itemIcon = '{"icon" : "fas fa-fw fa-file-image text-sm"}';
            } else {
                $itemIcon = '';
            }
            $this->content .=
                '<li class="' . $itemAdditionalClass . '" data-id="' . $items['id'] . '" data-file-type="' . $items['type'] . '" data-jstree="' . $itemIcon . '">';

                if (isset($items['title'])) {
                    $this->content .= $items['title'];
                } else if (isset($items['name'])) {
                    $this->content .= $items['name'];
                } else if (isset($items['entry'])) {
                    $this->content .= $items['entry'];
                }

            $this->content .= '</li>';

        } else if ($this->treeMode === 'sideMenu') {

            $icon =
                isset($items['icon']) ?
                $items['icon'] :
                'circle';

            $this->content .=
                '<li class="nav-item">
                    <a class="nav-link contentAjaxLink" href="' . $this->links->url($items['link']) . '">
                        <i class="fas fa-fw fa-' . $icon . ' nav-icon"></i>
                        <p class="text-uppercase">';

            if (isset($items['title'])) {
                $this->content .= $items['title'];
            } else if (isset($items['name'])) {
                $this->content .= $items['name'];
            } else if (isset($items['entry'])) {
                $this->content .= $items['entry'];
            }

            $this->content .= '</p></a></li>';

        } else if ($this->treeMode == 'select2') {

            foreach ($items as $itemKey => $itemValue) {
                // var_dump($itemKey, $itemValue);
                // $hasKeyValue = '';
                // $hasValue = '';
                // $hasValueText = '';

                if (isset($this->fieldParams['fieldDataSelect2OptionsKey']) &&
                    isset($this->fieldParams['fieldDataSelect2OptionsValue'])
                ) {
                    $key = $itemValue[$this->fieldParams['fieldDataSelect2OptionsKey']];
                    $value = $itemValue[$this->fieldParams['fieldDataSelect2OptionsValue']];
                // }
                // if ($itemKey === $this->fieldParams['fieldDataSelect2OptionsKey']) {

                //     $hasKeyValue = $itemValue;
                // } else if ($itemKey === $this->fieldParams['fieldDataSelect2OptionsValue']) {

                //     $hasValue = $itemValue;

                //     $hasValueText = $itemValue;
                // } else {
                    // $hasKeyValue = $itemKey;
                    // $hasValue = $itemValue;
                    // $hasValueText = $itemValue;
                }

                if (isset($itemValue['dataType'])) {
                    $dataType = 'data-datatype="' . $itemValue['dataType'] . '"';
                } else {
                    $dataType = '';
                }

                if (isset($itemValue['numeric'])) {
                    $numeric = 'data-numeric="' . $itemValue['numeric'] . '"';
                } else {
                    $numeric = '';
                }

                if ($itemKey === $this->fieldParams['fieldDataSelect2OptionsSelected']) {
                    $this->content .=
                        '<option ' . $dataType . ' ' . $numeric . ' data-value="' . $key . '" value="' . $key . '" selected>' . $value . '</option>';
                } else {
                    $this->content .=
                        '<option ' . $dataType . ' ' . $numeric . ' data-value="' . $key . '" value="' . $key . '">' .$value . '</option>';
                }
            }
        }
    }
}