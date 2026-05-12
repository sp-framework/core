<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Addresses;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Single
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

        $this->buildSingleAddressLayout();
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

        $fieldsArr = ['addressReference','attentionTo','streetAddress','streetAddress2','streetAddress3','streetAddress4','cityId','cityName','postCode','stateId','stateName','countryId','countryName'];
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

    protected function buildSingleAddressLayout()
    {
        $this->content .=
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'address_reference',
                            'fieldLabel'                            => 'Address Reference',
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => 'Address Reference. Example: Main, Shipping, etc.',
                            'fieldHidden'                           => false,
                            'fieldRequired'                         => true,
                            'fieldBazScan'                          => true,
                            'fieldBazPostOnCreate'                  => $this->addressesParams['addressReferenceFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'                  => $this->addressesParams['addressReferenceFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => $this->addressesParams['addressReference']
                        ]
                    ) .
                '</div>
            </div>';

        if (isset($this->addressesParams['includeAttentionTo']) && $this->addressesParams['includeAttentionTo'] === true) {
            $this->content .= $this->inclAttentionTo();
        }

        if (isset($this->addressesParams['includeStreet']) && $this->addressesParams['includeStreet'] === true) {
            $this->content .= $this->inclStreet();
        }

        if (isset($this->addressesParams['includeStreetExt']) && $this->addressesParams['includeStreetExt'] === true) {
            $this->content .= $this->inclStreetExt();
        }

        if (isset($this->addressesParams['searchType'])) {
            if ($this->addressesParams['searchType'] === 'city') {
                $this->content .=
                    '<div class="row">' . $this->inclCity() . $this->inclPostCode() . '</div>' .
                    '<div class="row">' . $this->inclState() . $this->inclCountry() . '</div>';

                $this->content .=
                    $this->inclBaseJs() .
                        $this->inclCityJs() .
                        $this->inclPostCodeJs() .
                        $this->inclStateJs() .
                        $this->inclCountryJs();

                $this->content .=
                    '});</script>';
            } else if ($this->addressesParams['searchType'] === 'state') {
                $this->content .=
                    '<div class="row">' . $this->inclState() . $this->inclCountry() . '</div>';

                $this->content .=
                    $this->inclBaseJs() . $this->inclStateJs();

                $this->content .=
                    '});</script>';
            } else if ($this->addressesParams['searchType'] === 'country') {
                $this->content .=
                    '<div class="row">' . $this->inclCountry() . '</div>';

                $this->content .=
                    $this->inclBaseJs() . $this->inclCountryJs();

                $this->content .=
                    '});</script>';
            }
        } else {
            $this->content .=
                '<div class="row">' . $this->inclCity() . $this->inclPostCode() . '</div>' .
                '<div class="row">' . $this->inclState() . $this->inclCountry() . '</div>';

            $this->content .=
                    $this->inclBaseJs() .
                        $this->inclCityJs() .
                        $this->inclPostCodeJs() .
                        $this->inclStateJs() .
                        $this->inclCountryJs();

            $this->content .=
                '});</script>';
        }
    }

    protected function inclAttentionTo()
    {
        return
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'attention_to',
                            'fieldLabel'                            => $this->addressesParams['attentionToFieldLabel'],
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => $this->addressesParams['attentionToFieldLabel'],
                            'fieldHidden'                           => $this->addressesParams['attentionToFieldHidden'],
                            'fieldDisabled'                         => $this->addressesParams['attentionToFieldDisabled'],
                            'fieldRequired'                         => $this->addressesParams['attentionToFieldRequired'],
                            'fieldBazScan'                          => true,
                            'fieldBazJstreeSearch'                  => true,
                            'fieldBazPostOnCreate'                  => $this->addressesParams['attentionToFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'                  => $this->addressesParams['attentionToFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => $this->addressesParams['attentionTo']
                        ]
                    ) .
                '</div>
            </div>';
    }

    protected function inclStreet()
    {
        return
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'street_address',
                            'fieldLabel'                            => $this->addressesParams['streetAddressFieldLabel'],
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => $this->addressesParams['streetAddressFieldLabel'],
                            'fieldHidden'                           => $this->addressesParams['streetAddressFieldHidden'],
                            'fieldDisabled'                         => $this->addressesParams['streetAddressFieldDisabled'],
                            'fieldRequired'                         => $this->addressesParams['streetAddressFieldRequired'],
                            'fieldBazScan'                          => true,
                            'fieldBazJstreeSearch'                  => true,
                            'fieldBazPostOnCreate'                  => $this->addressesParams['streetAddressFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'                  => $this->addressesParams['streetAddressFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => $this->params['streetAddress']
                        ]
                    ) .
                '</div>
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'street_address_2',
                            'fieldLabel'                            => $this->addressesParams['streetAddress2FieldLabel'],
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => $this->addressesParams['streetAddress2FieldLabel'],
                            'fieldHidden'                           => $this->addressesParams['streetAddress2FieldHidden'],
                            'fieldDisabled'                         => $this->addressesParams['streetAddress2FieldDisabled'],
                            'fieldRequired'                         => $this->addressesParams['streetAddress2FieldRequired'],
                            'fieldBazScan'                          => true,
                            'fieldBazJstreeSearch'                  => true,
                            'fieldBazPostOnCreate'                  => $this->addressesParams['streetAddress2FieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'                  => $this->addressesParams['streetAddress2FieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => $this->params['streetAddress2']
                        ]
                    ) .
                '</div>
            </div>';
    }

    protected function inclStreetExt()
    {
        return
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'street_address_3',
                            'fieldLabel'                            => $this->addressesParams['streetAddress3FieldLabel'],
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => $this->addressesParams['streetAddress3FieldLabel'],
                            'fieldHidden'                           => $this->addressesParams['streetAddress3FieldHidden'],
                            'fieldDisabled'                         => $this->addressesParams['streetAddress3FieldDisabled'],
                            'fieldRequired'                         => $this->addressesParams['streetAddress3FieldRequired'],
                            'fieldBazScan'                          => true,
                            'fieldBazJstreeSearch'                  => true,
                            'fieldBazPostOnCreate'                  => $this->addressesParams['streetAddress3FieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'                  => $this->addressesParams['streetAddress3FieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => $this->params['streetAddress3']
                        ]
                    ) .
                '</div>
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'street_address_4',
                            'fieldLabel'                            => $this->addressesParams['streetAddress4FieldLabel'],
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => $this->addressesParams['streetAddress4FieldLabel'],
                            'fieldHidden'                           => $this->addressesParams['streetAddress4FieldHidden'],
                            'fieldDisabled'                         => $this->addressesParams['streetAddress4FieldDisabled'],
                            'fieldRequired'                         => $this->addressesParams['streetAddress4FieldRequired'],
                            'fieldBazScan'                          => true,
                            'fieldBazJstreeSearch'                  => true,
                            'fieldBazPostOnCreate'                  => $this->addressesParams['streetAddress4FieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'                  => $this->addressesParams['streetAddress4FieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => $this->params['streetAddress4']
                        ]
                    ) .
                '</div>
            </div>';
    }

    protected function inclCity()
    {
        return
            '<div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'city_id',
                        'fieldLabel'                            => $this->addressesParams['cityFieldLabel'],
                        'fieldHidden'                           => true,
                        'fieldType'                             => 'input',
                        'fieldBazScan'                          => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['cityFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['cityFieldBazPostOnUpdate'],
                        'fieldValue'                            => $this->params['cityId']
                    ]
                ) .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'city_name',
                        'fieldLabel'                            => $this->addressesParams['cityFieldLabel'],
                        'fieldType'                             => 'input',
                        'fieldPlaceholder'                      => 'Search City',
                        'fieldHelp'                             => true,
                        'fieldHelpTooltipContent'               => 'Search ' . $this->addressesParams['cityFieldLabel'] . '. If there are no search results, try fixing the spelling or use full form for the name, ex=> use Saint instead of St.',
                        'fieldBazScan'                          => true,
                        'fieldBazJstreeSearch'                  => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['cityFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['cityFieldBazPostOnUpdate'],
                        'fieldHidden'                           => $this->addressesParams['cityFieldHidden'],
                        'fieldDisabled'                         => $this->addressesParams['cityFieldDisabled'],
                        'fieldRequired'                         => $this->addressesParams['cityFieldRequired'],
                        'fieldDataInputMinLength'               => 1,
                        'fieldDataInputMaxLength'               => 100,
                        'fieldValue'                            => $this->params['cityName']
                    ]
                ) .
            '</div>';
    }

    protected function inclPostCode()
    {
        return
            '<div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'post_code',
                        'fieldLabel'                            => $this->addressesParams['postCodeFieldLabel'],
                        'fieldType'                             => 'input',
                        'fieldHelp'                             => true,
                        'fieldHelpTooltipContent'               => $this->addressesParams['postCodeFieldLabel'] . ' of the address.',
                        'fieldHidden'                           => $this->addressesParams['postCodeFieldHidden'],
                        'fieldDisabled'                         => $this->addressesParams['postCodeFieldDisabled'],
                        'fieldRequired'                         => $this->addressesParams['postCodeFieldRequired'],
                        'fieldBazScan'                          => true,
                        'fieldBazJstreeSearch'                  => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['postCodeFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['postCodeFieldBazPostOnUpdate'],
                        'fieldDataInputMinLength'               => 1,
                        'fieldDataInputMaxLength'               => 50,
                        'fieldValue'                            => $this->params['postCode']
                    ]
                ) .
            '</div>';
    }

    protected function inclState()
    {
        return
            '<div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'state_id',
                        'fieldLabel'                            => $this->addressesParams['stateFieldLabel'],
                        'fieldHidden'                           => true,
                        'fieldType'                             => 'input',
                        'fieldBazScan'                          => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['stateFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['stateFieldBazPostOnUpdate'],
                        'fieldValue'                            => $this->params['stateId']
                    ]
                ) .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'state_name',
                        'fieldPlaceholder'                      => 'Search State',
                        'fieldLabel'                            => $this->addressesParams['stateFieldLabel'],
                        'fieldType'                             => 'input',
                        'fieldHelp'                             => true,
                        'fieldHelpTooltipContent'               => 'Enter ' . $this->addressesParams['stateFieldLabel'],
                        'fieldBazScan'                          => true,
                        'fieldBazJstreeSearch'                  => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['stateFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['stateFieldBazPostOnUpdate'],
                        'fieldHidden'                           => $this->addressesParams['stateFieldHidden'],
                        'fieldDisabled'                         => $this->addressesParams['stateFieldDisabled'],
                        'fieldRequired'                         => $this->addressesParams['stateFieldRequired'],
                        'fieldDataInputMinLength'               => 1,
                        'fieldDataInputMaxLength'               => 100,
                        'fieldValue'                            => $this->params['stateName']
                    ]
                ) .
            '</div>';
    }

    protected function inclCountry()
    {
        return
            '<div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'country_id',
                        'fieldLabel'                            => $this->addressesParams['countryFieldLabel'],
                        'fieldHidden'                           => true,
                        'fieldType'                             => 'input',
                        'fieldBazScan'                          => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['countryFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['countryFieldBazPostOnUpdate'],
                        'fieldValue'                            => $this->params['countryId']
                    ]
                ) .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'country_name',
                        'fieldPlaceholder'                      => 'Search Country',
                        'fieldLabel'                            => $this->addressesParams['countryFieldLabel'],
                        'fieldType'                             => 'input',
                        'fieldHelp'                             => true,
                        'fieldHelpTooltipContent'               => 'Enter ' . $this->addressesParams['countryFieldLabel'],
                        'fieldBazScan'                          => true,
                        'fieldBazJstreeSearch'                  => true,
                        'fieldBazPostOnCreate'                  => $this->addressesParams['countryFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'                  => $this->addressesParams['countryFieldBazPostOnUpdate'],
                        'fieldHidden'                           => $this->addressesParams['countryFieldHidden'],
                        'fieldDisabled'                         => $this->addressesParams['countryFieldDisabled'],
                        'fieldRequired'                         => $this->addressesParams['countryFieldRequired'],
                        'fieldDataInputMinLength'               => 1,
                        'fieldDataInputMaxLength'               => 100,
                        'fieldValue'                            => $this->params['countryName']
                    ]
                ) .
            '</div>';
    }

    protected function inclBaseJs()
    {
        $baseJs =
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
                $.extend(dataCollectionSection, {';

        if (isset($this->params['includeStreet']) && $this->params['includeStreet'] === true) {
            $baseJs .=
                '"' . $this->compSecId . '-address_reference"   : { },
                "' . $this->compSecId . '-attention_to"         : { },
                "' . $this->compSecId . '-street_address"       : { },
                "' . $this->compSecId . '-street_address_2"     : { },';
        }

        if (isset($this->params['includeStreetExt']) && $this->params['includeStreetExt'] === true) {
            $baseJs .=
                '"' . $this->compSecId . '-street_address_3"    : { },
                "' . $this->compSecId . '-street_address_4"     : { },';
        }

        return $baseJs;
    }

    protected function inclCityJs()
    {
        $cityJs =
            '"' . $this->compSecId . '-city_id"         : { },
            "' . $this->compSecId . '-city_name"        : {
                afterInit   : function() {
                    dataCollectionSection["' . $this->compSecId . '-form"]["autoCompleteCities"] =
                        new autoComplete({
                            data: {
                                src: async() => {
                                    const url = "' . $this->links->url("system/geo/cities/searchCity") . '";

                                    var myHeaders = new Headers();
                                    myHeaders.append("accept", "application/json");

                                    var formdata = new FormData();
                                    formdata.append("search", document.querySelector("#' . $this->compSecId . '-city_name").value);
                                    formdata.append($("#security-token").attr("name"), $("#security-token").val());

                                    var requestOptions = {
                                        method: "POST",
                                        headers: myHeaders,
                                        body: formdata
                                    };

                                    const responseData = await fetch(url, requestOptions);

                                    const response = await responseData.json();

                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("name", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }

                                    if (response.responseData.cities) {
                                        return response.responseData.cities;
                                    } else {
                                        return [];
                                    }
                                },
                                key: ["name"],
                                cache: false
                            },
                            selector: "#' . $this->compSecId . '-city_name",
                            threshold : 4,
                            debounce: 500,
                            searchEngine: "strict",
                            resultsList: {
                                render: true,
                                container: source => {
                                    source.setAttribute("id", "' . $this->compSecId . '-city_name_list");
                                    source.setAttribute("class", "autoComplete_results");
                                },
                                destination: "#' . $this->compSecId . '-city_name",
                                position: "afterend",
                                element: "div",
                                className: "autoComplete_results"
                            },
                            maxResults: 5,
                            highlight: true,
                            resultItem: {
                                content: (data, source) => {
                                    var postCode = "0";

                                    if (data.value.postcode) {
                                        postCode = data.value.postcode;
                                    }

                                    source.innerHTML = data.match + " <span>(Post Code: " + postCode + ", State: " + data.value.state_name + ", Country: " + data.value.country_name + ")</span>";
                                },
                                element: "div"
                            },
                            noResults: () => {
                                const result = document.createElement("li");
                                result.setAttribute("class", "autoComplete_result text-danger");
                                result.setAttribute("tabindex", "1");
                                result.innerHTML = "No search results. Click field help for more information.";

                                if (document.querySelector("#' . $this->compSecId . '-city_name_list")) {
                                    $("#' . $this->compSecId . '-city_name_list").empty().append(result);
                                } else {
                                    $("#' . $this->compSecId . '-city_name").parent(".form-group").append(
                                        \'<div id="' . $this->compSecId . '-city_name_list" class="autoComplete_results"></div>\'
                                    );
                                    document.querySelector("#' . $this->compSecId . '-city_name_list").appendChild(result);
                                }
                            },
                            onSelection: feedback => {
                                $("#' . $this->compSecId . '-city_id").val(feedback.selection.value.id);
                                $("#' . $this->compSecId . '-city_id").attr("value", feedback.selection.value.id);
                                $("#' . $this->compSecId . '-city_name").blur();
                                $("#' . $this->compSecId . '-city_name").val(feedback.selection.value.name);
                                $("#' . $this->compSecId . '-post_code").val(feedback.selection.value.postcode);
                                $("#' . $this->compSecId . '-post_code").attr("value", feedback.selection.value.postcode);
                                $("#' . $this->compSecId . '-state_id").val(feedback.selection.value.state_id);
                                $("#' . $this->compSecId . '-state_id").attr("value", feedback.selection.value.state_id);
                                $("#' . $this->compSecId . '-state_name").val(feedback.selection.value.state_name);
                                $("#' . $this->compSecId . '-state_name").attr("value", feedback.selection.value.state_name);
                                $("#' . $this->compSecId . '-country_id").val(feedback.selection.value.country_id);
                                $("#' . $this->compSecId . '-country_id").attr("value", feedback.selection.value.country_id);
                                $("#' . $this->compSecId . '-country_name").val(feedback.selection.value.country_name);
                                $("#' . $this->compSecId . '-country_name").attr("value", feedback.selection.value.country_name);
                            }
                    });
                    // On delete
                    $("#' . $this->compSecId . '-city_name").on("input propertychange", function() {
                        $("#' . $this->compSecId . '-city_id").val(0);
                        $("#' . $this->compSecId . '-city_id").attr("value", 0);
                        $("#' . $this->compSecId . '-post_code").val("");
                        $("#' . $this->compSecId . '-state_id").val(0);
                        $("#' . $this->compSecId . '-state_id").attr("value", 0);
                        $("#' . $this->compSecId . '-state_name").val("");
                        $("#' . $this->compSecId . '-state_name").attr("value", "");
                        $("#' . $this->compSecId . '-country_id").val(0);
                        $("#' . $this->compSecId . '-country_id").attr("value", 0);
                        $("#' . $this->compSecId . '-country_name").val("");
                        $("#' . $this->compSecId . '-country_name").attr("value", "");
                    });

                    $("#' . $this->compSecId . '-city_name").focusout(function() {
                        $("#' . $this->compSecId . '-city_name_list").children("li").remove();
                    });
                }
            },';

        return $cityJs;
    }

    protected function inclPostCodeJs()
    {
        return
            '"' . $this->compSecId . '-post_code"        : { },';
    }

    protected function inclStateJs()
    {
        $stateJs =
            '"' . $this->compSecId . '-state_id"         : { },
            "' . $this->compSecId . '-state_name"       : {
                afterInit   : function() {
                    dataCollectionSection["' . $this->compSecId . '-form"]["autoCompleteStates"] =
                        new autoComplete({
                            data: {
                                src: async() => {
                                    const url = "' . $this->links->url("system/geo/states/searchState") . '";

                                    var myHeaders = new Headers();
                                    myHeaders.append("accept", "application/json");

                                    var formdata = new FormData();
                                    formdata.append("search", document.querySelector("#' . $this->compSecId . '-state_name").value);
                                    formdata.append($("#security-token").attr("name"), $("#security-token").val());

                                    var requestOptions = {
                                        method: "POST",
                                        headers: myHeaders,
                                        body: formdata
                                    };

                                    const responseData = await fetch(url, requestOptions);

                                    const response = await responseData.json();

                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("name", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }

                                    if (response.responseData.states) {
                                        return response.responseData.states;
                                    } else {
                                        return [];
                                    }
                                },
                                key: ["name"],
                                cache: false
                            },
                            selector: "#' . $this->compSecId . '-state_name",
                            debounce: 500,
                            searchEngine: "strict",
                            resultsList: {
                                render: true,
                                container: source => {
                                    source.setAttribute("id", "' . $this->compSecId . '-state_name_list");
                                    source.setAttribute("class", "autoComplete_results");
                                },
                                destination: "#' . $this->compSecId . '-state_name",
                                position: "afterend",
                                element: "div",
                                className: "autoComplete_results"
                            },
                            maxResults: 5,
                            highlight: true,
                            resultItem: {
                                content: (data, source) => {
                                    source.innerHTML = data.match + " <span>(Country: " + data.value.country_name + ")</span>";
                                },
                                element: "div"
                            },
                            noResults: () => {
                                const result = document.createElement("li");
                                result.setAttribute("class", "autoComplete_result text-danger");
                                result.setAttribute("tabindex", "1");
                                result.innerHTML = "No search results. Click field help for more information.";

                                if (document.querySelector("#' . $this->compSecId . '-state_name_list")) {
                                    $("#' . $this->compSecId . '-state_name_list").empty().append(result);
                                } else {
                                    $("#' . $this->compSecId . '-state_name").parent(".form-group").append(
                                        \'<div id="' . $this->compSecId . '-state_name_list" class="autoComplete_results"></div>\'
                                    );
                                    document.querySelector("#' . $this->compSecId . '-state_name_list").appendChild(result);
                                }
                            },
                            onSelection: feedback => {
                                $("#' . $this->compSecId . '-state_id").val(feedback.selection.value.id);
                                $("#' . $this->compSecId . '-state_id").attr("value", feedback.selection.value.id);
                                $("#' . $this->compSecId . '-state_name").val(feedback.selection.value.name);
                                $("#' . $this->compSecId . '-state_name").attr("value", feedback.selection.value.name);
                                $("#' . $this->compSecId . '-country_id").val(feedback.selection.value.country_id);
                                $("#' . $this->compSecId . '-country_id").attr("value", feedback.selection.value.country_id);
                                $("#' . $this->compSecId . '-country_name").val(feedback.selection.value.country_name);
                                $("#' . $this->compSecId . '-country_name").attr("value", feedback.selection.value.country_name);
                            }
                    });
                    // On delete
                    $("#' . $this->compSecId . '-state_name").on("input propertychange", function() {
                        $("#' . $this->compSecId . '-state_id").val(0);
                        $("#' . $this->compSecId . '-state_id").attr("value", 0);
                        $("#' . $this->compSecId . '-country_id").val(0);
                        $("#' . $this->compSecId . '-country_id").attr("value", 0);
                        $("#' . $this->compSecId . '-country_name").val("");
                        $("#' . $this->compSecId . '-country_name").attr("value", "");
                    });

                    $("#' . $this->compSecId . '-state_name").focusout(function() {
                        $("#' . $this->compSecId . '-state_name_list").children("li").remove();
                    });
                }
            },';

        return $stateJs;

    }

    protected function inclCountryJs()
    {
        $countryJs =
            '"' . $this->compSecId . '-country_id"       : { },
            "' . $this->compSecId . '-country_name"     : {
                afterInit   : function() {
                    dataCollectionSection["' . $this->compSecId . '-form"]["autoCompleteCountries"] =
                        new autoComplete({
                            data: {
                                src: async() => {
                                    const url = "' . $this->links->url("system/geo/countries/searchCountry") . '";

                                    var myHeaders = new Headers();
                                    myHeaders.append("accept", "application/json");

                                    var formdata = new FormData();
                                    formdata.append("search", document.querySelector("#' . $this->compSecId . '-country_name").value);
                                    formdata.append($("#security-token").attr("name"), $("#security-token").val());

                                    var requestOptions = {
                                        method: "POST",
                                        headers: myHeaders,
                                        body: formdata
                                    };

                                    const responseData = await fetch(url, requestOptions);

                                    const response = await responseData.json();

                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("name", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }

                                    if (response.responseData.countries) {
                                        return response.responseData.countries;
                                    } else {
                                        return [];
                                    }
                                },
                                key: ["name"],
                                cache: false
                            },
                            selector: "#' . $this->compSecId . '-country_name",
                            debounce: 500,
                            searchEngine: "strict",
                            resultsList: {
                                render: true,
                                container: source => {
                                    source.setAttribute("id", "' . $this->compSecId . '-country_name_list");
                                    source.setAttribute("class", "autoComplete_results");
                                },
                                destination: "#' . $this->compSecId . '-country_name",
                                position: "afterend",
                                element: "div",
                                className: "autoComplete_results"
                            },
                            maxResults: 5,
                            highlight: true,
                            resultItem: {
                                content: (data, source) => {
                                    source.innerHTML = data.match;
                                },
                                element: "div"
                            },
                            noResults: () => {
                                const result = document.createElement("li");
                                result.setAttribute("class", "autoComplete_result text-danger");
                                result.setAttribute("tabindex", "1");
                                result.innerHTML = "No search results. Click field help for more information.";
                                if (document.querySelector("#' . $this->compSecId . '-country_name_list")) {
                                    $("#' . $this->compSecId . '-country_name_list").empty().append(result);
                                } else {
                                    $("#' . $this->compSecId . '-country_name").parent(".form-group").append(
                                        \'<div id="' . $this->compSecId . '-country_name_list" class="autoComplete_results"></div>\'
                                    );
                                    document.querySelector("#' . $this->compSecId . '-country_name_list").appendChild(result);
                                }
                            },
                            onSelection: feedback => {
                                $("#' . $this->compSecId . '-country_id").val(feedback.selection.value.id);
                                $("#' . $this->compSecId . '-country_id").attr("value", feedback.selection.value.id);
                                $("#' . $this->compSecId . '-country_name").val(feedback.selection.value.name);
                                $("#' . $this->compSecId . '-country_name").attr("value", feedback.selection.value.name);
                            }
                    });

                    $("#' . $this->compSecId . '-country_name").focusout(function() {
                        $("#' . $this->compSecId . '-country_name_list").children("li").remove();
                    });
                }
            }';

        return $countryJs;
    }
}
