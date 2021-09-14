<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Model\SystemApiXeroAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Model\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model\SystemApiXeroHistory;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestRequest;
use Phalcon\Helper\Json;
use Apps\Dash\Packages\Crms\Customers\Customers;
use System\Base\BasePackage;

class SyncCustomers extends BasePackage
{
    protected $customersPackage;

    public $customer;

    protected $api;

    protected $xeroApi;

    public function setup($api, $xeroApi)
    {
        $this->api = $api;

        $this->xeroApi = $xeroApi;

        $this->customersPackage = $this->usePackage(Customers::class);

        $this->customer = [];
        $this->customer['errors'] = [];
        $this->customer['errors']['customers'] = [];
        $this->customer['errors']['address'] = [];

        return $this;
    }

    public function generateCustomerData(array $contact)
    {
        $customer = [];
        $customer['id'] = 0;

        if (isset($contact['EmailAddress']) && $contact['EmailAddress'] !== '') {
            $customer['account_email'] = $contact['EmailAddress'];
        } else {
            $customer['ContactID'] = $contact['ContactID'];

            $this->customer['customer'] = $customer;

            return $this->customer;
        }

        $customer['first_name'] = $contact['Name'];
        $customer['last_name'] = $contact['Name'];
        $customer['full_name'] = $contact['Name'];
        $customer['contact_mobile'] = '0';

        $customer['customer_group_id'] = '0';

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
                        if ($xeroContactGroup->baz_customer_group_id) {
                            $customer['customer_group_id'] = $xeroContactGroup->baz_customer_group_id;
                            break;
                        }
                    }
                }
            }
        }

        $geo = $this->getAddressIds($contact);

        if (isset($geo['currency'])) {
            $customer['currency'] = $geo['currency'];
        }

        $customer['address_ids'] = Json::encode($geo['address_ids']);

        $customer = $this->customersPackage->updateAddresses($customer);

        if ($contact['phones'] && count($contact['phones']) > 0) {
            $customer['contact_other'] = '';

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
                    $customer['contact_phone'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'DDI') {
                    if ($phoneStr !== '') {
                        $customer['contact_other'] .= 'Direct: ' . $phoneStr . ' ';
                    }
                } else if ($phone['PhoneType'] === 'FAX') {
                    $customer['contact_fax'] = $phoneStr;
                } else if ($phone['PhoneType'] === 'MOBILE') {
                    if ($phoneStr !== '') {
                        $customer['contact_other'] .= 'Mobile: ' . $phoneStr . ' ';
                    }
                }
            }

            $customer['contact_other'] = trim($customer['contact_other']);
        }

        if (!isset($customer['contact_phone']) ||$customer['contact_phone'] === '') {
            $customer['contact_phone'] = '0';
        }

        $checkCustomer = $this->customersPackage->checkCustomerDuplicate($customer['account_email']);

        if ($contact['baz_customer_id'] && $contact['baz_customer_id'] != '0' || $checkCustomer) {
            if ($checkCustomer) {
                $customer['id'] = $checkCustomer->toArray()['id'];
            } else {
                $customer['id'] = $contact['baz_customer_id'];
            }

            if ($this->customersPackage->update($customer)) {
                $customer = $this->customersPackage->packagesData->last;

                $customerFinancials = $this->generateCustomerFinancialsData($contact, $customer);

                $this->customersPackage->updateFinancialDetails($customerFinancials);
            } else {

                $this->customer['errors']['customers'] = array_merge($this->customer['errors']['customers'], ['Could not update customer data - ' . $contact['Name']]);
            }
        } else {
            if ($this->customersPackage->add($customer)) {
                $customer = $this->customersPackage->packagesData->last;

                $customerFinancials = $this->generateCustomerFinancialsData($contact, $customer);

                $this->customersPackage->addFinancialDetails($customerFinancials);
            } else {

                $this->customer['errors']['customers'] = array_merge($this->customer['errors']['customers'], ['Could not add customer data - ' . $contact['Name']]);
            }
        }

        if ($customer['contact_phone'] === '0') {
            $this->customer['errors']['customers'] = array_merge($this->customer['errors']['customers'], ['Phone missing for customer - ' . $contact['Name']]);
        }

        if ($contact['HasAttachments'] == '1') {
            $this->addContactAttachments($contact, $customer);
        }

        $this->addContactHistory($contact, $customer);

        $contact['baz_customer_id'] = $customer['id'];
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

        $customer['ContactID'] = $contact['ContactID'];

        $this->customer['customer'] = $customer;

        return $this->customer;
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

                            $this->customer['errors']['address'] = array_merge($this->customer['errors']['address'], ['Could not add country data.']);

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

                            $this->customer['errors']['address'] = array_merge($this->customer['errors']['address'], ['Could not add state data.']);

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

                        $this->customer['errors']['address'] = array_merge($this->customer['errors']['address'], ['Could not add city data.']);

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

    protected function generateCustomerFinancialsData($contact, $customer)
    {
        $customerFinancials['id'] = $customer['id'];

        $customerFinancials['customer_id'] = $customer['id'];

        if ($contact['finance']['TaxNumber']) {
            $customerFinancials['abn'] = str_replace(' ', '', $contact['finance']['TaxNumber']);
        } else {
            $customerFinancials['abn'] = '00000000000';
        }

        if ($contact['finance']['DefaultCurrency']) {
            $customerFinancials['currency'] = $contact['finance']['DefaultCurrency'];
        } else if (isset($customer['currency'])) {
            $customerFinancials['currency'] = $customer['currency'];
        } else {
            $customerFinancials['currency'] = '0';
        }

        if ($contact['finance']['BankAccountDetails']) {
            $customerFinancials['account_number'] = $contact['finance']['BankAccountDetails'];
        }

        if ($contact['finance']['PaymentTermsSalesDay']) {
            $customerFinancials['invoices_due_date'] = $contact['finance']['PaymentTermsSalesDay'];
        }
        if ($contact['finance']['PaymentTermsSalesType']) {
            $customerFinancials['invoices_due_date_term'] = $contact['finance']['PaymentTermsSalesType'];
        }

        if ($contact['finance']['Discount']) {
            $customerFinancials['invoices_discount'] = $contact['finance']['Discount'];
        }

        return $customerFinancials;
    }

    protected function addContactAttachments($contact, $customer)
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
                            $storageId = $this->addAttachmentToStorage($attachment, $contact, $customer, $response);

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

    protected function addAttachmentToStorage($attachment, $contact, $customer, $response)
    {
        if ($this->basepackages->storages->storeFile(
                'private',
                'customers',
                $response->getBody()->getContents(),
                $attachment['FileName'],
                $attachment['ContentLength'],
                $attachment['MimeType']
            )
        ) {
            $newNote['package_row_id'] = $customer['id'];
            $newNote['note_type'] = '1';
            $newNote['note_app_visibility']['data'] = [];
            $newNote['is_private'] = '0';
            $newNote['note'] = 'Added via Xero API.';
            $newNote['note_attachments'][] = $this->basepackages->storages->packagesData->storageData['uuid'];

            $this->basepackages->notes->addNote('customers', $newNote);

            return $this->basepackages->storages->packagesData->storageData;
        }

        return false;
    }

    protected function addContactHistory($contact, $customer)
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

                    $note = $this->addHistoryToNote($history, $customer);

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

    protected function addHistoryToNote($history, $customer)
    {
        $newNote['package_row_id'] = $customer['id'];
        $newNote['note_type'] = '1';
        $newNote['note_app_visibility']['data'] = [];
        $newNote['is_private'] = '0';
        $newNote['note'] =
            'Added via Xero API.' .
            '<br>Change Type: ' . $history['Changes'] .
            '<br>Created At: ' . \DateTime::createFromFormat('Y-m-d\TH:i:s', $history['DateUTCString'])->format('Y-m-d H:i:s') .
            '<br>Details: ' . $history['Details'];

        $this->basepackages->notes->addNote('customers', $newNote);

        if ($this->basepackages->notes->packagesData->last) {
            return $this->basepackages->notes->packagesData->last;
        }

        return false;
    }
}