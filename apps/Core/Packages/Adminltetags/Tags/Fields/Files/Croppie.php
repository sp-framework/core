<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Fields\Files;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Croppie
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $params;

    protected $fieldParams;

    protected $content;

    protected $adminLTETags;

    protected $compSecId;

    public function __construct($view, $tag, $links, $escaper, $params, $fieldParams)
    {
        $this->adminLTETags = new Adminltetags();

        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->params = $params;

        $this->fieldParams = $fieldParams;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        $this->fieldParams['fieldCroppieLabel'] =
            isset($this->params['fieldCroppieLabel']) ?
            $this->params['fieldCroppieLabel'] :
            false;

        $this->fieldParams['fieldHelpTooltipContent'] =
            isset($this->params['fieldHelpTooltipContent']) ?
            $this->params['fieldHelpTooltipContent'] :
            '';

        $this->fieldParams['fieldRequired'] =
            isset($this->params['fieldRequired']) && $this->params['fieldRequired'] === true ?
            true :
            false;

        $this->fieldParams['fieldBazJstreeSearch'] =
            isset($this->params['fieldBazJstreeSearch']) && $this->params['fieldBazJstreeSearch'] === true ?
            true :
            false;

        $this->fieldParams['uploadDirectory'] =
            isset($this->params['uploadDirectory']) ?
            $this->params['uploadDirectory'] = strtolower($this->params['uploadDirectory']) :
            $this->params['uploadDirectory'] = strtolower($this->params['componentName']);

        if (isset($this->params['isPointer']) && $this->params['isPointer'] === true) {
            $this->params['isPointer'] = 'true';
        } else {
            $this->params['isPointer'] = 'false';
        }

        if (isset($this->params['setOrphan']) && $this->params['setOrphan'] === false) {
            $this->params['setOrphan'] = 'false';
        } else {
            $this->params['setOrphan'] = 'true';
        }

        if (!isset($this->params['fieldValue'])) {
            $this->params['fieldValue'] = '';
        }

        if (!isset($this->params['storageType'])) {
            $this->params['storageType'] = 'public';
        }

        if (!isset($this->params['imageType'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">imageType missing. Set imageType as logo or image</span>';
            return;
        }

        if (!isset($this->params['fieldCroppieSize'])) {
            $this->params['fieldCroppieSize'] = 'viewport';//sizes: viewport, original
        }

        if (!isset($this->params['fieldCroppieFormat'])) {
            $this->params['fieldCroppieFormat'] = 'png';//formats: png, jpeg, webp
        }

        if (isset($this->params['fieldCroppieViewportCircle']) && $this->params['fieldCroppieViewportCircle'] === true) {
            $this->fieldParams['fieldCroppieViewportCircle'] = 'true';//circle: true/false
            $this->fieldParams['fieldCroppieViewportType'] = 'circle';
        } else {
            $this->fieldParams['fieldCroppieViewportCircle'] = 'false';//circle: true/false
            $this->fieldParams['fieldCroppieViewportType'] = 'square';
        }

        if (!isset($this->params['maxWidth']) ||
            isset($this->params['maxWidth']) && $this->params['maxWidth'] == 0
        ) {
            $this->params['maxWidth'] = 200;
        }
        $this->params['viewportWidth'] = $this->params['maxWidth'];
        $this->params['boundaryWidth'] = $this->params['maxWidth'] + 50;

        if (!isset($this->params['maxHeight']) ||
            isset($this->params['maxHeight']) && $this->params['maxHeight'] == 0
        ) {
            $this->params['maxHeight'] = 200;
        }
        $this->params['viewportHeight'] = $this->params['maxHeight'];
        $this->params['boundaryHeight'] = $this->params['maxHeight'] + 50;

        $croppieButtons = [];

        if (isset($this->params['upload']) && $this->params['upload'] === true) {
            $croppieButtons =
                array_merge(
                    $croppieButtons,
                    [
                        $this->params['fieldId'] . '-croppie-upload' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'upload',
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1 btn-file',
                            'content'                   =>
                                '<input type="file" class="ignore" style="cursor: pointer;opacity: 0;font-size: 0;" id="' .
                                $this->compSecId . '-' . $this->params['fieldId'] .
                                '-croppie-upload-image" value="Choose a file" accept="image/*"/>'
                        ],
                        $this->params['fieldId'] . '-croppie-save' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'save',
                            'hidden'                    => true,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        $this->params['fieldId'] . '-croppie-cancel' =>
                        [
                            'title'                     => false,
                            'type'                      => 'secondary',
                            'position'                  => 'left',
                            'icon'                      => 'times',
                            'hidden'                    => true,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ]
                    ]
                );
        }

        if (isset($this->params['avatar']) && $this->params['avatar'] === true) {
            $croppieButtons =
                array_merge(
                    $croppieButtons,
                    [
                        $this->params['fieldId'] . '-croppie-avatar-male' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'male',
                            'hidden'                    => false,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        $this->params['fieldId'] . '-croppie-avatar-female' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'female',
                            'hidden'                    => false,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        $this->params['fieldId'] . '-croppie-avatar-refresh' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'redo',
                            'hidden'                    => true,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        $this->params['fieldId'] . '-croppie-avatar-save' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'save',
                            'hidden'                    => true,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ]
                    ]
                );
        }

        if (isset($this->params['recover']) && $this->params['recover'] === true) {
            $croppieButtons =
                array_merge(
                    $croppieButtons,
                    [
                        $this->params['fieldId'] . '-croppie-avatar-recover' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'history',
                            'hidden'                    => false,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ]
                    ]
                );
        }

        if (isset($this->params['remove']) && $this->params['remove'] === true) {
            $croppieButtons =
                array_merge(
                    $croppieButtons,
                    [
                        $this->params['fieldId'] . '-croppie-remove' =>
                        [
                            'title'                     => false,
                            'type'                      => 'danger',
                            'position'                  => 'left',
                            'icon'                      => 'trash',
                            'hidden'                    => true,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ]
                    ]
                );
        }

        $this->content .=
            '<div class="row">
                <div class="col mb-2">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                     => $this->params['component'],
                            'componentName'                 => $this->params['componentName'],
                            'componentId'                   => $this->params['componentId'],
                            'sectionId'                     => $this->params['sectionId'],
                            'fieldId'                       => $this->params['fieldId'] . '-label',
                            'fieldLabel'                    => $this->fieldParams['fieldCroppieLabel'],
                            'fieldType'                     => 'html',
                            'fieldHelp'                     => true,
                            'fieldHelpTooltipContent'       => $this->fieldParams['fieldHelpTooltipContent'],
                            'fieldAdditionalClass'          => 'mb-0',
                            'fieldRequired'                 => $this->fieldParams['fieldRequired'],
                            'fieldBazScan'                  => true,
                            'fieldBazJstreeSearch'          => $this->fieldParams['fieldBazJstreeSearch'],
                            'fieldBazPostOnCreate'          => false,
                            'fieldBazPostOnUpdate'          => false
                        ]
                    ) .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                     => $this->params['component'],
                            'componentName'                 => $this->params['componentName'],
                            'componentId'                   => $this->params['componentId'],
                            'sectionId'                     => $this->params['sectionId'],
                            'fieldId'                       => $this->params['fieldId'],
                            'fieldLabel'                    => false,
                            'fieldType'                     => 'input',
                            'fieldHelp'                     => true,
                            'fieldHelpTooltipContent'       => false,
                            'fieldRequired'                 => true,
                            'fieldBazScan'                  => true,
                            'fieldBazPostOnCreate'          => true,
                            'fieldBazPostOnUpdate'          => true,
                            'fieldHidden'                   => true,
                            'fieldDisabled'                 => true,
                            'fieldValue'                    => $this->params['fieldValue']
                        ]
                    ) .
                    $this->adminLTETags->useTag('buttons',
                        [
                            'componentId'                   => $this->params['componentId'],
                            'sectionId'                     => $this->params['sectionId'],
                            'buttonLabel'                   => false,
                            'buttonType'                    => 'button',
                            'buttons'                       => $croppieButtons
                        ]
                    ) .
                    '<span id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save-warning" class="text-danger" hidden>Save/Cancel Image</span>
                </div>
            </div>';

        if (isset($this->params['avatar']) && $this->params['avatar'] === true) {
            $this->content .=
                '<div class="row">
                    <div class="col mb-2">' .
                        $this->adminLTETags->useTag('fields',
                            [
                                'component'                     => $this->params['component'],
                                'componentName'                 => $this->params['componentName'],
                                'componentId'                   => $this->params['componentId'],
                                'sectionId'                     => $this->params['sectionId'],
                                'fieldId'                       => $this->params['fieldId'] . '-croppie-avatar-filename',
                                'fieldLabel'                    => 'Avatar Filename',
                                'fieldType'                     => 'input',
                                'fieldHelp'                     => true,
                                'fieldHelpTooltipContent'       => 'Enter avatar filename to recover.',
                                'fieldRequired'                 => false,
                                'fieldBazScan'                  => true,
                                'fieldBazPostOnCreate'          => false,
                                'fieldBazPostOnUpdate'          => false,
                                'fieldHidden'                   => true,
                                'fieldDisabled'                 => true,
                                'fieldValue'                    => '',
                                'fieldGroupPostAddonButtons'    =>
                                    [
                                        $this->params['fieldId'] . '-croppie-avatar-search' => [
                                            'title'                   => false,
                                            'type'                    => 'primary',
                                            'icon'                    => 'search',
                                            'noMargin'                => true,
                                            'buttonAdditionalClass'   => 'rounded-0 text-white',
                                            'position'                => 'right'
                                        ],
                                        $this->params['fieldId'] . '-croppie-avatar-cancel' => [
                                            'title'                   => false,
                                            'type'                    => 'secondary',
                                            'icon'                    => 'times',
                                            'noMargin'                => true,
                                            'buttonAdditionalClass'   => 'rounded-0 text-white',
                                            'position'                => 'right'
                                        ]
                                    ]
                            ]
                        ) .
                    '</div>
                </div>';
        }

        $this->content .= '<div class="text-center image-content ' . $this->compSecId . '-' . $this->params['fieldId'] . '-image-content">';

        $fileName = $this->adminLTETags->basepackages->storages->getFileInfo($this->params['fieldValue']);

        if ($fileName) {
            $fileName = $fileName['org_file_name'];
        } else {
            if (!isset($this->params['fieldValue']) || $this->params['fieldValue'] === '') {
                if ($this->params['imageType'] === 'logo') {
                    $fileName = $this->links->images('general/logo.png');
                } else if ($this->params['imageType'] === 'image') {
                    $fileName = $this->links->images('general/img.png');
                } else if ($this->params['imageType'] === 'portrait') {
                    if (isset($this->params['initialsAvatar']) &&
                        is_array($this->params['initialsAvatar']) &&
                        isset($this->params['initialsAvatar']['large'])
                    ) {
                        $fileName = 'Initials Avatar';
                    } else {
                        $fileName = $this->links->images('general/portrait.png');
                    }
                }
            } else {
                if (!str_starts_with($this->params['fieldValue'], 'http')) {
                    $this->params['fieldValue'] = ltrim($this->params['fieldValue'], '/');
                    if (str_starts_with($this->params['fieldValue'], 'public')) {
                        $this->params['fieldValue'] = str_replace('public/', '', $this->params['fieldValue']);
                        $fileName = $this->links->url($this->params['fieldValue'], true);
                    } else {
                        $fileName = $this->links->images($this->params['fieldValue']);
                    }
                } else {
                    $fileName = $this->params['fieldValue'];
                }
            }
        }

        if ($this->params['imageType'] === 'logo') {
            if (isset($this->params['logoLink']) &&
                ($this->params['logoLink'] !== '' && $this->params['logoLink'] !== '#')
            ) {
                if (!str_starts_with($this->params['logoLink'], 'http')) {
                    $this->params['logoLink'] = ltrim($this->params['logoLink'], '/');
                    if (str_starts_with($this->params['logoLink'], 'public')) {
                        $this->params['logoLink'] = str_replace('public/', '', $this->params['logoLink']);
                        $this->params['logoLink'] = $this->links->url($this->params['logoLink'], true);
                    } else {
                        $this->params['logoLink'] = $this->links->images($this->params['logoLink']);
                    }
                }

                $this->content .=
                    '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="logo" data-type="logo" data-orgimage="' . $this->links->images('general/logo.png') . '" src="' . $this->params['logoLink'] .'" class="user-image img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="logo" data-type="logo" data-orgimage="' . $this->links->images('general/logo.png') . '" src="' . $this->links->images('general/logo.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
            }
        } else if ($this->params['imageType'] === 'image') {
            if (isset($this->params['imageLink']) &&
                ($this->params['imageLink'] !== '' && $this->params['imageLink'] !== '#')
        ) {
                if (!str_starts_with($this->params['imageLink'], 'http')) {
                    $this->params['imageLink'] = ltrim($this->params['imageLink'], '/');
                    if (str_starts_with($this->params['imageLink'], 'public')) {
                        $this->params['imageLink'] = str_replace('public/', '', $this->params['imageLink']);
                        $this->params['imageLink'] = $this->links->url($this->params['imageLink'], true);
                    } else {
                        $this->params['imageLink'] = $this->links->images($this->params['imageLink']);
                    }
                }

                $this->content .=
                    '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="image" data-type="image" data-orgimage="' . $this->links->images('general/img.png') . '" src="' . $this->params['imageLink'] . '" class="user-image img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="image" data-type="image" data-orgimage="' . $this->links->images('general/img.png') . '" src="' . $this->links->images('general/img.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
            }
        } else if ($this->params['imageType'] === 'portrait') {
            if (isset($this->params['portraitLink']) &&
                ($this->params['portraitLink'] !== '' && $this->params['portraitLink'] !== '#')
            ) {
                if (!str_starts_with($this->params['portraitLink'], 'http')) {
                    $this->params['portraitLink'] = ltrim($this->params['portraitLink'], '/');
                    if (str_starts_with($this->params['portraitLink'], 'public')) {
                        $this->params['portraitLink'] = str_replace('public/', '', $this->params['portraitLink']);
                        $this->params['portraitLink'] = $this->links->url($this->params['portraitLink'], true);
                    } else {
                        $this->params['portraitLink'] = $this->links->images($this->params['portraitLink']);
                    }
                }

                if (isset($this->params['initialsAvatar']) &&
                    is_array($this->params['initialsAvatar']) &&
                    isset($this->params['initialsAvatar']['large'])
                ) {
                    $this->content .=
                        '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="portrait" data-type="portrait" data-orgimage="data:image/png;base64,' . $this->params['initialsAvatar']['large'] . '" src="' . $this->params['portraitLink'] . '" class="user-portrait img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
                } else {
                    $this->content .=
                        '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->params['portraitLink'] . '" class="user-portrait img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
                }
            } else {
                if (isset($this->params['initialsAvatar']) &&
                    is_array($this->params['initialsAvatar']) &&
                    isset($this->params['initialsAvatar']['large'])
                ) {
                    $this->content .=
                        '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="portrait" data-type="portrait" data-orgimage="data:image/png;base64,' . $this->params['initialsAvatar']['large'] . '" src="data:image/png;base64,' . $this->params['initialsAvatar']['large'] . '" class="user-portrait img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
                } else {
                    $this->content .=
                    '<img id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->links->images('general/portrait.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:' . $this->params['maxWidth'] . 'px;max-height:' . $this->params['maxHeight'] . 'px;">';
                }
            }
        }

        if (isset($this->params['initialsAvatar']) &&
            is_array($this->params['initialsAvatar']) &&
            isset($this->params['initialsAvatar']['small'])
        ) {
            $this->params['smallIntialsAvatar'] = $this->params['initialsAvatar']['small'];
        } else {
            $this->params['smallIntialsAvatar'] = '';
        }

        $this->content .=
                '<div class="image-text">' . $fileName . '</div>
            </div>
        <div id="' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie" hidden></div>' .

        $this->inclJs();
    }

    protected function inclJs()
    {
        return
            '<script type="text/javascript">' .
            'if (!window["dataCollection"]["' . $this->params['componentId'] . '"]) {
                window["dataCollection"]["' . $this->params['componentId'] . '"] = { };
            }
            window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"] =
                $.extend(window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"], {
                    "' . $this->compSecId . '-' . $this->params['fieldId'] . '"                    : {
                        afterInit: function() {
                            var uploadUUIDs = [];
                            var deleteUUIDs = [];
                            var imageBlob, newImage, oldImage, imageName;
                            var orgImage = $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").data("orgimage");
                            var smallInitialsAvatar = "' . $this->params['smallIntialsAvatar'] . '";

                            if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("src") !== $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").data("orgimage")) {
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-recover").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload-image").attr("disabled", true);
                            }

                            function initCroppie() {
                                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"] =
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie").croppie({
                                        viewport: {
                                            width: ' . $this->params['viewportWidth'] . ',
                                            height: ' . $this->params['viewportHeight'] . ',
                                            type: "' . $this->fieldParams['fieldCroppieViewportType'] . '"
                                        },
                                        boundary: {
                                            width: ' . $this->params['boundaryWidth'] . ',
                                            height: ' . $this->params['boundaryHeight'] . '
                                        }
                                    });
                            }

                            if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val() !== "") {
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-remove").attr("hidden", false);
                            }

                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload-image").change(function () {
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("disabled", true);
                                readFile(this);
                            });

                            //Recover
                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-recover").click(function (e) {
                                $(this).attr("disabled", true);

                                e.preventDefault();

                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-search").off();
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-search").click(function (e) {
                                    e.preventDefault();

                                    generateAvatar(null, $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").val());
                                });

                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-cancel").off();
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-cancel").click(function (e) {
                                    e.preventDefault();

                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-recover").attr("disabled", false);

                                    $($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").parents(".form-group")[0]).addClass("d-none");
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").attr("disabled", true);
                                });

                                $($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").parents(".form-group")[0]).removeClass("d-none");
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").attr("disabled", false);
                            });

                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save").click(function (e) {
                                e.preventDefault();

                                if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val() &&
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val() !== ""
                                ) {
                                    oldImage = true;
                                    deleteUUIDs.push($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val());
                                } else {
                                    oldImage = false;
                                    deleteUUIDs = [];
                                }

                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save-warning").attr("hidden", true);

                                //To Canvas for show
                                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"].croppie("result", {
                                    type    : "canvas",
                                    size    : "' . $this->params['fieldCroppieSize'] . '",
                                    format  : "' . $this->params['fieldCroppieFormat'] . '",
                                    circle  : ' . $this->fieldParams['fieldCroppieViewportCircle'] . '
                                }).then(function (croppedImage) {
                                    imageBlob = croppedImage;
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-cancel").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("src", croppedImage);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("hidden", false);
                                });

                                //To Blob for upload
                                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"].croppie("result", {
                                    type    : "blob",
                                    size    : "' . $this->params['fieldCroppieSize'] . '",
                                    format  : "' . $this->params['fieldCroppieFormat'] . '",
                                    circle  : ' . $this->fieldParams['fieldCroppieViewportCircle'] . '
                                }).then(function (croppedImage) {
                                    imageBlob = croppedImage;
                                    newImage = true;
                                    processDeleteUUIDs();
                                    uploadImage();
                                });
                            });

                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-cancel").click(function () {
                                croppieReset();
                            });

                            function croppieReset() {
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("disabled", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("hidden", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload-image").attr("disabled", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save").attr("hidden", true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-cancel").attr("hidden", true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save-warning").attr("hidden", true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("hidden", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie").attr("hidden", true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-remove").attr("hidden", true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("src", orgImage);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("disabled", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("disabled", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("hidden", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("hidden", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-recover").attr("hidden", false);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-refresh").attr("hidden", true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-save").attr("hidden", true);
                                updateProfileThumbnail(true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val("");
                                oldImage = false;
                                deleteUUIDs = [];
                            }

                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-remove").click(function (e) {
                                e.preventDefault();
                                croppieReset();
                                if (smallInitialsAvatar === "") {
                                    $(".' . $this->compSecId . '-' . $this->params['fieldId'] . '-image-content .image-text").html($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").data("orgimage"));
                                } else {
                                    $(".' . $this->compSecId . '-' . $this->params['fieldId'] . '-image-content .image-text").html("Initials Avatar");
                                }
                            });

                            function readFile(input) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();

                                    reader.onload = function (e) {
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("hidden", true);
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie").attr("hidden", false);
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-save").attr("hidden", false);
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-cancel").attr("hidden", false);
                                        window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"].croppie("bind", {
                                            url: e.target.result
                                        }).then(function(){
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("disabled", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("disabled", true);
                                        });
                                    }

                                    imageName = input.files[0].name;
                                    reader.readAsDataURL(input.files[0]);
                                }
                                else {
                                    PNotify.error("Sorry - you\'re browser doesn\'t support the FileReader API");
                                }
                            }

                            if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").length > 0 ||
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").length > 0
                            ) {
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").click(function() {
                                    $(this).attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload-image").attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("disabled", false);
                                    generateAvatar("M");
                                });
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").click(function() {
                                    $(this).attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload-image").attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("disabled", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("disabled", false);
                                    generateAvatar("F");
                                });
                            }

                            function generateAvatar(gender = "M", avatarFile = null) {
                                var postData = { };
                                postData[$("#security-token").attr("name")] = $("#security-token").val();
                                if (avatarFile) {
                                    postData["avatarfile"] = avatarFile; //"F_background3_face1_head29_eye50_clothes20_mouth7.png";
                                } else if (gender) {
                                    postData["gender"] = gender;
                                }

                                $.post("' . $this->links->url("system/users/profile/generateavatar") . '", postData, function(response) {
                                    if (response.responseCode == 1) {
                                        PNotify.error(response.responseMessage);
                                        return;
                                    }

                                    $(".' . $this->compSecId . '-' . $this->params['fieldId'] . '-image-content .image-text").html(response.avatarName);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("src", "data:image/png;base64," + response.avatar);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-remove").attr("hidden", false);

                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("name", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }

                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-refresh").attr("hidden", false);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-refresh").off();
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-refresh").click(function() {
                                        generateAvatar(gender);
                                    });
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-save").attr("hidden", false);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-save").off();
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-save").click(function() {
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("hidden", true);
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("hidden", true);
                                        $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("hidden", true);
                                        uploadAvatar(response.avatarName);
                                    });
                                }, "json");
                            }

                            function uploadAvatar(avatarName) {
                                var avatar = $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").attr("src");
                                var block = avatar.split(";");
                                var contentType = block[0].split(":")[1];
                                var realData = block[1].split(",")[1];
                                var avatarBlob = b64toBlob(realData, contentType);

                                var formData = new FormData();

                                formData.append("file", avatarBlob);
                                formData.append("upload", true);
                                formData.append("directory", "' . $this->fieldParams['uploadDirectory'] . '");
                                formData.append("isPointer", "' . $this->params['isPointer'] . '");
                                formData.append("setOrphan", "' . $this->params['setOrphan'] . '");
                                formData.append("fileName", avatarName);
                                formData.append("storagetype", "' . $this->params['storageType'] . '");

                                performUpload(formData);
                            }

                            function b64toBlob(b64Data, contentType, sliceSize) {
                                contentType = contentType || "";
                                sliceSize = sliceSize || 512;

                                var byteCharacters = atob(b64Data);
                                var byteArrays = [];

                                for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                                    var slice = byteCharacters.slice(offset, offset + sliceSize);

                                    var byteNumbers = new Array(slice.length);
                                    for (var i = 0; i < slice.length; i++) {
                                        byteNumbers[i] = slice.charCodeAt(i);
                                    }

                                    var byteArray = new Uint8Array(byteNumbers);

                                    byteArrays.push(byteArray);
                                }

                                var blob = new Blob(byteArrays, {type: contentType});
                                return blob;
                            }

                            function processDeleteUUIDs() {
                                if (deleteUUIDs.length > 0) {
                                    var url = "' . $this->links->url("system/storages/remove") . '";

                                    $(deleteUUIDs).each(function(index, uuid) {
                                        var postData = { };
                                        postData[$("#security-token").attr("name")] = $("#security-token").val();
                                        postData["uuid"] = uuid;
                                        postData["storagetype"] = "' . $this->params['storageType'] . '";

                                        $.ajax({
                                            type    : "POST",
                                            url     : url,
                                            data    : postData,
                                            dataType: "json"
                                        }).done(function (response) {
                                            if (response.tokenKey && response.token) {
                                                $("#security-token").attr("name", response.tokenKey);
                                                $("#security-token").val(response.token);
                                            }

                                            if (response && response.responseCode === 0) {
                                                //
                                            }
                                        });
                                    });
                                }
                            }

                            function uploadImage() {
                                var formData = new FormData();

                                formData.append("file", imageBlob);
                                formData.append("upload", true);
                                formData.append("directory", "' . $this->fieldParams['uploadDirectory'] . '");
                                formData.append("isPointer", "' . $this->params['isPointer'] . '");
                                formData.append("setOrphan", "' . $this->params['setOrphan'] . '");
                                formData.append("fileName", imageName);
                                formData.append("storagetype", "' . $this->params['storageType'] . '");
                                formData.append($("#security-token").attr("name"), $("#security-token").val());

                                performUpload(formData);
                            }

                            function performUpload(formData) {
                                if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-image").data("type") === "portrait") {
                                    updateProfileThumbnail();
                                }

                                var url = "' . $this->links->url("system/storages/add") . '";

                                $.ajax(url, {
                                    method      : "POST",
                                    data        : formData,
                                    dataType    : "json",
                                    processData : false,
                                    contentType : false,
                                }).done(function (response) {
                                    if (response) {
                                        if (response.tokenKey && response.token) {
                                            $("#security-token").attr("name", response.tokenKey);
                                            $("#security-token").val(response.token);
                                        }
                                        if (response.responseCode == 0) {
                                            uploadUUIDs.push(response.responseData.uuid);
                                            $(".' . $this->compSecId . '-' . $this->params['fieldId'] . '-image-content .image-text").html(response.responseData.name);
                                            if (response.responseData.is_pointer == 1) {
                                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val(response.responseData.uuid_location + response.responseData.uuid);
                                            } else {
                                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val(response.responseData.uuid);
                                            }
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-remove").attr("hidden", false);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("hidden", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("hidden", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-recover").attr("hidden", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-recover").attr("disabled", false);
                                            $($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").parents(".form-group")[0]).addClass("d-none");
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename").attr("disabled", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("hidden", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '")
                                            .trigger(
                                                {
                                                    "type"      : "croppieSaved",
                                                    "uuid"      : response.responseData.uuid
                                                }
                                            );
                                        } else {
                                            PNotify.error(response.responseMessage);
                                            croppieReset();
                                        }
                                    } else {
                                        PNotify.error("Image Upload Failed!");
                                    }
                                });
                            }

                            function updateProfileThumbnail(remove = false) {
                                $("body").off("sectionWithFormDataUpdated");
                                $("body").on("sectionWithFormDataUpdated", function() {
                                    if (remove) {
                                        if (smallInitialsAvatar === "") {
                                            $("#profile-portrait").children("i").attr("hidden", false);
                                            $("#profile-portrait").children("img").attr("src", "");
                                            $("#profile-portrait").children("img").attr("hidden", true);
                                        } else {
                                            $("#profile-portrait").children("i").attr("hidden", true);
                                            $("#profile-portrait").children("img").attr("src", "data:image/png;base64," + smallInitialsAvatar);
                                            $("#profile-portrait").children("img").attr("hidden", false);
                                        }
                                    } else {
                                        $("#profile-portrait").children("i").attr("hidden", true);
                                        $("#profile-portrait").children("img").attr("src", window.dataCollection.env.rootPath + window.dataCollection.env.appRoute +
                                            "/system/storages/q/uuid/" + uploadUUIDs[0] + "/w/30");
                                        $("#profile-portrait").children("img").attr("hidden", false);
                                        window.dataCollection.env.profile.portrait =
                                            window.dataCollection.env.rootPath + window.dataCollection.env.appRoute + "/system/storages/q/uuid/" + uploadUUIDs[0] + "/w/80";
                                    }

                                    $("body").off("sectionWithFormDataUpdated");
                                });

                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").off();
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").on("croppieSaved", function(e) {
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-refresh").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-save").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-female").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-male").attr("hidden", true);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-upload").attr("hidden", true);

                                });
                            }

                            initCroppie();
                        }
                    },
                    "' . $this->compSecId . '-' . $this->params['fieldId'] . '-croppie-avatar-filename"                             : {
                        afterInit: function() {
                            //
                        }
                    },
                    "' . $this->compSecId . '-' . $this->params['fieldId'] . '-label"              : { }
                });
            </script>';
    }
}