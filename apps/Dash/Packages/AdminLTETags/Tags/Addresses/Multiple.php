<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Addresses;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Json;

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

        $this->adminLTETags = new AdminLTETags();

        $this->params = $params;

        $this->addressesParams = $addressesParams;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->buildMultipleAddressesLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildMultipleAddressesLayout()
    {
        $this->content .=
            '<div class="row vdivide">
                <div class="col">
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'address_types',
                                    'fieldLabel'                            => 'Address Types',
                                    'fieldType'                             => 'select2',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Select Address Type',
                                    'fieldRequired'                         => true,
                                    'fieldBazScan'                          => true,
                                    'fieldBazJstreeSearch'                  => true,
                                    'fieldBazPostOnCreate'                  => false,
                                    'fieldBazPostOnUpdate'                  => false,
                                    'fieldDataSelect2Options'               => $this->addressesParams['addressTypes'],
                                    'fieldDataSelect2OptionsKey'            => 'id',
                                    'fieldDataSelect2OptionsValue'          => 'name',
                                    'fieldDataSelect2OptionsArray'          => true,
                                    'fieldDataSelect2OptionsSelected'       => ''
                                ]
                            ) . '
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'address_ids',
                                    'fieldLabel'                     => 'Address IDs',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Address IDs',
                                    'fieldRequired'                  => false,
                                    'fieldBazScan'                   => false,
                                    'fieldBazPostOnCreate'           => true,
                                    'fieldBazPostOnUpdate'           => true,
                                    'fieldHidden'                    => true,
                                    'fieldDisabled'                  => true,
                                    'fieldValue'                     => ''
                                ]
                            ) .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'delete_address_ids',
                                    'fieldLabel'                     => 'Delete Address IDs',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Delete Address IDs',
                                    'fieldRequired'                  => false,
                                    'fieldBazScan'                   => false,
                                    'fieldBazPostOnCreate'           => true,
                                    'fieldBazPostOnUpdate'           => true,
                                    'fieldHidden'                    => true,
                                    'fieldDisabled'                  => true,
                                    'fieldValue'                     => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'address_id',
                                    'fieldLabel'                     => 'Address ID',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Address ID',
                                    'fieldRequired'                  => false,
                                    'fieldBazScan'                   => false,
                                    'fieldBazPostOnCreate'           => false,
                                    'fieldBazPostOnUpdate'           => false,
                                    'fieldHidden'                    => true,
                                    'fieldDisabled'                  => true,
                                    'fieldValue'                     => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row" id="' . $this->compSecId . '-address_types-address">
                        <div class="col">' .
                            $this->adminLTETags->useTag('addresses',
                                [
                                    'component'                                   => $this->params['component'],
                                    'componentName'                               => $this->params['componentName'],
                                    'componentId'                                 => $this->params['componentId'],
                                    'sectionId'                                   => $this->params['sectionId'],
                                    'addressFieldType'                            => 'single',
                                    'includeStreet'                               => $this->params['includeStreet'],
                                    'searchType'                                  => $this->params['searchType'],
                                    'streetAddress'                               => $this->params['streetAddress'],
                                    'streetAddressFieldDisabled'                  => $this->params['streetAddressFieldDisabled'],
                                    'streetAddressFieldBazPostOnCreate'           => $this->params['streetAddressFieldBazPostOnCreate'],
                                    'streetAddressFieldBazPostOnUpdate'           => $this->params['streetAddressFieldBazPostOnUpdate'],
                                    'streetAddress2'                              => $this->params['streetAddress2'],
                                    'streetAddress2FieldDisabled'                 => $this->params['streetAddress2FieldDisabled'],
                                    'streetAddress2FieldBazPostOnCreate'          => $this->params['streetAddress2FieldBazPostOnCreate'],
                                    'streetAddress2FieldBazPostOnUpdate'          => $this->params['streetAddress2FieldBazPostOnUpdate'],
                                    'cityId'                                      => $this->params['cityId'],
                                    'cityName'                                    => $this->params['cityName'],
                                    'cityFieldDisabled'                           => $this->params['cityFieldDisabled'],
                                    'cityFieldBazPostOnCreate'                    => $this->params['cityFieldBazPostOnCreate'],
                                    'cityFieldBazPostOnUpdate'                    => $this->params['cityFieldBazPostOnUpdate'],
                                    'postCode'                                    => $this->params['postCode'],
                                    'postCodeFieldDisabled'                       => $this->params['postCodeFieldDisabled'],
                                    'postCodeFieldBazPostOnCreate'                => $this->params['postCodeFieldBazPostOnCreate'],
                                    'postCodeFieldBazPostOnUpdate'                => $this->params['postCodeFieldBazPostOnUpdate'],
                                    'stateId'                                     => $this->params['stateId'],
                                    'stateName'                                   => $this->params['stateName'],
                                    'stateFieldDisabled'                          => $this->params['stateFieldDisabled'],
                                    'stateFieldBazPostOnCreate'                   => $this->params['stateFieldBazPostOnCreate'],
                                    'stateFieldBazPostOnUpdate'                   => $this->params['stateFieldBazPostOnUpdate'],
                                    'countryId'                                   => $this->params['countryId'],
                                    'countryName'                                 => $this->params['countryName'],
                                    'countryFieldDisabled'                        => $this->params['countryFieldDisabled'],
                                    'countryFieldBazPostOnCreate'                 => $this->params['countryFieldBazPostOnCreate'],
                                    'countryFieldBazPostOnUpdate'                 => $this->params['countryFieldBazPostOnUpdate']
                                ]
                            ) .
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
                                                'hidden'                  => true,
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'update-address'    => [
                                                'title'                   => 'Update',
                                                'hidden'                  => true,
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'cancel-address'    => [
                                                'title'                   => 'Cancel',
                                                'hidden'                  => true,
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
                <div class="col">';
                    if (isset($this->addressesParams['addressTypes']) && count($this->addressesParams['addressTypes']) > 0) {
                        $addressTypesIds = [];

                        foreach ($this->addressesParams['addressTypes'] as $type) {
                            $addressTypesIds[$this->compSecId . '-addresses-' . $type['id']] = $type['id'];

                            $this->content .=
                                '<div class="row">
                                    <div class="col">' .
                                        $this->adminLTETags->useTag('fields',
                                            [
                                                'component'                 => $this->params['component'],
                                                'componentName'             => $this->params['componentName'],
                                                'componentId'               => $this->params['componentId'],
                                                'sectionId'                 => $this->params['sectionId'],
                                                'fieldId'                   => 'addresses-' . $type['id'],
                                                'fieldLabel'                => $type['name'],
                                                'fieldType'                 => 'html',
                                                'fieldHelp'                 => true,
                                                'fieldHelpTooltipContent'   => $type['name'] . ' Note: First address is the list is primary address',
                                                'fieldAdditionalClass'      => 'mb-0',
                                                'fieldRequired'             => false,
                                                'fieldBazScan'              => false,
                                                'fieldBazJstreeSearch'      => true,
                                                'fieldBazPostOnCreate'      => false,
                                                'fieldBazPostOnUpdate'      => false
                                            ]
                                        ) .
                                        '<ul class="list-group list-group-sortable" data-sortlisttypeid="' . $type['id'] . '" id="' . $this->compSecId . '-sortable-addresses-' . $type['id'] . '">';

                                            if (isset($this->params['address_ids']) &&
                                                count($this->params['address_ids']) > 0 &&
                                                count($this->params['address_ids'][$type['id']]) > 0
                                            ) {
                                                foreach ($this->params['address_ids'][$type['id']] as $key => $address) {
                                                    if ($key === 0) {
                                                        $listType = 'success';
                                                    } else {
                                                        $listType = 'secondary';
                                                    }

                                                    $this->content .=
                                                        '<li class="list-group-item list-group-item-' . $listType . '" area-disabled="false" style="cursor: pointer" data-listtypeid="' . $type['id'] . '" data-new="0" data-address-id="' . $address['id'] . '">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <i class="fa fa-sort fa-fw handle"></i>
                                                                </div>
                                                                <div class="col">
                                                                    <button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 addressDeleteButton">
                                                                        <i class="fa fas fa-fw text-xs fa-trash"></i>
                                                                    </button>
                                                                    <button data-sort-id="" type="button" class="btn btn-xs btn-primary float-right ml-1 addressEditButton">
                                                                        <i class="fa fas fa-fw text-xs fa-edit"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col list-group-item-data">
                                                                    <dl class="row mb-0">
                                                                        <dt class="text-uppercase mb-0 col-sm-4">Street Address</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-street">' . $address['street_address'] . '</dd>
                                                                        <dt class="text-uppercase mb-0 col-sm-4">Street Address 2</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-street2">' . $address['street_address_2'] . '</dd>
                                                                        <dt class="text-uppercase mb-0 col-sm-4">City</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-city" data-id="' . $address['city_id'] . '">' . $address['city_name'] . '</dd>
                                                                        <dt class="text-uppercase mb-0 col-sm-4">Post Code</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-postcode">' . $address['post_code'] . '</dd>
                                                                        <dt class="text-uppercase mb-0 col-sm-4">State</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-state" data-id="' . $address['state_id'] . '">' . $address['state_name'] . '</dd>
                                                                        <dt class="text-uppercase mb-0 col-sm-4">Country</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-country" data-id="' . $address['country_id'] . '">' . $address['country_name'] . '</dd>
                                                                    </dl>
                                                                </div>
                                                            </div>
                                                        </li>';
                                                }
                                            }

                                        $this->content .=
                                        '</ul>
                                    </div>
                                </div>
                                <hr>';
                        }
                        $addressTypesIds = Json::encode($addressTypesIds);
                    }
                $this->content .=
                '</div>
            </div>' .
            $this->inclAddressesJs($addressTypesIds);
    }

    protected function inclAddressesJs($addressTypesIds)
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
                    "' . $this->compSecId . '-address_types"                   : {
                        placeholder : "SELECT ADDRESS TYPE",
                        afterInit : function () {
                            var addressTypesIds = JSON.parse(\'' . $addressTypesIds . '\');
                            dataCollectionSection["data"]["address_ids"] = { }
                            dataCollectionSection["data"]["delete_address_ids"] = [];

                            $("#' . $this->compSecId . '-address_types").on("select2:select", function(e) {
                                var id = e.params.data.id;

                                toggleAddressFields(false);

                                initMainButtons(id);
                            });

                            function initMainButtons(id) {
                                $("#' . $this->compSecId . '-cancel-address").off();
                                $("#' . $this->compSecId . '-cancel-address").click(function(e) {
                                    $("#' . $this->compSecId . '-address_types").val(0);
                                    $("#' . $this->compSecId . '-address_types").trigger("change");
                                    $(".addressEditButton, .addressDeleteButton").attr("disabled", false);
                                    e.preventDefault();
                                    toggleAddressFields(true);
                                });
                                $("#' . $this->compSecId . '-add-address, #' . $this->compSecId . '-update-address").off();
                                $("#' . $this->compSecId . '-add-address, #' . $this->compSecId . '-update-address").click(function(e) {
                                    $(".addressEditButton, .addressDeleteButton").attr("disabled", false);
                                    e.preventDefault();
                                    extractData(id);
                                });
                            }

                            function toggleAddressFields(status, update = false) {
                                $("#' . $this->compSecId . '-address_id").attr("disabled", status);
                                $("#' . $this->compSecId . '-street_address").attr("disabled", status);
                                $("#' . $this->compSecId . '-street_address_2").attr("disabled", status);
                                $("#' . $this->compSecId . '-city_id").attr("disabled", status);
                                $("#' . $this->compSecId . '-city_name").attr("disabled", status);
                                $("#' . $this->compSecId . '-post_code").attr("disabled", status);
                                $("#' . $this->compSecId . '-state_id").attr("disabled", status);
                                $("#' . $this->compSecId . '-state_name").attr("disabled", status);
                                $("#' . $this->compSecId . '-country_id").attr("disabled", status);
                                $("#' . $this->compSecId . '-country_name").attr("disabled", status);
                                $("#' . $this->compSecId . '-cancel-address").attr("hidden", status);
                                $("#' . $this->compSecId . '-add-address").attr("hidden", status);
                                $("#' . $this->compSecId . '-update-address").attr("hidden", status);
                                if (status === true) {
                                    $("#' . $this->compSecId . '-address_types").attr("disabled", false);
                                    $("#' . $this->compSecId . '-address_id").val("");
                                    $("#' . $this->compSecId . '-street_address").val("");
                                    $("#' . $this->compSecId . '-street_address_2").val("");
                                    $("#' . $this->compSecId . '-city_id").val("");
                                    $("#' . $this->compSecId . '-city_name").val("");
                                    $("#' . $this->compSecId . '-post_code").val("");
                                    $("#' . $this->compSecId . '-state_id").val("");
                                    $("#' . $this->compSecId . '-state_name").val("");
                                    $("#' . $this->compSecId . '-country_id").val("");
                                    $("#' . $this->compSecId . '-country_name").val("");
                                }

                                if (update === true) {
                                    $("#' . $this->compSecId . '-add-address").attr("hidden", true);
                                    $("#' . $this->compSecId . '-update-address").attr("hidden", false);
                                } else {
                                    $("#' . $this->compSecId . '-update-address").attr("hidden", true);
                                }
                            }

                            function extractData(id) {
                                if ($("#' . $this->compSecId . '-street_address").val() === "") {
                                    $("#' . $this->compSecId . '-street_address").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_address").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-city_name").val() === "") {
                                    $("#' . $this->compSecId . '-city_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-city_name").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-post_code").val() === "") {
                                    $("#' . $this->compSecId . '-post_code").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-post_code").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-state_name").val() === "") {
                                    $("#' . $this->compSecId . '-state_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-state_name").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-country_name").val() === "") {
                                    $("#' . $this->compSecId . '-country_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-country_name").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else {
                                    $("#' . $this->compSecId . '-address_types").val(0);
                                    $("#' . $this->compSecId . '-address_types").trigger("change");
                                    var data = { };
                                    var addressId, addressNew;

                                    data["address_id"] = $("#' . $this->compSecId . '-address_id").val();
                                    data["street_address"] = $("#' . $this->compSecId . '-street_address").val();
                                    data["street_address_2"] = $("#' . $this->compSecId . '-street_address_2").val();
                                    data["city_id"] = $("#' . $this->compSecId . '-city_id").val();
                                    data["city_name"] = $("#' . $this->compSecId . '-city_name").val();
                                    data["post_code"] = $("#' . $this->compSecId . '-post_code").val();
                                    data["state_id"] = $("#' . $this->compSecId . '-state_id").val();
                                    data["state_name"] = $("#' . $this->compSecId . '-state_name").val();
                                    data["country_id"] = $("#' . $this->compSecId . '-country_id").val();
                                    data["country_name"] = $("#' . $this->compSecId . '-country_name").val();

                                    var html =
                                        \'<dl class="row mb-0">\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Street Address</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-street">\' + data["street_address"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Street Address 2</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-street2">\' + data["street_address_2"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">City</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-city" data-id="\' + data["city_id"] + \'">\' + data["city_name"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Post Code</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-postcode">\' + data["post_code"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">State</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-state" data-id="\' + data["state_id"] + \'">\' + data["state_name"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Country</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-country" data-id="\' + data["country_id"] + \'">\' + data["country_name"] + \'</dd>\' +
                                        \'</dl>\';

                                    if ($("#' . $this->compSecId . '-sortable-addresses-" + id).length > 0) {
                                        var listType;
                                        if ($("#' . $this->compSecId . '-sortable-addresses-" + id + " li").length > 0) {
                                            listType = "secondary";
                                        } else {
                                            listType = "success";
                                        }

                                        if (data["address_id"] === "") {
                                            addressId = Date.now();
                                            addressNew = "1";
                                        } else {
                                            addressId = data["address_id"];
                                            addressNew = "0";
                                        }

                                        var list =
                                            \'<li class="list-group-item list-group-item-\' + listType +
                                                \'" area-disabled="false" style="cursor: pointer" data-listtypeid="\' + id +
                                                \'" data-new="\' + addressNew + \'" data-address-id="\' + addressId + \'">\' +
                                                \'<div class="row">\' +
                                                    \'<div class="col">\' +
                                                        \'<i class="fa fa-sort fa-fw handle"></i>\' +
                                                    \'</div>\' +
                                                    \'<div class="col">\' +
                                                        \'<button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 addressDeleteButton">\' +
                                                            \'<i class="fa fas fa-fw text-xs fa-trash"></i>\' +
                                                        \'</button>\' +
                                                        \'<button data-sort-id="" type="button" class="btn btn-xs btn-primary float-right ml-1 addressEditButton">\' +
                                                            \'<i class="fa fas fa-fw text-xs fa-edit"></i>\' +
                                                        \'</button>\' +
                                                    \'</div>\' +
                                                \'</div>\' +
                                                \'<div class="row">\' +
                                                    \'<div class="col list-group-item-data">\' +
                                                        html +
                                                    \'</div>\' +
                                                \'</div>\' +
                                            \'</li>\';
                                        //Check this again for multiple addresses in the list. This might be a bug.
                                        //Also add No-data div like we did in dropzone.
                                        if (addressNew === "1") {
                                            $("#' . $this->compSecId . '-sortable-addresses-" + id).append(list);
                                        } else {
                                            $("#' . $this->compSecId . '-sortable-addresses-" + id + " .list-group-item-data").empty().append(html);
                                        }
                                    }

                                    toggleAddressFields(true);
                                    initSortable("' . $this->compSecId . '-sortable-addresses-" + id, id);
                                    collectData();
                                    registerAddressButtons();
                                }
                            }

                            function initSortable(element, id) {
                                var el = document.getElementById(element);
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = { };
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"][id] = { };
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"][id] = Sortable.create(el, {
                                    dataIdAttr : "data-address-id",
                                    onEnd: function(e) {
                                        if (e.newIndex === 0) {
                                            $($(e.from).find("li")).each(function(index, li) {
                                                $(li).removeClass("list-group-item-success");
                                                $(li).addClass("list-group-item-secondary");
                                            });
                                            $(e.item).removeClass("list-group-item-secondary");
                                            $(e.item).addClass("list-group-item-success");
                                        }
                                        collectData();
                                    }
                                });
                            }

                            function collectData() {
                                for (var typeId in addressTypesIds) {
                                    dataCollectionSection["data"]["address_ids"][addressTypesIds[typeId]] = { };

                                    if ($("#' . $this->compSecId . '-sortable-addresses-" + addressTypesIds[typeId] + " li").length > 0) {
                                        $("#' . $this->compSecId . '-sortable-addresses-" + addressTypesIds[typeId] + " li").each(function(index, id) {
                                            var data = { };
                                            data["seq"] = index;
                                            var addressId;

                                            $(id).find("dd").each(function(index,dd) {
                                                addressId = $(dd).parents("li").data("address-id");
                                                data["new"] = $(dd).parents("li").data("new");
                                                if ($(dd).is(".cla-street")) {
                                                    data["street_address"] = $(dd).html();
                                                } else if ($(dd).is(".cla-street2")) {
                                                    data["street_address_2"] = $(dd).html();
                                                } else if ($(dd).is(".cla-city")) {
                                                    data["city_id"] = $(dd).data("id");
                                                    data["city_name"] = $(dd).html();
                                                } else if ($(dd).is(".cla-postcode")) {
                                                    data["post_code"] = $(dd).html();
                                                } else if ($(dd).is(".cla-state")) {
                                                    data["state_id"] = $(dd).data("id");
                                                    data["state_name"] = $(dd).html();
                                                } else if ($(dd).is(".cla-country")) {
                                                    data["country_id"] = $(dd).data("id");
                                                    data["country_name"] = $(dd).html();
                                                }
                                            });

                                            dataCollectionSection["data"]["address_ids"][addressTypesIds[typeId]][addressId] = data;
                                        });

                                    }
                                }
                            }

                            function registerAddressButtons() {
                                $(".addressEditButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        $(this).attr("disabled", true);
                                        $(this).siblings(".addressDeleteButton").attr("disabled", true);

                                        var typeId;

                                        $($(this).parents("li").children(".row")[1]).find("dd").each(function(index,dd) {
                                            typeId = $(dd).parents("li").data("listtypeid");

                                            $("#' . $this->compSecId . '-address_types").val(typeId);
                                            $("#' . $this->compSecId . '-address_types").trigger("change");
                                            $("#' . $this->compSecId . '-address_types").attr("disabled", true);

                                            if ($(dd).is(".cla-street")) {
                                                $("#' . $this->compSecId . '-street_address").val($(dd).html());
                                            } else if ($(dd).is(".cla-street2")) {
                                                $("#' . $this->compSecId . '-street_address_2").val($(dd).html());
                                            } else if ($(dd).is(".cla-city")) {
                                                $("#' . $this->compSecId . '-city_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-city_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-postcode")) {
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
                                        initMainButtons(typeId);
                                    });
                                });

                                $(".addressDeleteButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {

                                        var addressesCount = $(this).parents("ul").children("li").length;

                                        if (addressesCount > 1) {
                                            if ($(this).parents("li").is(".list-group-item-success")) {

                                                $($(this).parents("li").siblings("li")[0]).removeClass("list-group-item-secondary");
                                                $($(this).parents("li").siblings("li")[0]).addClass("list-group-item-success");
                                            }
                                        }
                                        dataCollectionSection["data"]["delete_address_ids"].push($(this).parents("li").data("address-id"));

                                        $(this).parents("li").remove();

                                        collectData();
                                    });
                                });
                            }

                            $(".list-group-sortable").each(function(index, ul) {
                                initSortable($(ul)[0].id, $(ul).data("sortlisttypeid"));
                            });
                            collectData();
                            registerAddressButtons();
                        }
                    }
                });
            </script>';

        return $inclJs;
    }
}