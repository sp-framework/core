<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Model\SystemApiXeroAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContactsContactPersons;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model\SystemApiXeroHistory;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestRequest;
use Phalcon\Helper\Json;
use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use System\Base\BasePackage;

class SyncVendors extends BasePackage
{
    protected $vendorsPackage;

    protected $contactsPackage;

    public $vendor;

    protected $api;

    protected $xeroApi;

    public function setup($api, $xeroApi)
    {
        $this->api = $api;

        $this->xeroApi = $xeroApi;

        $this->vendorsPackage = $this->usePackage(Vendors::class);

        $this->contactsPackage = $this->usePackage(Contacts::class);

        $this->vendor = [];
        $this->vendor['errors'] = [];
        $this->vendor['errors']['vendors'] = [];
        $this->vendor['errors']['address'] = [];

        return $this;
    }

    public function generateVendorData(array $contact)
    {
        $vendor = [];
        $vendor['id'] = 0;

        if ($contact['IsCustomer'] == '1') {
            $vendor['is_b2b_customer'] = '1';
        }

        if ($contact['finance']['TaxNumber']) {
            $vendor['abn'] = preg_replace('/[^0-9]/', '', $contact['finance']['TaxNumber']);
        } else {
            $vendor['abn'] = '00000000000';
        }

        $vendor['business_name'] = $contact['Name'];

        $vendor['vendor_group_id'] = '0';

        if ($contact['ContactGroups'] && $contact['ContactGroups'] !== '') {
            $contact['ContactGroups'] = Json::decode($contact['ContactGroups'], true);

            $model = SystemApiXeroContactGroups::class;

            foreach ($contact['ContactGroups'] as $groupKey => $group) {
                if ($group['Status'] === 'ACTIVE') {
                    $xeroContactGroup = $model::findFirst(
                        [
                            'conditions'    => 'ContactGroupID = :cgid:',
                            'bind'          =>
                                [
                                    'cgid'  => $group['ContactGroupID']
                                ]
                        ]
                    );

                    if ($xeroContactGroup) {
                        if ($xeroContactGroup->baz_vendor_group_id) {
                            $vendor['vendor_group_id'] = $xeroContactGroup->baz_vendor_group_id;
                            break;
                        }
                    }
                }
            }
        }

        $vendor['is_supplier'] = '1';

        $geo = $this->getAddressIds($contact);

        if (isset($geo['currency'])) {
            $vendor['currency'] = $geo['currency'];
        }

        $vendor['address_ids'] = Json::encode($geo['address_ids']);

        $vendor = $this->vendorsPackage->updateAddresses($vendor);

        if ($contact['EmailAddress']) {
            $vendor['email'] = $contact['EmailAddress'];
        } else {
            $vendor['email'] = 'missing';
        }

        $vendor['website'] = $contact['Website'];

        if ($contact['phones'] && count($contact['phones']) > 0) {
            $vendor['contact_other'] = '';

            foreach ($contact['phones'] as $phoneKey => $phone) {
                $phoneStr = '';

                if ($phone['PhoneCountryCode']) {
                    $phoneStr .= $phone['PhoneCountryCode'] . '-';
                }
                if ($phone['PhoneAreaCode']) {
                    $phoneStr .= $phone['PhoneAreaCode'] . '-';
                }
                if ($phone['PhoneNumber']) {
                    $phoneStr .= $phone['PhoneNumber'];
                }

                if ($phone['PhoneType'] === 'DEFAULT') {
                    $vendor['contact_phone'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'DDI') {
                    if ($phoneStr !== '') {
                        $vendor['contact_other'] .= 'Direct: ' . $phoneStr . ' ';
                    }
                } else if ($phone['PhoneType'] === 'FAX') {
                    $vendor['contact_fax'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'MOBILE') {
                    if ($phoneStr !== '') {
                        $vendor['contact_other'] .= 'Mobile: ' . $phoneStr . ' ';
                    }
                }
            }

            $vendor['contact_other'] = trim($vendor['contact_other']);
        }

        if (!isset($vendor['contact_phone']) ||$vendor['contact_phone'] === '') {
            $vendor['contact_phone'] = '0';
        }

        $checkVendor = $this->vendorsPackage->checkVendorDuplicate($vendor['business_name']);

        if ($contact['baz_vendor_id'] && $contact['baz_vendor_id'] != '0' || $checkVendor) {
            if ($checkVendor) {
                $vendor['id'] = $checkVendor->toArray()['id'];
            } else {
                $vendor['id'] = $contact['baz_vendor_id'];
            }

            if ($this->vendorsPackage->update($vendor)) {
                $vendor = $this->vendorsPackage->packagesData->last;

                $vendorFinancials = $this->generateVendorFinancialsData($contact, $vendor);

                $vendorFinancials['id'] = $vendor['id'];

                $this->vendorsPackage->updateFinancialDetails($vendorFinancials);
            } else {

                $this->vendor['errors']['vendors'] = array_merge($this->vendor['errors']['vendors'], ['Could not update vendor data - ' . $vendor['business_name']]);
            }
        } else {
            if ($this->vendorsPackage->add($vendor)) {
                $vendor = $this->vendorsPackage->packagesData->last;

                $vendorFinancials = $this->generateVendorFinancialsData($contact, $vendor);

                $this->vendorsPackage->addFinancialDetails($vendorFinancials);
            } else {

                $this->vendor['errors']['vendors'] = array_merge($this->vendor['errors']['vendors'], ['Could not add vendor data - ' . $vendor['business_name']]);
            }
        }

        if ($vendor['email'] === 'missing') {
            $this->vendor['errors']['vendors'] = array_merge($this->vendor['errors']['vendors'], ['Email missing for vendor - ' . $vendor['business_name']]);
        }
        if ($vendor['abn'] === '00000000000') {
            $this->vendor['errors']['vendors'] = array_merge($this->vendor['errors']['vendors'], ['ABN missing for vendor - ' . $vendor['business_name']]);
        }
        if ($vendor['contact_phone'] === '0') {
            $this->vendor['errors']['vendors'] = array_merge($this->vendor['errors']['vendors'], ['Phone missing for vendor - ' . $vendor['business_name']]);
        }

        if ($contact['persons'] && count($contact['persons']) > 0) {
            $this->generateVendorContacts($contact, $vendor['id']);
        }

        if ($contact['HasAttachments'] == '1') {
            $this->addContactAttachments($contact, $vendor);
        }

        $this->addContactHistory($contact, $vendor);

        $contact['baz_vendor_id'] = $vendor['id'];
        $contact['resync_local'] = null;

        $model = SystemApiXeroContacts::class;

        $xeroContact = $model::findFirst(
            [
                'conditions'    => 'ContactID = :cid:',
                'bind'          =>
                    [
                        'cid'   => $contact['ContactID']
                    ]
            ]
        );

        $xeroContact->assign($this->jsonData($contact));

        $xeroContact->update();

        $vendor['ContactID'] = $contact['ContactID'];

        $this->vendor['vendor'] = $vendor;

        return $this->vendor;
    }

    protected function getAddressIds(array $contact)
    {
        $geo['currency'] = '0';
        $geo['address_ids'] = [];
        $geo['address_ids']['1'] = [];
        $geo['address_ids']['2'] = [];

        if (count($contact['addresses']) > 0) {
            foreach ($contact['addresses'] as $addressKey => $address) {
                if (!$address['City'] || $address['City'] === '' ||
                    !$address['Region'] || $address['Region'] === '' ||
                    !$address['Country'] || $address['Country'] === ''
                ) {
                    continue;
                }

                $found = false;

                //Xero Uses an address API for address verification, so the address received from Xero is accurate.
                //If we do not have matching data in our system, we create new GeoLocation data.
                if ($this->basepackages->geoCities->searchCities($address['City'])) {
                    $cityData = $this->basepackages->geoCities->packagesData->cities;

                    if (count($cityData) > 0) {
                        foreach ($cityData as $cityKey => $city) {
                            if (strtolower($city['name']) === strtolower($address['City'])) {
                                $found = true;

                                $newAddress['city_id'] = $city['id'];
                                $newAddress['city_name'] = $city['name'];
                                $newAddress['state_id'] = $city['state_id'];
                                $newAddress['state_name'] = $city['state_name'];
                                $newAddress['country_id'] = $city['country_id'];
                                $newAddress['country_name'] = $city['country_name'];
                            }

                            if ($found) {
                                break;
                            }
                        }
                    }
                }

                if (!$found) {
                    //Country
                    $foundCountry = null;

                    if ($this->basepackages->geoCountries->searchCountries($address['Country'], true)) {
                        $countryData = $this->basepackages->geoCountries->packagesData->countries;

                        if (count($countryData) > 0) {
                            foreach ($countryData as $countryKey => $country) {
                                if (strtolower($country['name']) === strtolower($address['Country'])) {
                                    $foundCountry = $country;
                                    $geo['currency'] = $country['currency'];
                                    break;
                                }
                            }
                        }
                    }

                    if (!$foundCountry) {
                        $newCountry['name'] = $address['Country'];
                        $newCountry['installed'] = '1';
                        $newCountry['enabled'] = '1';
                        $newCountry['user_added'] = '1';

                        if ($this->basepackages->geoCountries->add($newCountry)) {
                            $newAddress['country_id'] = $this->basepackages->geoCountries->packagesData->last['id'];
                            $newAddress['country_name'] = $newCountry['name'];
                        } else {

                            $this->vendor['errors']['address'] = array_merge($this->vendor['errors']['address'], ['Could not add country data.']);

                            continue;
                        }
                    } else {
                        //We check if country is installed or not, if not, we install and enable it
                        if ($foundCountry['installed'] != '1') {
                            $foundCountry['enabled'] = '1';

                            $this->basepackages->geoCountries->installCountry($foundCountry);
                        } else if ($foundCountry['enabled'] != '1') {
                            $foundCountry['enabled'] = '1';

                            $this->basepackages->geoCountries->update($foundCountry);
                        }

                        $newAddress['country_id'] = $foundCountry['id'];
                        $newAddress['country_name'] = $foundCountry['name'];
                    }

                    //State (Region in Xero Address)
                    $foundState = null;

                    if ($this->basepackages->geoStates->searchStatesByCode($address['Region'], true)) {
                        $stateData = $this->basepackages->geoStates->packagesData->states;

                        if (count($stateData) > 0) {
                            foreach ($stateData as $stateKey => $state) {
                                if (strtolower($state['state_code']) === strtolower($address['Region'])) {
                                    $foundState = $state;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$foundState) {
                        $newState['name'] = $address['Region'];
                        $newState['state_code'] = substr($address['Region'], 0, 3);
                        $newState['user_added'] = '1';
                        $newState['country_id'] = $newAddress['country_id'];

                        if ($this->basepackages->geoStates->add($newState)) {
                            $newAddress['state_id'] = $this->basepackages->geoStates->packagesData->last['id'];
                            $newAddress['state_name'] = $newState['name'];
                        } else {

                            $this->vendor['errors']['address'] = array_merge($this->vendor['errors']['address'], ['Could not add state data.']);

                            continue;
                        }
                    } else {
                        $newAddress['state_id'] = $foundState['id'];
                        $newAddress['state_name'] = $foundState['name'];
                    }

                    //New City
                    $newCity['name'] = $address['City'];
                    $newCity['state_id'] = $newAddress['state_id'];
                    $newCity['country_id'] = $newAddress['country_id'];
                    $newCity['user_added'] = '1';

                    if ($this->basepackages->geoCities->add($newCity)) {
                        $newAddress['city_id'] = $this->basepackages->geoCities->packagesData->last['id'];
                        $newAddress['city_name'] = $newCity['name'];
                    } else {

                        $this->vendor['errors']['address'] = array_merge($this->vendor['errors']['address'], ['Could not add city data.']);

                        continue;
                    }
                }

                $newAddress['seq'] = 0;
                $newAddress['new'] = 1;
                $newAddress['attention_to'] = $address['AttentionTo'];
                $newAddress['street_address'] = $address['AddressLine1'];
                $newAddress['street_address_2'] = $address['AddressLine2'];

                if ($address['AddressType'] === 'POBOX') {
                    array_push($geo['address_ids']['2'], $newAddress);
                } else if ($address['AddressType'] === 'DELIVERY' ||
                           $address['AddressType'] === 'STREET'
                ) {
                    array_push($geo['address_ids']['1'], $newAddress);
                }
            }
        }

        return $geo;
    }

    protected function generateVendorFinancialsData($contact, $vendor)
    {
        $vendorFinancials['vendor_id'] = $vendor['id'];
        $vendorFinancials['acn'] = substr($vendor['abn'], 2);

        if ($contact['finance']['DefaultCurrency']) {
            $vendorFinancials['currency'] = $contact['finance']['DefaultCurrency'];
        } else if (isset($vendor['currency'])) {
            $vendorFinancials['currency'] = $vendor['currency'];
        } else {
            $vendorFinancials['currency'] = '0';
        }

        if ($contact['finance']['BankAccountDetails']) {
            $vendorFinancials['account_number'] = $contact['finance']['BankAccountDetails'];
        }

        if ($contact['finance']['PaymentTermsBillsDay']) {
            $vendorFinancials['bills_due_date'] = $contact['finance']['PaymentTermsBillsDay'];
        }
        if ($contact['finance']['PaymentTermsBillsType']) {
            $vendorFinancials['bills_due_date_term'] = $contact['finance']['PaymentTermsBillsType'];
        }

        if ($contact['finance']['Discount']) {
            $vendorFinancials['bills_discount'] = $contact['finance']['Discount'];
        }

        return $vendorFinancials;
    }

    protected function generateVendorContacts($contact, $vendorId)
    {
        foreach ($contact['persons'] as $personKey => $person) {
            if ((isset($person['EmailAddress']) && $person['EmailAddress'] !== '') &&
                (isset($person['FirstName']) && $person['FirstName'] !== '') &&
                (isset($person['LastName']) && $person['LastName'] !== '')
            ) {
                $newContact = [];
                $newContact['account_email'] = $person['EmailAddress'];
                $newContact['vendor_id'] = $vendorId;
                $newContact['first_name'] = $person['FirstName'];
                $newContact['last_name'] = $person['LastName'];
                $newContact['full_name'] = $newContact['first_name'] . ' ' . $newContact['last_name'];
                $newContact['contact_phone'] = '0';
                $newContact['contact_mobile'] = '0';

                $checkContact = $this->contactsPackage->checkContactDuplicate($newContact['account_email']);
                if ($person['baz_contact_id'] && $person['baz_contact_id'] != '0' || $checkContact) {
                    if ($checkContact) {
                        $newContact['id'] = $checkContact->toArray()['id'];
                    } else {
                        $newContact['id'] = $person['baz_contact_id'];
                    }

                    if (!$this->contactsPackage->update($newContact)) {
                        $person['baz_contact_id'] = $this->contactsPackage->packagesData->last['id'];

                        $this->vendor['errors']['address'] = array_merge($this->vendor['errors']['address'], ['Could not update contact data.']);
                    }
                } else {
                    if ($this->contactsPackage->add($newContact)) {
                        $person['baz_contact_id'] = $this->contactsPackage->packagesData->last['id'];
                        $this->contactsPackage->errorContact('Phone and Mobile missing for contact - ' . $newContact['account_email']);
                    } else {

                        $this->vendor['errors']['address'] = array_merge($this->vendor['errors']['address'], ['Could not add contact data.']);
                    }
                }

                if (isset($person['baz_contact_id'])) {
                    $model = SystemApiXeroContactsContactPersons::class;

                    $xeroPerson = $model::findFirst(
                        [
                            'conditions'    => 'ContactID = :cid:',
                            'bind'          =>
                                [
                                    'cid'   => $contact['ContactID']
                                ]
                        ]
                    );

                    $xeroPerson->assign($person);

                    $xeroPerson->update();
                }
            }
        }
    }

    protected function addContactAttachments($contact, $vendor)
    {
        $model = SystemApiXeroAttachments::class;

        $xeroAttachment = $model::find(
            [
                'conditions'    => 'baz_storage_local_id IS NULL AND xero_package = :xp: AND xero_package_row_id = :xpri:',
                'bind'          =>
                    [
                        'xp'    => 'contacts',
                        'xpri'  => $contact['ContactID']
                    ]
            ]
        );

        if ($xeroAttachment) {
            $attachments = $xeroAttachment->toArray();

            if (count($attachments) > 0) {

                $request = new GetContactAttachmentByIdRestRequest;

                foreach ($attachments as $attachmentKey => $attachment) {
                    $request->ContactID = $attachment['xero_package_row_id'];

                    $request->AttachmentID = $attachment['AttachmentID'];

                    $response = $this->xeroApi->getContactAttachmentById($request);

                    if ($response) {
                        $this->api->refreshXeroCallStats($response->getHeaders());

                        if ($response->getStatusCode() === 200) {
                            $storageId = $this->addAttachmentToStorage($attachment, $contact, $vendor, $response);

                            if ($storageId) {
                                $xA = $model::findFirst(
                                    [
                                        'conditions'    => 'AttachmentID = :aid:',
                                        'bind'          =>
                                            [
                                                'aid'   => $attachment['AttachmentID']
                                            ]
                                    ]
                                );

                                if ($xA) {
                                    $xA->baz_storage_local_id = $storageId['id'];

                                    $xA->update();

                                    $this->basepackages->storages->changeOrphanStatus($storageId['uuid'], null, false, 0);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function addAttachmentToStorage($attachment, $contact, $vendor, $response)
    {
        if ($this->basepackages->storages->storeFile(
                'private',
                'contacts',
                $response->getBody()->getContents(),
                $attachment['FileName'],
                $attachment['ContentLength'],
                $attachment['MimeType']
            )
        ) {
            $newNote['package_row_id'] = $vendor['id'];
            $newNote['note_type'] = '1';
            $newNote['note_app_visibility']['data'] = [];
            $newNote['is_private'] = '0';
            $newNote['note'] = 'Added via Xero API.';
            $newNote['note_attachments'][] = $this->basepackages->storages->packagesData->storageData['uuid'];

            $this->basepackages->notes->addNote('vendors', $newNote);

            return $this->basepackages->storages->packagesData->storageData;
        }

        return false;
    }

    protected function addContactHistory($contact, $vendor)
    {
        $model = SystemApiXeroHistory::class;

        $xeroHistory = $model::find(
            [
                'conditions'    => 'baz_note_id IS NULL AND xero_package = :xp: AND xero_package_row_id = :xpri:',
                'bind'          =>
                    [
                        'xp'    => 'contacts',
                        'xpri'  => $contact['ContactID']
                    ]
            ]
        );

        if ($xeroHistory) {
            $histories = $xeroHistory->toArray();

            if (count($histories) > 0) {
                foreach ($histories as $historyKey => $history) {

                    $note = $this->addHistoryToNote($history, $vendor);

                    if ($note) {
                        $xH = $model::findFirstById($history['id']);

                        if ($xH) {
                            $xH->baz_note_id = $note['id'];

                            $xH->update();
                        }
                    }
                }
            }
        }
    }

    protected function addHistoryToNote($history, $vendor)
    {
        $newNote['package_row_id'] = $vendor['id'];
        $newNote['note_type'] = '1';
        $newNote['note_app_visibility']['data'] = [];
        $newNote['is_private'] = '0';
        if (isset($history['User'])) {
            $historyUser = '<br>Created By: ' . $history['User'];
        } else {
            $historyUser = '';
        }

        if (isset($history['Details']) && $history['Details'] !== '') {
            $historyDetails = '<br>Details: ' . $history['Details'];
        } else {
            $historyDetails = '<br>Details: -';
        }

        $newNote['note'] =
            'Added via Xero API.' .
            '<br>Change Type: ' . $history['Changes'] .
            '<br>Created At: ' . \DateTime::createFromFormat('Y-m-d\TH:i:s', $history['DateUTCString'])->format('Y-m-d H:i:s') .
            $historyUser .
            $historyDetails;

        $this->basepackages->notes->addNote('vendors', $newNote);

        if ($this->basepackages->notes->packagesData->last) {
            return $this->basepackages->notes->packagesData->last;
        }

        return false;
    }
}