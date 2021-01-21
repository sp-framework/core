<?php

namespace Applications\Dash\Packages\AdminLTETags\Tags\Fields\Files;

use Applications\Dash\Packages\AdminLTETags\AdminLTETags;

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
        $this->adminLTETags = new AdminLTETags();

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

        $this->content .=
            '<div class="row">
                <div class="col mb-2">' .
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
                    ).
                    $this->adminLTETags->useTag('buttons',
                        [
                            'componentId'                   => $this->params['componentId'],
                            'sectionId'                     => $this->params['sectionId'],
                            'buttonLabel'                   => false,
                            'buttonType'                    => 'button',
                            'buttons'                       =>
                                [
                                    'croppie-upload' =>
                                    [
                                        'title'                     => false,
                                        'position'                  => 'left',
                                        'icon'                      => 'upload',
                                        'size'                      => 'xs',
                                        'buttonAdditionalClass'     => 'mr-1 ml-1 btn-file',
                                        'content'                   =>
                                            '<input type="file" class="ignore" style="cursor: pointer;opacity: 0;font-size: 0;" id="' .
                                            $this->compSecId .
                                            '-croppie-upload-image" value="Choose a file" accept="image/*"/>'
                                    ],
                                    'croppie-save' =>
                                    [
                                        'title'                     => false,
                                        'position'                  => 'left',
                                        'icon'                      => 'save',
                                        'hidden'                    => true,
                                        'size'                      => 'xs',
                                        'buttonAdditionalClass'     => 'mr-1 ml-1'
                                    ],
                                    'croppie-cancel' =>
                                    [
                                        'title'                     => false,
                                        'type'                      => 'secondary',
                                        'position'                  => 'left',
                                        'icon'                      => 'times',
                                        'hidden'                    => true,
                                        'size'                      => 'xs',
                                        'buttonAdditionalClass'     => 'mr-1 ml-1'
                                    ],
                                    'croppie-remove' =>
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
                        ]
                    ) .
                    '<span id="' . $this->compSecId . '-croppie-save-warning" class="text-danger" hidden>Save/Cancel Image</span>
                </div>
            </div>
            <div class="text-center">';

        if ($this->params['imageType'] === 'logo') {
            if (isset($this->params['logoLink']) && $this->params['logoLink'] !== '') {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="logo" data-orgimage="' . $this->links->images('general/logo.png') . '" src="' . $this->params['logoLink'] .'" class="user-image img-fluid img-thumbnail">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="logo" data-orgimage="' . $this->links->images('general/logo.png') . '" src="' . $this->links->images('general/logo.png') . '" class="user-image img-fluid img-thumbnail">';
            }
        } else if ($this->params['imageType'] === 'image') {
            if (isset($this->params['imageLink']) && $this->params['imageLink'] !== '') {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="image" data-orgimage="' . $this->links->images('general/img.png') . '" src="' . $this->params['imageLink'] . '" class="user-image img-fluid img-thumbnail">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="image" data-orgimage="' . $this->links->images('general/img.png') . '" src="' . $this->links->images('general/img.png') . '" class="user-image img-fluid img-thumbnail">';
            }
        } else if ($this->params['imageType'] === 'portrait') {
            if (isset($this->params['portraitLink']) && $this->params['portraitLink'] !== '') {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->params['portraitLink'] . '" class="user-portrait img-fluid img-thumbnail">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->links->images('general/portrait.png') . '" class="user-image img-fluid img-thumbnail">';
            }
        }

        $this->content .=
            '<div id="' . $this->compSecId . '-croppie" hidden></div>
        </div>' .

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
                            var orgImage = $("#' . $this->compSecId . '-croppie-image").data("orgimage");

                            function initCroppie() {

                                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"] =
                                    $("#' . $this->compSecId . '-croppie").croppie({
                                        viewport: {
                                            width: 200,
                                            height: 200,
                                        },
                                        boundary: {
                                            width: 250,
                                            height: 250
                                        }
                                    });

                                if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val() !== "") {
                                    $("#' . $this->compSecId . '-croppie-remove").attr("hidden", false);
                                }

                                $("#' . $this->compSecId . '-croppie-upload-image").change(function () {
                                    readFile(this);
                                });

                                $("#' . $this->compSecId . '-croppie-save").click(function (e) {
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

                                    $("#' . $this->compSecId . '-save-warning").attr("hidden", true);

                                    //To Canvas for show
                                    window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"].croppie("result", {
                                        type    : "canvas",
                                        size    : "viewport",
                                        format  : "jpeg",
                                        circle  : false
                                    }).then(function (croppedImage) {
                                        imageBlob = croppedImage;
                                        $("#' . $this->compSecId . '-croppie").attr("hidden", true);
                                        $("#' . $this->compSecId . '-croppie-save").attr("hidden", true);
                                        $("#' . $this->compSecId . '-croppie-cancel").attr("hidden", true);
                                        $("#' . $this->compSecId . '-croppie-image").attr("src", croppedImage);
                                        $("#' . $this->compSecId . '-croppie-image").attr("hidden", false);
                                    });
                                    //To Blob for upload
                                    window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"].croppie("result", {
                                        type    : "blob",
                                        size    : "viewport",
                                        format  : "jpeg",
                                        circle  : false
                                    }).then(function (croppedImage) {
                                        imageBlob = croppedImage;
                                        newImage = true;
                                        processDeleteUUIDs();
                                        uploadImage();
                                    });
                                });

                                $("#' . $this->compSecId . '-croppie-cancel").click(function () {
                                    $("#' . $this->compSecId . '-croppie-save").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-cancel").attr("hidden", true);
                                    $("#' . $this->compSecId . '-save-warning").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-image").attr("hidden", false);
                                    $("#' . $this->compSecId . '-croppie").attr("hidden", true);
                                    oldImage = false;
                                    deleteUUIDs = [];
                                });

                                $("#' . $this->compSecId . '-croppie-remove").click(function () {
                                    $("#' . $this->compSecId . '-croppie-remove").attr("hidden", true);
                                    $("#' . $this->compSecId . '-save-warning").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-image").attr("src", orgImage);
                                    $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val("");
                                });
                            }

                            function readFile(input) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();

                                    reader.onload = function (e) {
                                        $("#' . $this->compSecId . '-croppie-image").attr("hidden", true);
                                        $("#' . $this->compSecId . '-croppie").attr("hidden", false);
                                        $("#' . $this->compSecId . '-croppie-save").attr("hidden", false);
                                        $("#' . $this->compSecId . '-croppie-cancel").attr("hidden", false);
                                        window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"].croppie("bind", {
                                            url: e.target.result
                                        }).then(function(){
                                            //
                                        });
                                    }

                                    imageName = input.files[0].name;
                                    reader.readAsDataURL(input.files[0]);
                                }
                                else {
                                    PNotify.error("Sorry - you\'re browser doesn\'t support the FileReader API");
                                }
                            }

                            function processDeleteUUIDs() {
                                if (deleteUUIDs.length > 0) {

                                    var url = "' . $this->links->url("storages/remove") . '";

                                    $(deleteUUIDs).each(function(index, uuid) {
                                        $.ajax({
                                            type    : "POST",
                                            url     : url,
                                            data    :
                                                {
                                                    "uuid"             : uuid,
                                                    "storagetype"      : "' . $this->params['storageType'] . '"
                                                },
                                            dataType: "json"
                                        }).done(function (data) {
                                            if (data.tokenKey && data.token) {
                                                $("#security-token").attr("name", data.tokenKey);
                                                $("#security-token").val(data.token);
                                            }

                                            if (data && data.responseCode === 0) {
                                                //
                                            }
                                        });
                                    });
                                }
                            }

                            function uploadImage() {
                                var formData = new FormData();

                                formData.append("file", imageBlob);
                                formData.append("directory", "' . strtolower($this->params['componentName']) . '");
                                formData.append("fileName", imageName);
                                formData.append("storagetype", "' . $this->params['storageType'] . '");

                                var url = "' . $this->links->url("storages/add") . '";

                                $.ajax(url, {
                                    method      : "POST",
                                    data        : formData,
                                    dataType    : "json",
                                    processData : false,
                                    contentType : false,
                                }).done(function (data) {
                                    if (data) {
                                        if (data.tokenKey && data.token) {
                                            $("#security-token").attr("name", data.tokenKey);
                                            $("#security-token").val(data.token);
                                        }
                                        if (data.responseCode == 0) {
                                            uploadUUIDs.push(data.storageData.uuid);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val(data.storageData.uuid);
                                            $("#' . $this->compSecId . '-croppie-remove").attr("hidden", false);
                                        } else {
                                            PNotify.error(data.responseMessage);
                                        }
                                    } else {
                                        PNotify.error("Image Upload Failed!");
                                    }
                                });
                            }

                            initCroppie();
                        }
                    }
                });
            </script>';
    }
}