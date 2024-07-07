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

        $this->addressesParams['attentionToFieldHidden'] =
            isset($this->params['attentionToFieldHidden']) &&
                $this->params['attentionToFieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['streetAddressFieldHidden'] =
            isset($this->params['streetAddressFieldHidden']) &&
                $this->params['streetAddressFieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['streetAddress2FieldHidden'] =
            isset($this->params['streetAddress2FieldHidden']) &&
                $this->params['streetAddress2FieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['cityFieldHidden'] =
            isset($this->params['cityFieldHidden']) &&
                $this->params['cityFieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['postCodeFieldHidden'] =
            isset($this->params['postCodeFieldHidden']) &&
                $this->params['postCodeFieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['stateFieldHidden'] =
            isset($this->params['stateFieldHidden']) &&
                $this->params['stateFieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['countryFieldHidden'] =
            isset($this->params['countryFieldHidden']) &&
                $this->params['countryFieldHidden'] === true ?
            true :
            false;

        $this->addressesParams['attentionToFieldDisabled'] =
            isset($this->params['attentionToFieldDisabled']) &&
                $this->params['attentionToFieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['streetAddressFieldDisabled'] =
            isset($this->params['streetAddressFieldDisabled']) &&
                $this->params['streetAddressFieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['streetAddress2FieldDisabled'] =
            isset($this->params['streetAddress2FieldDisabled']) &&
                $this->params['streetAddress2FieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['cityFieldDisabled'] =
            isset($this->params['cityFieldDisabled']) &&
                $this->params['cityFieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['postCodeFieldDisabled'] =
            isset($this->params['postCodeFieldDisabled']) &&
                $this->params['postCodeFieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['stateFieldDisabled'] =
            isset($this->params['stateFieldDisabled']) &&
                $this->params['stateFieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['countryFieldDisabled'] =
            isset($this->params['countryFieldDisabled']) &&
                $this->params['countryFieldDisabled'] === true ?
            true :
            false;

        $this->addressesParams['attentionToFieldRequired'] =
            isset($this->params['attentionToFieldRequired']) &&
                $this->params['attentionToFieldRequired'] === true ?
            true :
            false;

        $this->addressesParams['streetAddressFieldRequired'] =
            isset($this->params['streetAddressFieldRequired']) &&
                $this->params['streetAddressFieldRequired'] === false ?
            false :
            true;

        $this->addressesParams['streetAddress2FieldRequired'] =
            isset($this->params['streetAddress2FieldRequired']) &&
                $this->params['streetAddress2FieldRequired'] === true ?
            true :
            false;

        $this->addressesParams['cityFieldRequired'] =
            isset($this->params['cityFieldRequired']) &&
                $this->params['cityFieldRequired'] === false ?
            false :
            true;

        $this->addressesParams['postCodeFieldRequired'] =
            isset($this->params['postCodeFieldRequired']) &&
                $this->params['postCodeFieldRequired'] === false ?
            false :
            true;

        $this->addressesParams['stateFieldRequired'] =
            isset($this->params['stateFieldRequired']) &&
                $this->params['stateFieldRequired'] === false ?
            false :
            true;

        $this->addressesParams['countryFieldRequired'] =
            isset($this->params['countryFieldRequired']) &&
                $this->params['countryFieldRequired'] === false ?
            false :
            true;

        $this->addressesParams['attentionToFieldBazPostOnCreate'] =
            isset($this->params['attentionToFieldBazPostOnCreate']) &&
                $this->params['attentionToFieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['attentionToFieldBazPostOnUpdate'] =
            isset($this->params['attentionToFieldBazPostOnUpdate']) &&
                $this->params['attentionToFieldBazPostOnUpdate'] === false ?
            false :
            true;

        $this->addressesParams['streetAddressFieldBazPostOnCreate'] =
            isset($this->params['streetAddressFieldBazPostOnCreate']) &&
                $this->params['streetAddressFieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['streetAddressFieldBazPostOnUpdate'] =
            isset($this->params['streetAddressFieldBazPostOnUpdate']) &&
                $this->params['streetAddressFieldBazPostOnUpdate'] === false ?
            false :
            true;

        $this->addressesParams['streetAddress2FieldBazPostOnCreate'] =
            isset($this->params['streetAddress2FieldBazPostOnCreate']) &&
                $this->params['streetAddress2FieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['streetAddress2FieldBazPostOnUpdate'] =
            isset($this->params['streetAddress2FieldBazPostOnUpdate']) &&
                $this->params['streetAddress2FieldBazPostOnUpdate'] === false ?
            false :
            true;

        $this->addressesParams['cityFieldBazPostOnCreate'] =
            isset($this->params['cityFieldBazPostOnCreate']) &&
                $this->params['cityFieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['cityFieldBazPostOnUpdate'] =
            isset($this->params['cityFieldBazPostOnUpdate']) &&
                $this->params['cityFieldBazPostOnUpdate'] === false ?
            false :
            true;

        $this->addressesParams['postCodeFieldBazPostOnCreate'] =
            isset($this->params['postCodeFieldBazPostOnCreate']) &&
                $this->params['postCodeFieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['postCodeFieldBazPostOnUpdate'] =
            isset($this->params['postCodeFieldBazPostOnUpdate']) &&
                $this->params['postCodeFieldBazPostOnUpdate'] === false ?
            false :
            true;

        $this->addressesParams['stateFieldBazPostOnCreate'] =
            isset($this->params['stateFieldBazPostOnCreate']) &&
                $this->params['stateFieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['stateFieldBazPostOnUpdate'] =
            isset($this->params['stateFieldBazPostOnUpdate']) &&
                $this->params['stateFieldBazPostOnUpdate'] === false ?
            false :
            true;

        $this->addressesParams['countryFieldBazPostOnCreate'] =
            isset($this->params['countryFieldBazPostOnCreate']) &&
                $this->params['countryFieldBazPostOnCreate'] === false ?
            false :
            true;

        $this->addressesParams['countryFieldBazPostOnUpdate'] =
            isset($this->params['countryFieldBazPostOnUpdate']) &&
                $this->params['countryFieldBazPostOnUpdate'] === false ?
            false :
            true;
    }

    protected function buildSingleAddressLayout()
    {
        if (isset($this->params['includeAttentionTo']) && $this->params['includeAttentionTo'] === true) {
            $this->content .= $this->inclAttentionTo();
        }

        if (isset($this->params['includeStreet']) && $this->params['includeStreet'] === true) {
            $this->content .= $this->inclStreet();
        }

        if (isset($this->params['searchType'])) {
            if ($this->params['searchType'] === 'city') {

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

            } else if ($this->params['searchType'] === 'state') {

                $this->content .=
                    '<div class="row">' . $this->inclState() . $this->inclCountry() . '</div>';

                $this->content .=
                    $this->inclBaseJs() . $this->inclStateJs();

                $this->content .=
                    '});</script>';

            } else if ($this->params['searchType'] === 'country') {

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
                            'fieldValue'                            => $this->params['attentionTo']
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
                '"' . $this->compSecId . '-attention_to"        : { },
                "' . $this->compSecId . '-street_address"       : { },
                "' . $this->compSecId . '-street_address_2"     : { },';
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
                                    if (response.cities) {
                                        return response.cities;
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
            '"' . $this->compSecId . '-post_code"        : {
                afterInit   : function() {
                    dataCollectionSection["' . $this->compSecId . '-form"]["autoCompleteCities"] =
                        new autoComplete({
                            data: {
                                src: async() => {
                                    const url = "' . $this->links->url("system/geo/cities/searchPostCode") . '";

                                    var myHeaders = new Headers();
                                    myHeaders.append("accept", "application/json");

                                    var formdata = new FormData();
                                    formdata.append("search", document.querySelector("#' . $this->compSecId . '-post_code").value);
                                    formdata.append($("#security-token").attr("name"), $("#security-token").val());

                                    var requestOptions = {
                                        method: "POST",
                                        headers: myHeaders,
                                        body: formdata
                                    };

                                    const responseData = await fetch(url, requestOptions);

                                    const response = await responseData.json();

                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("postcode", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }
                                    if (response.postCodes) {
                                        return response.postCodes;
                                    } else {
                                        return [];
                                    }
                                },
                                key: ["postcode"],
                                cache: false
                            },
                            selector: "#' . $this->compSecId . '-post_code",
                            threshold : 4,
                            debounce: 500,
                            searchEngine: "strict",
                            resultsList: {
                                render: true,
                                container: source => {
                                    source.setAttribute("id", "' . $this->compSecId . '-post_code_list");
                                    source.setAttribute("class", "autoComplete_results");
                                },
                                destination: "#' . $this->compSecId . '-post_code",
                                position: "afterend",
                                element: "div",
                                className: "autoComplete_results"
                            },
                            maxResults: 5,
                            highlight: true,
                            resultItem: {
                                content: (data, source) => {
                                    var cityName = "";

                                    if (data.value.name) {
                                        cityName = data.value.name;
                                    }

                                    source.innerHTML = data.match + " <span>(City: " + cityName + ", State: " + data.value.state_name + ", Country: " + data.value.country_name + ")</span>";
                                },
                                element: "div"
                            },
                            noResults: () => {
                                const result = document.createElement("li");
                                result.setAttribute("class", "autoComplete_result text-danger");
                                result.setAttribute("tabindex", "1");
                                result.innerHTML = "No search results. Click field help for more information.";

                                if (document.querySelector("#' . $this->compSecId . '-post_code_list")) {
                                    $("#' . $this->compSecId . '-post_code_list").empty().append(result);
                                } else {
                                    $("#' . $this->compSecId . '-post_code").parent(".form-group").append(
                                        \'<div id="' . $this->compSecId . '-post_code_list" class="autoComplete_results"></div>\'
                                    );
                                    document.querySelector("#' . $this->compSecId . '-post_code_list").appendChild(result);
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
                    $("#' . $this->compSecId . '-post_code").on("input propertychange", function() {
                        $("#' . $this->compSecId . '-city_id").val(0);
                        $("#' . $this->compSecId . '-city_id").attr("value", 0);
                        $("#' . $this->compSecId . '-state_id").val(0);
                        $("#' . $this->compSecId . '-state_id").attr("value", 0);
                        $("#' . $this->compSecId . '-state_name").val("");
                        $("#' . $this->compSecId . '-state_name").attr("value", "");
                        $("#' . $this->compSecId . '-country_id").val(0);
                        $("#' . $this->compSecId . '-country_id").attr("value", 0);
                        $("#' . $this->compSecId . '-country_name").val("");
                        $("#' . $this->compSecId . '-country_name").attr("value", "");
                    });

                    $("#' . $this->compSecId . '-post_code").focusout(function() {
                        $("#' . $this->compSecId . '-post_code_list").children("li").remove();
                    });
                }
            },';
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
                                    if (response.states) {
                                        return response.states;
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

                                    if (response.countries) {
                                        return response.countries;
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
