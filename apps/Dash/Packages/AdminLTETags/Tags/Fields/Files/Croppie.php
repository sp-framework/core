<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Fields\Files;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

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

        $croppieButtons = [];

        if (isset($this->params['upload']) && $this->params['upload'] === true) {
            $croppieButtons =
                array_merge(
                    $croppieButtons,
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
                        ]
                    ]
                );
        }

        if (isset($this->params['avatar']) && $this->params['avatar'] === true) {
            $croppieButtons =
                array_merge(
                    $croppieButtons,
                    [
                        'croppie-avatar-male' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'male',
                            'hidden'                    => false,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        'croppie-avatar-female' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'female',
                            'hidden'                    => false,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        'croppie-avatar-refresh' =>
                        [
                            'title'                     => false,
                            'position'                  => 'left',
                            'icon'                      => 'redo',
                            'hidden'                    => true,
                            'size'                      => 'xs',
                            'buttonAdditionalClass'     => 'mr-1 ml-1'
                        ],
                        'croppie-avatar-save' =>
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

        $croppieButtons =
            array_merge(
                $croppieButtons,
                [
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
            );

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
                    '<span id="' . $this->compSecId . '-croppie-save-warning" class="text-danger" hidden>Save/Cancel Image</span>
                </div>
            </div>
            <div class="text-center">';

        if ($this->params['imageType'] === 'logo') {
            if (isset($this->params['logoLink']) &&
                ($this->params['logoLink'] !== '' && $this->params['logoLink'] !== '#')
        ) {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="logo" data-type="logo" data-orgimage="' . $this->links->images('general/logo.png') . '" src="' . $this->params['logoLink'] .'" class="user-image img-fluid img-thumbnail" style="max-width:200px;max-height:200px;">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="logo" data-type="logo" data-orgimage="' . $this->links->images('general/logo.png') . '" src="' . $this->links->images('general/logo.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:200px;max-height:200px;">';
            }
        } else if ($this->params['imageType'] === 'image') {
            if (isset($this->params['imageLink']) &&
                ($this->params['imageLink'] !== '' && $this->params['imageLink'] !== '#')
        ) {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="image" data-type="image" data-orgimage="' . $this->links->images('general/img.png') . '" src="' . $this->params['imageLink'] . '" class="user-image img-fluid img-thumbnail" style="max-width:200px;max-height:200px;">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="image" data-type="image" data-orgimage="' . $this->links->images('general/img.png') . '" src="' . $this->links->images('general/img.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:200px;max-height:200px;">';
            }
        } else if ($this->params['imageType'] === 'portrait') {
            if (isset($this->params['portraitLink']) &&
                ($this->params['portraitLink'] !== '' && $this->params['portraitLink'] !== '#')
        ) {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->params['portraitLink'] . '" class="user-portrait img-fluid img-thumbnail" style="max-width:200px;max-height:200px;">';
            } else {
                $this->content .=
                    '<img id="' . $this->compSecId . '-croppie-image" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->links->images('general/portrait.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:200px;max-height:200px;">';
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

                            if ($("#' . $this->compSecId . '-croppie-image").attr("src") !== $("#' . $this->compSecId . '-croppie-image").data("orgimage")) {
                                    $("#' . $this->compSecId . '-croppie-upload").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-male").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-female").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-upload-image").attr("disabled", true);
                            }

                            function initCroppie() {
                                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '"]["' . $this->compSecId . '-' . $this->params['fieldId'] . '"] =
                                    $("#' . $this->compSecId . '-croppie").croppie({
                                        viewport: {
                                            width: 200,
                                            height: 200,
                                            type: "' . $this->fieldParams['fieldCroppieViewportType'] . '"
                                        },
                                        boundary: {
                                            width: 250,
                                            height: 250
                                        }
                                    });
                            }

                            if ($("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val() !== "") {
                                $("#' . $this->compSecId . '-croppie-remove").attr("hidden", false);
                            }

                            $("#' . $this->compSecId . '-croppie-upload-image").change(function () {
                                $("#' . $this->compSecId . '-croppie-upload").attr("disabled", true);
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
                                    size    : "' . $this->params['fieldCroppieSize'] . '",
                                    format  : "' . $this->params['fieldCroppieFormat'] . '",
                                    circle  : ' . $this->fieldParams['fieldCroppieViewportCircle'] . '
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

                            $("#' . $this->compSecId . '-croppie-cancel").click(function () {
                                croppieReset();
                            });

                            function croppieReset() {
                                $("#' . $this->compSecId . '-croppie-upload").attr("disabled", false);
                                $("#' . $this->compSecId . '-croppie-upload").attr("hidden", false);
                                $("#' . $this->compSecId . '-croppie-upload-image").attr("disabled", false);
                                $("#' . $this->compSecId . '-croppie-save").attr("hidden", true);
                                $("#' . $this->compSecId . '-croppie-cancel").attr("hidden", true);
                                $("#' . $this->compSecId . '-save-warning").attr("hidden", true);
                                $("#' . $this->compSecId . '-croppie-image").attr("hidden", false);
                                $("#' . $this->compSecId . '-croppie").attr("hidden", true);
                                $("#' . $this->compSecId . '-croppie-remove").attr("hidden", true);
                                $("#' . $this->compSecId . '-croppie-image").attr("src", orgImage);
                                $("#' . $this->compSecId . '-croppie-avatar-male").attr("disabled", false);
                                $("#' . $this->compSecId . '-croppie-avatar-female").attr("disabled", false);
                                $("#' . $this->compSecId . '-croppie-avatar-male").attr("hidden", false);
                                $("#' . $this->compSecId . '-croppie-avatar-female").attr("hidden", false);
                                $("#' . $this->compSecId . '-croppie-avatar-refresh").attr("hidden", true);
                                $("#' . $this->compSecId . '-croppie-avatar-save").attr("hidden", true);
                                updateProfileThumbnail(true);
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val("");
                                oldImage = false;
                                deleteUUIDs = [];
                            }

                            $("#' . $this->compSecId . '-croppie-remove").click(function (e) {
                                e.preventDefault();
                                croppieReset();
                            });

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
                                            $("#' . $this->compSecId . '-croppie-avatar-female").attr("disabled", true);
                                            $("#' . $this->compSecId . '-croppie-avatar-male").attr("disabled", true);
                                        });
                                    }

                                    imageName = input.files[0].name;
                                    reader.readAsDataURL(input.files[0]);
                                }
                                else {
                                    PNotify.error("Sorry - you\'re browser doesn\'t support the FileReader API");
                                }
                            }

                            if ($("#' . $this->compSecId . '-croppie-avatar-male").length > 0 ||
                                $("#' . $this->compSecId . '-croppie-avatar-female").length > 0
                            ) {
                                $("#' . $this->compSecId . '-croppie-avatar-male").click(function() {
                                    $(this).attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-upload").attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-upload-image").attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-female").attr("disabled", false);
                                    generateAvatar("M");
                                });
                                $("#' . $this->compSecId . '-croppie-avatar-female").click(function() {
                                    $(this).attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-upload").attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-upload-image").attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-female").attr("disabled", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-male").attr("disabled", false);
                                    generateAvatar("F");
                                });
                            }

                            function generateAvatar(gender) {
                                var postData = { };
                                postData[$("#security-token").attr("name")] = $("#security-token").val();
                                postData["gender"] = gender;
                                // postData["avatarfile"] = "F_background3_face1_head29_eye50_clothes20_mouth7.png";

                                $.post("' . $this->links->url("system/users/profile/generateavatar") . '", postData, function(response) {
                                    $("#' . $this->compSecId . '-croppie-image").attr("src", "data:image/png;base64," + response.avatar);
                                    $("#' . $this->compSecId . '-croppie-remove").attr("hidden", false);
                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("name", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }

                                    $("#' . $this->compSecId . '-croppie-avatar-refresh").attr("hidden", false);
                                    $("#' . $this->compSecId . '-croppie-avatar-refresh").off();
                                    $("#' . $this->compSecId . '-croppie-avatar-refresh").click(function() {
                                        generateAvatar(gender);
                                    });
                                    $("#' . $this->compSecId . '-croppie-avatar-save").attr("hidden", false);
                                    $("#' . $this->compSecId . '-croppie-avatar-save").off();
                                    $("#' . $this->compSecId . '-croppie-avatar-save").click(function() {
                                        $("#' . $this->compSecId . '-croppie-upload").attr("hidden", true);
                                        $("#' . $this->compSecId . '-croppie-avatar-male").attr("hidden", true);
                                        $("#' . $this->compSecId . '-croppie-avatar-female").attr("hidden", true);
                                        uploadAvatar(response.avatarName);
                                    });

                                }, "json");
                            }

                            function uploadAvatar(avatarName) {
                                var avatar = $("#' . $this->compSecId . '-croppie-image").attr("src");
                                var block = avatar.split(";");
                                var contentType = block[0].split(":")[1];
                                var realData = block[1].split(",")[1];
                                var avatarBlob = b64toBlob(realData, contentType);

                                var formData = new FormData();

                                formData.append("file", avatarBlob);
                                formData.append("directory", "' . strtolower($this->params['componentName']) . '");
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
                                        $.ajax({
                                            type    : "POST",
                                            url     : url,
                                            data    :
                                                {
                                                    "uuid"             : uuid,
                                                    "storagetype"      : "' . $this->params['storageType'] . '"
                                                },
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
                                formData.append("directory", "' . strtolower($this->params['componentName']) . '");
                                formData.append("fileName", imageName);
                                formData.append("storagetype", "' . $this->params['storageType'] . '");

                                performUpload(formData);
                            }

                            function performUpload(formData) {
                                if ($("#' . $this->compSecId . '-croppie-image").data("type") === "portrait") {
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
                                            uploadUUIDs.push(response.storageData.uuid);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").val(response.storageData.uuid);
                                            $("#' . $this->compSecId . '-croppie-remove").attr("hidden", false);
                                            $("#' . $this->compSecId . '-croppie-avatar-male").attr("hidden", true);
                                            $("#' . $this->compSecId . '-croppie-avatar-female").attr("hidden", true);
                                            $("#' . $this->compSecId . '-croppie-upload").attr("hidden", true);
                                            $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '")
                                            .trigger(
                                                {
                                                    "type"      : "croppieSaved",
                                                    "uuid"      : response.storageData.uuid
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
                                        $("#profile-portrait").children("i").attr("hidden", false);
                                        $("#profile-portrait").children("img").attr("src", "");
                                        $("#profile-portrait").children("img").attr("hidden", true);
                                    } else {
                                        $("#profile-portrait").children("i").attr("hidden", true);
                                        $("#profile-portrait").children("img").attr("src", window.dataCollection.env.rootPath + window.dataCollection.env.appRoute +
                                            "/system/storages/q/uuid/" + uploadUUIDs[0] + "/w/30");
                                        $("#profile-portrait").children("img").attr("hidden", false);
                                    }
                                });

                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").off();
                                $("#' . $this->compSecId . '-' . $this->params['fieldId'] . '").on("croppieSaved", function(e) {
                                    $("#' . $this->compSecId . '-croppie-avatar-refresh").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-save").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-female").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-avatar-male").attr("hidden", true);
                                    $("#' . $this->compSecId . '-croppie-upload").attr("hidden", true);

                                });
                            }

                            initCroppie();
                        }
                    },
                    "' . $this->compSecId . '-' . $this->params['fieldId'] . '-label"               : { }
                });
            </script>';
    }
}