<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Addresses;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Multiple
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $adminLTETags;

    protected $params;

    protected $content;

    protected $addressesParams = [];

    protected $compSecId;

    public function __construct($view, $tag, $links, $escaper, $params, $addressesParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->adminLTETags = new Adminltetags();

        $this->params = $params;

        $this->addressesParams = $addressesParams;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->buildSingleAddressData();

        $this->buildMultipleAddressesLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildSingleAddressData()
    {
        $this->addressesParams['searchType'] =
            isset($this->params['searchType']) ?
            $this->params['searchType'] :
            'city';

        $fieldsArr = ['addressReference','attentionTo','streetAddress','streetAddress2','streetAddress3','streetAddress4','cityId','cityName','postCodeId','postCode','stateId','stateName','countryId','countryName'];
        foreach ($fieldsArr as $field) {
            $this->addressesParams[$field] =
                isset($this->params[$field]) ?
                $this->params[$field] :
                '';
        }

        $this->addressesParams['attentionToFieldLabel'] =
            isset($this->params['attentionToFieldLabel']) ?
            $this->params['attentionToFieldLabel'] :
            'Attention To';

        $this->addressesParams['streetAddressFieldLabel'] =
            isset($this->params['streetAddressFieldLabel']) ?
            $this->params['streetAddressFieldLabel'] :
            'Street Address';

        $this->addressesParams['streetAddress2FieldLabel'] =
            isset($this->params['streetAddress2FieldLabel']) ?
            $this->params['streetAddress2FieldLabel'] :
            'Street Address 2';

        $this->addressesParams['streetAddress3FieldLabel'] =
            isset($this->params['streetAddress3FieldLabel']) ?
            $this->params['streetAddress3FieldLabel'] :
            'Street Address 3';

        $this->addressesParams['streetAddress4FieldLabel'] =
            isset($this->params['streetAddress4FieldLabel']) ?
            $this->params['streetAddress4FieldLabel'] :
            'Street Address 4';

        $this->addressesParams['cityFieldLabel'] =
            isset($this->params['cityFieldLabel']) ?
            $this->params['cityFieldLabel'] :
            'City';

        $this->addressesParams['postCodeFieldLabel'] =
            isset($this->params['postCodeFieldLabel']) ?
            $this->params['postCodeFieldLabel'] :
            'Post Code';

        $this->addressesParams['stateFieldLabel'] =
            isset($this->params['stateFieldLabel']) ?
            $this->params['stateFieldLabel'] :
            'State';

        $this->addressesParams['countryFieldLabel'] =
            isset($this->params['countryFieldLabel']) ?
            $this->params['countryFieldLabel'] :
            'Country';

        $fieldsArr = null;
        $field = null;

        $fieldsArr = ['includeAttentionTo','attentionToFieldHidden','includeStreet','streetAddressFieldHidden','streetAddress2FieldHidden','includeStreetExt','streetAddress3FieldHidden','streetAddress4FieldHidden','cityFieldHidden','postCodeFieldHidden','stateFieldHidden','countryFieldHidden','attentionToFieldDisabled','streetAddressFieldDisabled','streetAddress2FieldDisabled','streetAddress3FieldDisabled','streetAddress4FieldDisabled','cityFieldDisabled','postCodeFieldDisabled','stateFieldDisabled','countryFieldDisabled','attentionToFieldRequired','streetAddressFieldRequired','streetAddress2FieldRequired','streetAddress3FieldRequired','streetAddress4FieldRequired','cityFieldRequired','postCodeFieldRequired','stateFieldRequired','countryFieldRequired','addressReferenceFieldBazPostOnCreate','addressReferenceFieldBazPostOnUpdate','attentionToFieldBazPostOnCreate','attentionToFieldBazPostOnUpdate','streetAddressFieldBazPostOnCreate','streetAddressFieldBazPostOnUpdate','streetAddress2FieldBazPostOnCreate','streetAddress2FieldBazPostOnUpdate','streetAddress3FieldBazPostOnCreate','streetAddress3FieldBazPostOnUpdate','streetAddress4FieldBazPostOnCreate','streetAddress4FieldBazPostOnUpdate','cityFieldBazPostOnCreate','cityFieldBazPostOnUpdate','postCodeFieldBazPostOnCreate','postCodeFieldBazPostOnUpdate','stateFieldBazPostOnCreate','stateFieldBazPostOnUpdate','countryFieldBazPostOnCreate','countryFieldBazPostOnUpdate'];

        foreach ($fieldsArr as $field) {
            $this->addressesParams[$field] =
                isset($this->params[$field]) &&
                    $this->params[$field] === true ?
                true :
                false;
        }
    }

    protected function buildMultipleAddressesLayout()
    {
        $this->addressesParams['multiple'] = true;

        $singleAddressArr = [
            'component'                                   => $this->params['component'],
            'componentName'                               => $this->params['componentName'],
            'componentId'                                 => $this->params['componentId'],
            'sectionId'                                   => $this->params['sectionId'],
            'addressFieldType'                            => 'single',
        ];

        $singleAddressArr = array_merge($singleAddressArr, $this->addressesParams);

        $this->content .=
            '<div class="row vdivide" id="' . $this->compSecId . '-addresses">
                <div class="col">
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'address_ids',
                                    'fieldLabel'                            => 'Address IDs',
                                    'fieldType'                             => 'input',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Address IDs',
                                    'fieldRequired'                         => false,
                                    'fieldBazScan'                          => false,
                                    'fieldBazPostOnCreate'                  => true,
                                    'fieldBazPostOnUpdate'                  => true,
                                    'fieldHidden'                           => true,
                                    'fieldDisabled'                         => true,
                                    'fieldValue'                            => ''
                                ]
                            ) .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'delete_address_ids',
                                    'fieldLabel'                            => 'Delete Address IDs',
                                    'fieldType'                             => 'input',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Delete Address IDs',
                                    'fieldRequired'                         => false,
                                    'fieldBazScan'                          => false,
                                    'fieldBazPostOnCreate'                  => true,
                                    'fieldBazPostOnUpdate'                  => true,
                                    'fieldHidden'                           => true,
                                    'fieldDisabled'                         => true,
                                    'fieldValue'                            => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'address_id',
                                    'fieldLabel'                            => 'Address ID',
                                    'fieldType'                             => 'input',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Address ID',
                                    'fieldRequired'                         => false,
                                    'fieldBazScan'                          => false,
                                    'fieldBazPostOnCreate'                  => false,
                                    'fieldBazPostOnUpdate'                  => false,
                                    'fieldHidden'                           => true,
                                    'fieldDisabled'                         => true,
                                    'fieldValue'                            => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row" id="' . $this->compSecId . '-address_types-address">
                        <div class="col">' .
                            $this->adminLTETags->useTag('addresses', $singleAddressArr) .
                        '</div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('buttons',
                                [
                                    'component'                     => $this->params['component'],
                                    'componentName'                 => $this->params['componentName'],
                                    'componentId'                   => $this->params['componentId'],
                                    'sectionId'                     => $this->params['sectionId'],
                                    'buttonType'                    => 'button',
                                    'buttons'                       =>
                                        [
                                            'add-address'       => [
                                                'title'                   => 'Add',
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'update-address'    => [
                                                'title'                   => 'Update',
                                                'hidden'                  => true,
                                                'disabled'                => true,
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'cancel-address'    => [
                                                'title'                   => 'Cancel',
                                                'size'                    => 'xs',
                                                'type'                    => 'secondary',
                                                'icon'                    => 'times',
                                                'position'                => 'right'
                                            ]
                                        ]
                                ]
                            ) .
                        '</div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                 => $this->params['component'],
                                    'componentName'             => $this->params['componentName'],
                                    'componentId'               => $this->params['componentId'],
                                    'sectionId'                 => $this->params['sectionId'],
                                    'fieldId'                   => 'addresses',
                                    'fieldLabel'                => 'Addresses',
                                    'fieldType'                 => 'html',
                                    'fieldHelp'                 => true,
                                    'fieldHelpTooltipContent'   => 'List of addresses',
                                    'fieldAdditionalClass'      => 'mb-0',
                                    'fieldRequired'             => false,
                                    'fieldBazScan'              => false,
                                    'fieldBazJstreeSearch'      => true,
                                    'fieldBazPostOnCreate'      => false,
                                    'fieldBazPostOnUpdate'      => false
                                ]
                            ) .
                            '<ul class="list-group list-group-sortable" id="' . $this->compSecId . '-sortable-addresses-list" style="max-height: 450px;overflow: scroll;border-radius: 0 !important;">';
                                if (isset($this->params['addresses']) && is_array($this->params['addresses']) && count($this->params['addresses']) > 0) {
                                    $this->content .=
                                        '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-addresses-list-nodata" hidden>
                                            <div class="row">
                                                <div class="col text-uppercase">
                                                    <i class="fa fa-fw fa-exclamation"></i> Add New Address
                                                </div>
                                            </div>
                                        </div>';

                                    foreach ($this->params['addresses'] as $key => $address) {
                                        $this->content .=
                                            '<li class="list-group-item list-group-item-secondary" area-disabled="false" style="cursor: pointer" data-new="0" data-address-id="' . $address['id'] . '">
                                                <div class="row">
                                                    <div class="col">
                                                        <button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 addressDeleteButton">
                                                            <i class="fa fas fa-fw text-xs fa-trash"></i>
                                                        </button>
                                                        <button data-sort-id="" type="button" class="btn btn-xs btn-primary float-right ml-1 addressEditButton">
                                                            <i class="fa fas fa-fw text-xs fa-edit"></i>
                                                        </button>
                                                        <button data-sort-id="" type="button" class="btn btn-xs btn-info float-right ml-1 addressCopyButton">
                                                            <i class="fa fas fa-fw text-xs fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col list-group-item-data">
                                                        <dl class="row mb-0">
                                                            <dt class="text-uppercase mb-0 col-sm-4">Address Reference</dt>
                                                            <dd class="mb-0 col-sm-8 cla-addressReference">' . $address['address_reference'] . '</dd>';
                                                            if (isset($address['attention_to']) && $address['attention_to'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Attention To</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-attentionTo">' . $address['attention_to'] . '</dd>';
                                                            }
                                                            $this->content .=
                                                                '<dt class="text-uppercase mb-0 col-sm-4">Street Address</dt>
                                                                <dd class="mb-0 col-sm-8 cla-street">' . $address['street_address'] . '</dd>';
                                                            if (isset($address['street_address_2']) && $address['street_address_2'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Street Address 2</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-street2">' . $address['street_address_2'] . '</dd>';
                                                            }
                                                            if (isset($address['street_address_3']) && $address['street_address_3'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Street Address 3</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-street3">' . $address['street_address_3'] . '</dd>';
                                                            }
                                                            if (isset($address['street_address_4']) && $address['street_address_4'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Street Address 4</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-street4">' . $address['street_address_4'] . '</dd>';
                                                            }
                                                            $this->content .=
                                                                '<dt class="text-uppercase mb-0 col-sm-4">City</dt>
                                                                <dd class="mb-0 col-sm-8 cla-city" data-id="' . $address['city_id'] . '">' . $address['city_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Post Code</dt>
                                                                <dd class="mb-0 col-sm-8 cla-post_code" data-id="' . $address['post_code_id'] . '">' . $address['post_code'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">State</dt>
                                                                <dd class="mb-0 col-sm-8 cla-state" data-id="' . $address['state_id'] . '">' . $address['state_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Country</dt>
                                                                <dd class="mb-0 col-sm-8 cla-country" data-id="' . $address['country_id'] . '">' . $address['country_name'] . '</dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </li>';
                                    }
                                } else {
                                    $this->content .=
                                        '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-addresses-list-nodata">
                                            <div class="row">
                                                <div class="col text-uppercase">
                                                    <i class="fa fa-fw fa-exclamation"></i> Add New Address
                                                </div>
                                            </div>
                                        </div>';
                                }

                            $this->content .=
                            '</ul>
                        </div>
                    </div>
                </div>
            </div>' .
            $this->inclAddressesJs();
    }

    protected function inclAddressesJs()
    {
        $inclJs =
            '<script type="text/javascript">
            var dataCollectionComponent, dataCollectionSection, dataCollectionSectionForm;

            if (!window["dataCollection"]["' . $this->params['componentId'] . '"]) {
                dataCollectionComponent =
                    window["dataCollection"]["' . $this->params['componentId'] . '"] = { };
            } else {
                dataCollectionComponent =
                    window["dataCollection"]["' . $this->params['componentId'] . '"];
            }
            if (!dataCollectionComponent["' . $this->compSecId . '"]) {
                dataCollectionSection =
                    dataCollectionComponent["' . $this->compSecId . '"] = { };
            } else {
                dataCollectionSection =
                    dataCollectionComponent["' . $this->compSecId . '"];
            }
            if (!dataCollectionSection["' . $this->compSecId . '-form"]) {
                dataCollectionSectionForm =
                    dataCollectionSection["' . $this->compSecId . '-form"] = { };
            } else {
                dataCollectionSectionForm =
                    dataCollectionSection["' . $this->compSecId . '-form"];
            }

            dataCollectionSection =
                $.extend(dataCollectionSection, {
                    "' . $this->compSecId . '-address_reference"                   : {
                        afterInit : function () {
                            dataCollectionSection["data"]["address_ids"] = { }
                            dataCollectionSection["data"]["delete_address_ids"] = [];

                            function initMainButtons() {
                                $("#' . $this->compSecId . '-cancel-address").off();
                                $("#' . $this->compSecId . '-cancel-address").click(function(e) {
                                    e.preventDefault();
                                    $(".addressEditButton, .addressDeleteButton").attr("disabled", false);
                                    toggleAddressFields(true);
                                    $("#' . $this->compSecId . '-addresses").trigger("addressCancel");
                                });
                                $("#' . $this->compSecId . '-add-address, #' . $this->compSecId . '-update-address").off();
                                $("#' . $this->compSecId . '-add-address, #' . $this->compSecId . '-update-address").attr("disabled", false);
                                $("#' . $this->compSecId . '-add-address, #' . $this->compSecId . '-update-address").click(function(e) {
                                    e.preventDefault();
                                    $(".addressEditButton, .addressDeleteButton").attr("disabled", false);

                                    if ($(this)[0].id === "' . $this->compSecId . '-update-address") {
                                        extractData(true);
                                        $("#' . $this->compSecId . '-addresses").trigger("addressUpdate");
                                    } else {
                                        extractData();
                                        $("#' . $this->compSecId . '-addresses").trigger("addressAdd");
                                    }
                                });
                            }

                            function toggleAddressFields(status, update = false) {
                                if (status === true) {
                                    $("#' . $this->compSecId . '-address_reference").val("");
                                    $("#' . $this->compSecId . '-address_id").val("");
                                    $("#' . $this->compSecId . '-attention_to").val("");
                                    $("#' . $this->compSecId . '-street_address").val("");
                                    $("#' . $this->compSecId . '-street_address_2").val("");
                                    $("#' . $this->compSecId . '-street_address_3").val("");
                                    $("#' . $this->compSecId . '-street_address_4").val("");
                                    $("#' . $this->compSecId . '-city_id").val("");
                                    $("#' . $this->compSecId . '-city_name").val("");
                                    $("#' . $this->compSecId . '-post_code_id").val("");
                                    $("#' . $this->compSecId . '-post_code").val("");
                                    $("#' . $this->compSecId . '-state_id").val("");
                                    $("#' . $this->compSecId . '-state_name").val("");
                                    $("#' . $this->compSecId . '-country_id").val("");
                                    $("#' . $this->compSecId . '-country_name").val("");
                                }

                                if (update === true) {
                                    $("#' . $this->compSecId . '-add-address").attr("hidden", true);
                                    $("#' . $this->compSecId . '-update-address").attr("hidden", false);
                                    $("#' . $this->compSecId . '-update-address").attr("disabled", false);
                                } else {
                                    $("#' . $this->compSecId . '-update-address").attr("hidden", true);
                                    $("#' . $this->compSecId . '-add-address").attr("hidden", false);
                                    $("#' . $this->compSecId . '-add-address").attr("disabled", false);
                                }

                                $("#' . $this->compSecId . '-address_reference").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-attention_to").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_address").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_address_2").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_address_3").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_address_4").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-city_name").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-post_code").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-state_name").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-country_name").removeClass("is-invalid");
                            }

                            function extractData(update = false) {
                                if ($("#' . $this->compSecId . '-address_reference").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-address_reference").val() === "") {
                                    $("#' . $this->compSecId . '-address_reference").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-address_reference").focus(function() {
                                        $("#' . $this->compSecId . '-address_reference").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-attention_to").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-attention_to").val() === "") {
                                    $("#' . $this->compSecId . '-attention_to").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-attention_to").focus(function() {
                                        $("#' . $this->compSecId . '-attention_to").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_address").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_address").val() === "") {
                                    $("#' . $this->compSecId . '-street_address").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_address").focus(function() {
                                        $("#' . $this->compSecId . '-street_address").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_address_2").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_address_2").val() === "") {
                                    $("#' . $this->compSecId . '-street_address_2").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_address_2").focus(function() {
                                        $("#' . $this->compSecId . '-street_address_2").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_address_3").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_address_3").val() === "") {
                                    $("#' . $this->compSecId . '-street_address_3").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_address_3").focus(function() {
                                        $("#' . $this->compSecId . '-street_address_3").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_address_4").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_address_4").val() === "") {
                                    $("#' . $this->compSecId . '-street_address_4").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_address_4").focus(function() {
                                        $("#' . $this->compSecId . '-street_address_4").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-city_name").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-city_name").val() === "") {
                                    $("#' . $this->compSecId . '-city_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-city_name").focus(function() {
                                        $("#' . $this->compSecId . '-city_name").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-post_code").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-post_code").val() === "") {
                                    $("#' . $this->compSecId . '-post_code").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-post_code").focus(function() {
                                        $("#' . $this->compSecId . '-post_code").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-state_name").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-state_name").val() === "") {
                                    $("#' . $this->compSecId . '-state_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-state_name").focus(function() {
                                        $("#' . $this->compSecId . '-state_name").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-country_name").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-country_name").val() === "") {
                                    $("#' . $this->compSecId . '-country_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-country_name").focus(function() {
                                        $("#' . $this->compSecId . '-country_name").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                var data = { };
                                var addressId, addressNew;

                                data["address_id"] = $("#' . $this->compSecId . '-address_id").val();
                                var html =
                                    \'<dl class="row mb-0">\';
                                if ($("#' . $this->compSecId . '-address_reference").length > 0) {
                                    data["address_reference"] = $("#' . $this->compSecId . '-address_reference").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Address Reference</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-addressReference">\' + data["address_reference"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-attention_to").length > 0) {
                                    data["attention_to"] = $("#' . $this->compSecId . '-attention_to").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Attention To</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-attentionTo">\' + data["attention_to"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_address").length > 0) {
                                    data["street_address"] = $("#' . $this->compSecId . '-street_address").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Address</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street">\' + data["street_address"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_address_2").length > 0) {
                                    data["street_address_2"] = $("#' . $this->compSecId . '-street_address_2").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Address 2</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street2">\' + data["street_address_2"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_address_3").length > 0) {
                                    data["street_address_3"] = $("#' . $this->compSecId . '-street_address_3").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Address 3</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street3">\' + data["street_address_3"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_address_4").length > 0) {
                                    data["street_address_4"] = $("#' . $this->compSecId . '-street_address_4").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Address 4</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street4">\' + data["street_address_4"] + \'</dd>\';
                                }
                                data["city_id"] = $("#' . $this->compSecId . '-city_id").val();
                                if ($("#' . $this->compSecId . '-city_name").length > 0) {
                                    data["city_name"] = $("#' . $this->compSecId . '-city_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">City</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-city" data-id="\' + data["city_id"] + \'">\' + data["city_name"] + \'</dd>\';
                                }
                                data["post_code_id"] = $("#' . $this->compSecId . '-post_code_id").val();
                                if ($("#' . $this->compSecId . '-post_code").length > 0) {
                                    data["post_code"] = $("#' . $this->compSecId . '-post_code").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Post Code</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-post_code" data-id="\' + data["post_code_id"] + \'">\' + data["post_code"] + \'</dd>\';
                                }
                                data["state_id"] = $("#' . $this->compSecId . '-state_id").val();
                                if ($("#' . $this->compSecId . '-state_name").length > 0) {
                                    data["state_name"] = $("#' . $this->compSecId . '-state_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">State</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-state" data-id="\' + data["state_id"] + \'">\' + data["state_name"] + \'</dd>\';
                                }
                                data["country_id"] = $("#' . $this->compSecId . '-country_id").val();
                                if ($("#' . $this->compSecId . '-country_name").length > 0) {
                                    data["country_name"] = $("#' . $this->compSecId . '-country_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Country</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-country" data-id="\' + data["country_id"] + \'">\' + data["country_name"] + \'</dd>\';
                                }

                                html +=
                                    \'</dl>\';

                                if ($("#' . $this->compSecId . '-sortable-addresses-list").length > 0) {
                                    var addressesLi = $("#' . $this->compSecId . '-sortable-addresses-list li");

                                    if (data["address_id"] === "") {
                                        addressId = Date.now();
                                        addressNew = "1";
                                    } else {
                                        addressId = data["address_id"];
                                        addressNew = "0";
                                    }

                                    var list =
                                        \'<li class="list-group-item list-group-item-secondary\' +
                                            \'" area-disabled="false" style="cursor: pointer" \' +
                                            \'" data-new="\' + addressNew + \'" data-address-id="\' + addressId + \'">\' +
                                            \'<div class="row">\' +
                                                \'<div class="col">\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 addressDeleteButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-trash"></i>\' +
                                                    \'</button>\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-primary float-right ml-1 addressEditButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-edit"></i>\' +
                                                    \'</button>\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-info float-right ml-1 addressCopyButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-copy"></i>\' +
                                                    \'</button>\' +
                                                \'</div>\' +
                                            \'</div>\' +
                                            \'<div class="row">\' +
                                                \'<div class="col list-group-item-data">\' +
                                                    html +
                                                \'</div>\' +
                                            \'</div>\' +
                                        \'</li>\';

                                    if (update === false && addressesLi.length > 0) {
                                        var exists = false;

                                        $(addressesLi).each(function(index, li) {
                                            if ($(li).find(".cla-addressReference").text() === data["address_reference"]) {
                                                PNotify.error({"title" : "Address with same reference already added!"});
                                                exists = true;
                                                return;
                                            }
                                        });

                                        if (exists === false) {
                                            $("#' . $this->compSecId . '-sortable-addresses-list").append(list);
                                        } else {
                                            return;
                                        }
                                    } else if (update === true) {
                                        $("#' . $this->compSecId . '-sortable-addresses-list [data-address-id=" + addressId + "]")
                                            .find(".list-group-item-data").empty().append(html);
                                    } else {
                                        $("#' . $this->compSecId . '-sortable-addresses-list").append(list);
                                        $("#' . $this->compSecId . '-addresses-list-nodata").attr("hidden", true);
                                    }
                                }

                                collectData();
                                registerAddressButtons();
                                toggleAddressFields(true);
                            }

                            function collectData() {
                                if ($("#' . $this->compSecId . '-sortable-addresses-list li").length > 0) {
                                    $("#' . $this->compSecId . '-sortable-addresses-list li").each(function(index, id) {
                                        var data = { };
                                        var addressId;

                                        addressId = $(this).data("address-id");
                                        data["new"] = $(this).data("new");

                                        $(id).find("dd").each(function(index,dd) {
                                            if ($(dd).is(".cla-attentionTo")) {
                                                data["attention_to"] = $(dd).html();
                                            } else if ($(dd).is(".cla-addressReference")) {
                                                data["address_reference"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street")) {
                                                data["street_address"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street2")) {
                                                data["street_address_2"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street3")) {
                                                data["street_address_3"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street4")) {
                                                data["street_address_4"] = $(dd).html();
                                            } else if ($(dd).is(".cla-city")) {
                                                data["city_id"] = $(dd).data("id");
                                                data["city_name"] = $(dd).html();
                                            } else if ($(dd).is(".cla-post_code")) {
                                                data["post_code_id"] = $(dd).data("id");
                                                data["post_code"] = $(dd).html();
                                            } else if ($(dd).is(".cla-state")) {
                                                data["state_id"] = $(dd).data("id");
                                                data["state_name"] = $(dd).html();
                                            } else if ($(dd).is(".cla-country")) {
                                                data["country_id"] = $(dd).data("id");
                                                data["country_name"] = $(dd).html();
                                            }
                                        });

                                        dataCollectionSection["data"]["address_ids"][addressId] = data;
                                    });
                                }
                            }

                            function registerAddressButtons() {
                                $(".addressEditButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        $(this).attr("disabled", true);
                                        $(this).siblings(".addressDeleteButton").attr("disabled", true);

                                        $($(this).parents("li").children(".row")[1]).find("dd").each(function(index,dd) {
                                            if ($(dd).is(".cla-addressReference")) {
                                                $("#' . $this->compSecId . '-address_reference").val($(dd).html());
                                            } else if ($(dd).is(".cla-attentionTo")) {
                                                $("#' . $this->compSecId . '-attention_to").val($(dd).html());
                                            } else if ($(dd).is(".cla-street")) {
                                                $("#' . $this->compSecId . '-street_address").val($(dd).html());
                                            } else if ($(dd).is(".cla-street2")) {
                                                $("#' . $this->compSecId . '-street_address_2").val($(dd).html());
                                            } else if ($(dd).is(".cla-street3")) {
                                                $("#' . $this->compSecId . '-street_address_3").val($(dd).html());
                                            } else if ($(dd).is(".cla-street4")) {
                                                $("#' . $this->compSecId . '-street_address_4").val($(dd).html());
                                            } else if ($(dd).is(".cla-city")) {
                                                $("#' . $this->compSecId . '-city_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-city_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-post_code")) {
                                                $("#' . $this->compSecId . '-post_code_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-post_code").val($(dd).html());
                                            } else if ($(dd).is(".cla-state")) {
                                                $("#' . $this->compSecId . '-state_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-state_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-country")) {
                                                $("#' . $this->compSecId . '-country_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-country_name").val($(dd).html());
                                            }

                                            $("#' . $this->compSecId . '-address_id").val($(dd).parents("li").data("address-id"));
                                        });
                                        toggleAddressFields(false, true);
                                    });
                                });

                                $(".addressCopyButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        $("#' . $this->compSecId . '-address_types").val(0).trigger("change");
                                        $($(this).parents("li").children(".row")[1]).find("dd").each(function(index,dd) {
                                            if ($(dd).is(".cla-addressReference")) {
                                                $("#' . $this->compSecId . '-address_reference").val($(dd).html());
                                            } else if ($(dd).is(".cla-attentionTo")) {
                                                $("#' . $this->compSecId . '-attention_to").val($(dd).html());
                                            } else if ($(dd).is(".cla-street")) {
                                                $("#' . $this->compSecId . '-street_address").val($(dd).html());
                                            } else if ($(dd).is(".cla-street2")) {
                                                $("#' . $this->compSecId . '-street_address_2").val($(dd).html());
                                            } else if ($(dd).is(".cla-street3")) {
                                                $("#' . $this->compSecId . '-street_address_3").val($(dd).html());
                                            } else if ($(dd).is(".cla-street4")) {
                                                $("#' . $this->compSecId . '-street_address_4").val($(dd).html());
                                            } else if ($(dd).is(".cla-city")) {
                                                $("#' . $this->compSecId . '-city_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-city_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-post_code")) {
                                                $("#' . $this->compSecId . '-post_code_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-post_code").val($(dd).html());
                                            } else if ($(dd).is(".cla-state")) {
                                                $("#' . $this->compSecId . '-state_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-state_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-country")) {
                                                $("#' . $this->compSecId . '-country_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-country_name").val($(dd).html());
                                            }
                                        });
                                        toggleAddressFields(false, false);
                                    });
                                });

                                $(".addressDeleteButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        var addressesCount = $(this).parents("ul").children("li").length;

                                        dataCollectionSection["data"]["delete_address_ids"].push($(this).parents("li").data("address-id"));

                                        if (dataCollectionSection["data"]["address_ids"][$(this).parents("li").data("address-id")]) {
                                            delete(dataCollectionSection["data"]["address_ids"][$(this).parents("li").data("address-id")]);
                                        }

                                        $(this).parents("li").remove();

                                        addressesCount = addressesCount - 1;

                                        if (addressesCount === 0) {
                                            $("#' . $this->compSecId . '-addresses-list-nodata").attr("hidden", false);
                                        }
                                        collectData();
                                    });
                                });
                            }

                            initMainButtons();
                            collectData();
                            registerAddressButtons();
                        }
                    }
                });
            </script>';

        return $inclJs;
    }
}
