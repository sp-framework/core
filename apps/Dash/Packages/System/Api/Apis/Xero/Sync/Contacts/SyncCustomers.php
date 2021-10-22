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
            $customer['account_email'] = 'no-reply@' . $this->domains->domains[0]['name'];
        }

        $contactName = explode(' ', trim($contact['Name']));

        $customer['first_name'] = $contactName[0];
        if (count($contactName) !== 1) {
            unset($contactName[0]);
        }

        preg_match_all('/[0].*/', $contact['Name'], $phoneInName);

        if (isset($phoneInName[0]) && count($phoneInName[0]) > 0) {
            if (isset($phoneInName[0][0])) {
                $phoneInName = $phoneInName[0][0];

                $customer['last_name'] =
                    trim(str_replace($customer['first_name'], '', str_replace($phoneInName, '', $contact['Name'])));

                $phoneInName = str_replace(' ', '', $phoneInName);

                $customer['contact_mobile'] = $phoneInName;
            } else {
                $customer['contact_mobile'] = '0';

                $customer['last_name'] = implode(' ', $contactName);
            }
        } else {
            $customer['contact_mobile'] = '0';

            $customer['last_name'] = implode(' ', $contactName);
        }

        if ($customer['last_name'] === '') {
            $customer['last_name'] = $customer['first_name'];
        }

        $customer['full_name'] = trim($customer['first_name'] . ' ' . $customer['last_name']);
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
                    $phoneStr .= $phone['PhoneCountryCode'];
                }
                if ($phone['PhoneAreaCode']) {
                    $phoneStr .= $phone['PhoneAreaCode'];
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
                    if ($phoneStr !== '') {
                        $customer['contact_fax'] = $phoneStr;
                    }
                } else if ($phone['PhoneType'] === 'MOBILE') {
                    if ($phoneStr !== '') {
                        $customer['contact_other'] .= 'Mobile: ' . $phoneStr . ' ';
                    }
                }
            }

            $customer['contact_other'] = trim($customer['contact_other']);
        }

        if (!isset($customer['contact_phone']) || $customer['contact_phone'] === '') {
            $customer['contact_phone'] = '0';
        }

        $checkCustomer = $this->customersPackage->checkCustomerDuplicate(null, $customer['contact_mobile']);

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

        if ($customer['account_email'] === 'no-reply@' . $this->domains->domains[0]['name']) {
            $this->customer['errors']['customers'] = array_merge($this->customer['errors']['customers'], ['Email missing for customer - ' . $contact['Name']]);
        }
        if ($customer['contact_mobile'] === '0') {
            $this->customer['errors']['customers'] = array_merge($this->customer['errors']['customers'], ['Mobile missing for customer - ' . $contact['Name']]);
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

                $newAddress['seq'] = 0;
                $newAddress['new'] = 1;
                $newAddress['attention_to'] = $address['AttentionTo'];
                $newAddress['street_address'] = $address['AddressLine1'];
                $newAddress['street_address_2'] = $address['AddressLine2'];
                $newAddress['city_id'] = '0';
                $newAddress['city_name'] = $address['City'];
                $newAddress['state_id'] = '0';
                $newAddress['state_name'] = $address['Region'];
                $newAddress['country_id'] = '0';
                $newAddress['country_name'] = $address['Country'];

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
        if (isset($history['User']) && $history['User'] !== '') {
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

        $this->basepackages->notes->addNote('customers', $newNote);

        if ($this->basepackages->notes->packagesData->last) {
            return $this->basepackages->notes->packagesData->last;
        }

        return false;
    }
}