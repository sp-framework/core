<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Services\XeroAccountingApiBaseService;

class XeroAccountingApiService extends XeroAccountingApiBaseService
{
    protected static $operations =
        [
        'GetAccounts' => [
          'method' => 'GET',
          'resource' => 'Accounts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountsRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateAccount' => [
          'method' => 'PUT',
          'resource' => 'Accounts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateAccountRestResponse',
          'params' => [
          ],
        ],
        'GetAccount' => [
          'method' => 'GET',
          'resource' => 'Accounts/{AccountID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountRestResponse',
          'params' => [
            'AccountID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateAccount' => [
          'method' => 'POST',
          'resource' => 'Accounts/{AccountID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateAccountRestResponse',
          'params' => [
            'AccountID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteAccount' => [
          'method' => 'DELETE',
          'resource' => 'Accounts/{AccountID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteAccountRestResponse',
          'params' => [
            'AccountID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetAccountAttachments' => [
          'method' => 'GET',
          'resource' => 'Accounts/{AccountID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentsRestResponse',
          'params' => [
            'AccountID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetAccountAttachmentById' => [
          'method' => 'GET',
          'resource' => 'Accounts/{AccountID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetAccountAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'Accounts/{AccountID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateAccountAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'Accounts/{AccountID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateAccountAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateAccountAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'Accounts/{AccountID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateAccountAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBatchPayments' => [
          'method' => 'GET',
          'resource' => 'BatchPayments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBatchPaymentsRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateBatchPayment' => [
          'method' => 'PUT',
          'resource' => 'BatchPayments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBatchPaymentRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetBatchPaymentHistory' => [
          'method' => 'GET',
          'resource' => 'BatchPayments/{BatchPaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBatchPaymentHistoryRestResponse',
          'params' => [
            'BatchPaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateBatchPaymentHistoryRecord' => [
          'method' => 'PUT',
          'resource' => 'BatchPayments/{BatchPaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBatchPaymentHistoryRecordRestResponse',
          'params' => [
            'BatchPaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransactions' => [
          'method' => 'GET',
          'resource' => 'BankTransactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'CreateBankTransactions' => [
          'method' => 'PUT',
          'resource' => 'BankTransactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateOrCreateBankTransactions' => [
          'method' => 'POST',
          'resource' => 'BankTransactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateBankTransactionsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetBankTransaction' => [
          'method' => 'GET',
          'resource' => 'BankTransactions/{BankTransactionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateBankTransaction' => [
          'method' => 'POST',
          'resource' => 'BankTransactions/{BankTransactionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransactionRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetBankTransactionAttachments' => [
          'method' => 'GET',
          'resource' => 'BankTransactions/{BankTransactionID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentsRestResponse',
          'params' => [
            'BankTransactionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransactionAttachmentById' => [
          'method' => 'GET',
          'resource' => 'BankTransactions/{BankTransactionID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransactionAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'BankTransactions/{BankTransactionID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateBankTransactionAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'BankTransactions/{BankTransactionID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransactionAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateBankTransactionAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'BankTransactions/{BankTransactionID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransactionsHistory' => [
          'method' => 'GET',
          'resource' => 'BankTransactions/{BankTransactionID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionsHistoryRestResponse',
          'params' => [
            'BankTransactionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateBankTransactionHistoryRecord' => [
          'method' => 'PUT',
          'resource' => 'BankTransactions/{BankTransactionID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionHistoryRecordRestResponse',
          'params' => [
            'BankTransactionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransfers' => [
          'method' => 'GET',
          'resource' => 'BankTransfers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransfersRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateBankTransfer' => [
          'method' => 'PUT',
          'resource' => 'BankTransfers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferRestResponse',
          'params' => [
          ],
        ],
        'GetBankTransfer' => [
          'method' => 'GET',
          'resource' => 'BankTransfers/{BankTransferID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferRestResponse',
          'params' => [
            'BankTransferID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransferAttachments' => [
          'method' => 'GET',
          'resource' => 'BankTransfers/{BankTransferID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentsRestResponse',
          'params' => [
            'BankTransferID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransferAttachmentById' => [
          'method' => 'GET',
          'resource' => 'BankTransfers/{BankTransferID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransferAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'BankTransfers/{BankTransferID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateBankTransferAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'BankTransfers/{BankTransferID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransferAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateBankTransferAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'BankTransfers/{BankTransferID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBankTransferHistory' => [
          'method' => 'GET',
          'resource' => 'BankTransfers/{BankTransferID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferHistoryRestResponse',
          'params' => [
            'BankTransferID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateBankTransferHistoryRecord' => [
          'method' => 'PUT',
          'resource' => 'BankTransfers/{BankTransferID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferHistoryRecordRestResponse',
          'params' => [
            'BankTransferID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBrandingThemes' => [
          'method' => 'GET',
          'resource' => 'BrandingThemes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemesRestResponse',
          'params' => [
          ],
        ],
        'GetBrandingTheme' => [
          'method' => 'GET',
          'resource' => 'BrandingThemes/{BrandingThemeID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemeRestResponse',
          'params' => [
            'BrandingThemeID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBrandingThemePaymentServices' => [
          'method' => 'GET',
          'resource' => 'BrandingThemes/{BrandingThemeID}/PaymentServices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemePaymentServicesRestResponse',
          'params' => [
            'BrandingThemeID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateBrandingThemePaymentServices' => [
          'method' => 'POST',
          'resource' => 'BrandingThemes/{BrandingThemeID}/PaymentServices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBrandingThemePaymentServicesRestResponse',
          'params' => [
            'BrandingThemeID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContacts' => [
          'method' => 'GET',
          'resource' => 'Contacts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestResponse',
          'params' => [
            'includeArchived' => [
              'valid' => [
          'boolean',
              ],
            ],
          ],
        ],
        'CreateContacts' => [
          'method' => 'PUT',
          'resource' => 'Contacts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'UpdateOrCreateContacts' => [
          'method' => 'POST',
          'resource' => 'Contacts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateContactsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetContactByContactNumber' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactNumber}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactByContactNumberRestResponse',
          'params' => [
            'ContactNumber' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContact' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateContact' => [
          'method' => 'POST',
          'resource' => 'Contacts/{ContactID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContactAttachments' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentsRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContactAttachmentById' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContactAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateContactAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'Contacts/{ContactID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateContactAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'Contacts/{ContactID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContactCISSettings' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactID}/CISSettings',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactCISSettingsRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContactHistory' => [
          'method' => 'GET',
          'resource' => 'Contacts/{ContactID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactHistoryRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateContactHistory' => [
          'method' => 'PUT',
          'resource' => 'Contacts/{ContactID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactHistoryRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetContactGroups' => [
          'method' => 'GET',
          'resource' => 'ContactGroups',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupsRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateContactGroup' => [
          'method' => 'PUT',
          'resource' => 'ContactGroups',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactGroupRestResponse',
          'params' => [
          ],
        ],
        'GetContactGroup' => [
          'method' => 'GET',
          'resource' => 'ContactGroups/{ContactGroupID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupRestResponse',
          'params' => [
            'ContactGroupID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateContactGroup' => [
          'method' => 'POST',
          'resource' => 'ContactGroups/{ContactGroupID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactGroupRestResponse',
          'params' => [
            'ContactGroupID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateContactGroupContacts' => [
          'method' => 'PUT',
          'resource' => 'ContactGroups/{ContactGroupID}/Contacts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactGroupContactsRestResponse',
          'params' => [
            'ContactGroupID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteContactGroupContacts' => [
          'method' => 'DELETE',
          'resource' => 'ContactGroups/{ContactGroupID}/Contacts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteContactGroupContactsRestResponse',
          'params' => [
            'ContactGroupID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteContactGroupContact' => [
          'method' => 'DELETE',
          'resource' => 'ContactGroups/{ContactGroupID}/Contacts/{ContactID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteContactGroupContactRestResponse',
          'params' => [
            'ContactID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCreditNotes' => [
          'method' => 'GET',
          'resource' => 'CreditNotes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNotesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'CreateCreditNotes' => [
          'method' => 'PUT',
          'resource' => 'CreditNotes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNotesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateOrCreateCreditNotes' => [
          'method' => 'POST',
          'resource' => 'CreditNotes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateCreditNotesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetCreditNote' => [
          'method' => 'GET',
          'resource' => 'CreditNotes/{CreditNoteID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateCreditNote' => [
          'method' => 'POST',
          'resource' => 'CreditNotes/{CreditNoteID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateCreditNoteRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetCreditNoteAttachments' => [
          'method' => 'GET',
          'resource' => 'CreditNotes/{CreditNoteID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentsRestResponse',
          'params' => [
            'CreditNoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCreditNoteAttachmentById' => [
          'method' => 'GET',
          'resource' => 'CreditNotes/{CreditNoteID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCreditNoteAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'CreditNotes/{CreditNoteID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateCreditNoteAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'CreditNotes/{CreditNoteID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateCreditNoteAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateCreditNoteAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'CreditNotes/{CreditNoteID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteAttachmentByFileNameRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\includeOnline',
        ],
        'GetCreditNoteAsPdf' => [
          'method' => 'GET',
          'resource' => 'CreditNotes/{CreditNoteID}/pdf',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAsPdfRestResponse',
          'params' => [
            'CreditNoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateCreditNoteAllocation' => [
          'method' => 'PUT',
          'resource' => 'CreditNotes/{CreditNoteID}/Allocations',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteAllocationRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetCreditNoteHistory' => [
          'method' => 'GET',
          'resource' => 'CreditNotes/{CreditNoteID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteHistoryRestResponse',
          'params' => [
            'CreditNoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateCreditNoteHistory' => [
          'method' => 'PUT',
          'resource' => 'CreditNotes/{CreditNoteID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteHistoryRestResponse',
          'params' => [
            'CreditNoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCurrencies' => [
          'method' => 'GET',
          'resource' => 'Currencies',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCurrenciesRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateCurrency' => [
          'method' => 'PUT',
          'resource' => 'Currencies',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCurrencyRestResponse',
          'params' => [
          ],
        ],
        'GetEmployees' => [
          'method' => 'GET',
          'resource' => 'Employees',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetEmployeesRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateEmployees' => [
          'method' => 'PUT',
          'resource' => 'Employees',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateEmployeesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'UpdateOrCreateEmployees' => [
          'method' => 'POST',
          'resource' => 'Employees',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateEmployeesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetEmployee' => [
          'method' => 'GET',
          'resource' => 'Employees/{EmployeeID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetEmployeeRestResponse',
          'params' => [
            'EmployeeID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetExpenseClaims' => [
          'method' => 'GET',
          'resource' => 'ExpenseClaims',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimsRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateExpenseClaims' => [
          'method' => 'PUT',
          'resource' => 'ExpenseClaims',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateExpenseClaimsRestResponse',
          'params' => [
          ],
        ],
        'GetExpenseClaim' => [
          'method' => 'GET',
          'resource' => 'ExpenseClaims/{ExpenseClaimID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimRestResponse',
          'params' => [
            'ExpenseClaimID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateExpenseClaim' => [
          'method' => 'POST',
          'resource' => 'ExpenseClaims/{ExpenseClaimID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateExpenseClaimRestResponse',
          'params' => [
            'ExpenseClaimID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetExpenseClaimHistory' => [
          'method' => 'GET',
          'resource' => 'ExpenseClaims/{ExpenseClaimID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimHistoryRestResponse',
          'params' => [
            'ExpenseClaimID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateExpenseClaimHistory' => [
          'method' => 'PUT',
          'resource' => 'ExpenseClaims/{ExpenseClaimID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateExpenseClaimHistoryRestResponse',
          'params' => [
            'ExpenseClaimID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInvoices' => [
          'method' => 'GET',
          'resource' => 'Invoices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoicesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'CreateInvoices' => [
          'method' => 'PUT',
          'resource' => 'Invoices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoicesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateOrCreateInvoices' => [
          'method' => 'POST',
          'resource' => 'Invoices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateInvoicesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetInvoice' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateInvoice' => [
          'method' => 'POST',
          'resource' => 'Invoices/{InvoiceID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateInvoiceRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetInvoiceAsPdf' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}/pdf',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAsPdfRestResponse',
          'params' => [
            'InvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInvoiceAttachments' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentsRestResponse',
          'params' => [
            'InvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInvoiceAttachmentById' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInvoiceAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateInvoiceAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'Invoices/{InvoiceID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateInvoiceAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateInvoiceAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'Invoices/{InvoiceID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoiceAttachmentByFileNameRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\includeOnline',
        ],
        'GetOnlineInvoice' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}/OnlineInvoice',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOnlineInvoiceRestResponse',
          'params' => [
            'InvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'EmailInvoice' => [
          'method' => 'POST',
          'resource' => 'Invoices/{InvoiceID}/Email',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\EmailInvoiceRestResponse',
          'params' => [
            'InvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInvoiceHistory' => [
          'method' => 'GET',
          'resource' => 'Invoices/{InvoiceID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceHistoryRestResponse',
          'params' => [
            'InvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateInvoiceHistory' => [
          'method' => 'PUT',
          'resource' => 'Invoices/{InvoiceID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoiceHistoryRestResponse',
          'params' => [
            'InvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInvoiceReminders' => [
          'method' => 'GET',
          'resource' => 'InvoiceReminders/Settings',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceRemindersRestResponse',
          'params' => [
          ],
        ],
        'GetItems' => [
          'method' => 'GET',
          'resource' => 'Items',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'CreateItems' => [
          'method' => 'PUT',
          'resource' => 'Items',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateItemsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateOrCreateItems' => [
          'method' => 'POST',
          'resource' => 'Items',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateItemsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetItem' => [
          'method' => 'GET',
          'resource' => 'Items/{ItemID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateItem' => [
          'method' => 'POST',
          'resource' => 'Items/{ItemID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateItemRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'DeleteItem' => [
          'method' => 'DELETE',
          'resource' => 'Items/{ItemID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteItemRestResponse',
          'params' => [
            'ItemID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetItemHistory' => [
          'method' => 'GET',
          'resource' => 'Items/{ItemID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemHistoryRestResponse',
          'params' => [
            'ItemID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateItemHistory' => [
          'method' => 'PUT',
          'resource' => 'Items/{ItemID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateItemHistoryRestResponse',
          'params' => [
            'ItemID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetJournals' => [
          'method' => 'GET',
          'resource' => 'Journals',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetJournalsRestResponse',
          'params' => [
            'paymentsOnly' => [
              'valid' => [
          'boolean',
              ],
            ],
          ],
        ],
        'GetJournal' => [
          'method' => 'GET',
          'resource' => 'Journals/{JournalID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetJournalRestResponse',
          'params' => [
            'JournalID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetLinkedTransactions' => [
          'method' => 'GET',
          'resource' => 'LinkedTransactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetLinkedTransactionsRestResponse',
          'params' => [
            'TargetTransactionID' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateLinkedTransaction' => [
          'method' => 'PUT',
          'resource' => 'LinkedTransactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateLinkedTransactionRestResponse',
          'params' => [
          ],
        ],
        'GetLinkedTransaction' => [
          'method' => 'GET',
          'resource' => 'LinkedTransactions/{LinkedTransactionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetLinkedTransactionRestResponse',
          'params' => [
            'LinkedTransactionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateLinkedTransaction' => [
          'method' => 'POST',
          'resource' => 'LinkedTransactions/{LinkedTransactionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateLinkedTransactionRestResponse',
          'params' => [
            'LinkedTransactionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteLinkedTransaction' => [
          'method' => 'DELETE',
          'resource' => 'LinkedTransactions/{LinkedTransactionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteLinkedTransactionRestResponse',
          'params' => [
            'LinkedTransactionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetManualJournals' => [
          'method' => 'GET',
          'resource' => 'ManualJournals',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateManualJournals' => [
          'method' => 'PUT',
          'resource' => 'ManualJournals',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'UpdateOrCreateManualJournals' => [
          'method' => 'POST',
          'resource' => 'ManualJournals',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateManualJournalsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetManualJournal' => [
          'method' => 'GET',
          'resource' => 'ManualJournals/{ManualJournalID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalRestResponse',
          'params' => [
            'ManualJournalID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateManualJournal' => [
          'method' => 'POST',
          'resource' => 'ManualJournals/{ManualJournalID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateManualJournalRestResponse',
          'params' => [
            'ManualJournalID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetManualJournalAttachments' => [
          'method' => 'GET',
          'resource' => 'ManualJournals/{ManualJournalID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentsRestResponse',
          'params' => [
            'ManualJournalID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetManualJournalAttachmentById' => [
          'method' => 'GET',
          'resource' => 'ManualJournals/{ManualJournalID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetManualJournalAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'ManualJournals/{ManualJournalID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateManualJournalAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'ManualJournals/{ManualJournalID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateManualJournalAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateManualJournalAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'ManualJournals/{ManualJournalID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetManualJournalsHistory' => [
          'method' => 'GET',
          'resource' => 'ManualJournals/{ManualJournalID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalsHistoryRestResponse',
          'params' => [
            'ManualJournalID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateManualJournalHistoryRecord' => [
          'method' => 'PUT',
          'resource' => 'ManualJournals/{ManualJournalID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalHistoryRecordRestResponse',
          'params' => [
            'ManualJournalID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetOrganisations' => [
          'method' => 'GET',
          'resource' => 'Organisation',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationsRestResponse',
          'params' => [
          ],
        ],
        'GetOrganisationActions' => [
          'method' => 'GET',
          'resource' => 'Organisation/Actions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationActionsRestResponse',
          'params' => [
          ],
        ],
        'GetOrganisationCISSettings' => [
          'method' => 'GET',
          'resource' => 'Organisation/{OrganisationID}/CISSettings',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationCISSettingsRestResponse',
          'params' => [
            'OrganisationID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetOverpayments' => [
          'method' => 'GET',
          'resource' => 'Overpayments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetOverpayment' => [
          'method' => 'GET',
          'resource' => 'Overpayments/{OverpaymentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentRestResponse',
          'params' => [
            'OverpaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateOverpaymentAllocations' => [
          'method' => 'PUT',
          'resource' => 'Overpayments/{OverpaymentID}/Allocations',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateOverpaymentAllocationsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetOverpaymentHistory' => [
          'method' => 'GET',
          'resource' => 'Overpayments/{OverpaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentHistoryRestResponse',
          'params' => [
            'OverpaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateOverpaymentHistory' => [
          'method' => 'PUT',
          'resource' => 'Overpayments/{OverpaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateOverpaymentHistoryRestResponse',
          'params' => [
            'OverpaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPayments' => [
          'method' => 'GET',
          'resource' => 'Payments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreatePayments' => [
          'method' => 'PUT',
          'resource' => 'Payments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'CreatePayment' => [
          'method' => 'POST',
          'resource' => 'Payments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentRestResponse',
          'params' => [
          ],
        ],
        'GetPayment' => [
          'method' => 'GET',
          'resource' => 'Payments/{PaymentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentRestResponse',
          'params' => [
            'PaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeletePayment' => [
          'method' => 'POST',
          'resource' => 'Payments/{PaymentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeletePaymentRestResponse',
          'params' => [
            'PaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPaymentHistory' => [
          'method' => 'GET',
          'resource' => 'Payments/{PaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentHistoryRestResponse',
          'params' => [
            'PaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreatePaymentHistory' => [
          'method' => 'PUT',
          'resource' => 'Payments/{PaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentHistoryRestResponse',
          'params' => [
            'PaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPaymentServices' => [
          'method' => 'GET',
          'resource' => 'PaymentServices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentServicesRestResponse',
          'params' => [
          ],
        ],
        'CreatePaymentService' => [
          'method' => 'PUT',
          'resource' => 'PaymentServices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentServiceRestResponse',
          'params' => [
          ],
        ],
        'GetPrepayments' => [
          'method' => 'GET',
          'resource' => 'Prepayments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetPrepayment' => [
          'method' => 'GET',
          'resource' => 'Prepayments/{PrepaymentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentRestResponse',
          'params' => [
            'PrepaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreatePrepaymentAllocations' => [
          'method' => 'PUT',
          'resource' => 'Prepayments/{PrepaymentID}/Allocations',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePrepaymentAllocationsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetPrepaymentHistory' => [
          'method' => 'GET',
          'resource' => 'Prepayments/{PrepaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentHistoryRestResponse',
          'params' => [
            'PrepaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreatePrepaymentHistory' => [
          'method' => 'PUT',
          'resource' => 'Prepayments/{PrepaymentID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePrepaymentHistoryRestResponse',
          'params' => [
            'PrepaymentID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrders' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreatePurchaseOrders' => [
          'method' => 'PUT',
          'resource' => 'PurchaseOrders',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrdersRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'UpdateOrCreatePurchaseOrders' => [
          'method' => 'POST',
          'resource' => 'PurchaseOrders',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreatePurchaseOrdersRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetPurchaseOrderAsPdf' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/pdf',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAsPdfRestResponse',
          'params' => [
            'PurchaseOrderID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrder' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderRestResponse',
          'params' => [
            'PurchaseOrderID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdatePurchaseOrder' => [
          'method' => 'POST',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdatePurchaseOrderRestResponse',
          'params' => [
            'PurchaseOrderID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrderByNumber' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderNumber}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderByNumberRestResponse',
          'params' => [
            'PurchaseOrderNumber' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrderHistory' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestResponse',
          'params' => [
            'PurchaseOrderID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreatePurchaseOrderHistory' => [
          'method' => 'PUT',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrderHistoryRestResponse',
          'params' => [
            'PurchaseOrderID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrderAttachments' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestResponse',
          'params' => [
            'PurchaseOrderID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrderAttachmentById' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPurchaseOrder≠AttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrder≠AttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdatePurchaseOrderAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdatePurchaseOrderAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreatePurchaseOrderAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'PurchaseOrders/{PurchaseOrderID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrderAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetQuotes' => [
          'method' => 'GET',
          'resource' => 'Quotes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuotesRestResponse',
          'params' => [
            'QuoteNumber' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateQuotes' => [
          'method' => 'PUT',
          'resource' => 'Quotes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuotesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'UpdateOrCreateQuotes' => [
          'method' => 'POST',
          'resource' => 'Quotes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateQuotesRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\summarizeErrors',
        ],
        'GetQuote' => [
          'method' => 'GET',
          'resource' => 'Quotes/{QuoteID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteRestResponse',
          'params' => [
            'QuoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateQuote' => [
          'method' => 'POST',
          'resource' => 'Quotes/{QuoteID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateQuoteRestResponse',
          'params' => [
            'QuoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetQuoteHistory' => [
          'method' => 'GET',
          'resource' => 'Quotes/{QuoteID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteHistoryRestResponse',
          'params' => [
            'QuoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateQuoteHistory' => [
          'method' => 'PUT',
          'resource' => 'Quotes/{QuoteID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuoteHistoryRestResponse',
          'params' => [
            'QuoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetQuoteAsPdf' => [
          'method' => 'GET',
          'resource' => 'Quotes/{QuoteID}/pdf',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAsPdfRestResponse',
          'params' => [
            'QuoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetQuoteAttachments' => [
          'method' => 'GET',
          'resource' => 'Quotes/{QuoteID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentsRestResponse',
          'params' => [
            'QuoteID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetQuoteAttachmentById' => [
          'method' => 'GET',
          'resource' => 'Quotes/{QuoteID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetQuoteAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'Quotes/{QuoteID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateQuoteAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'Quotes/{QuoteID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateQuoteAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateQuoteAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'Quotes/{QuoteID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuoteAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetReceipts' => [
          'method' => 'GET',
          'resource' => 'Receipts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptsRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'CreateReceipt' => [
          'method' => 'PUT',
          'resource' => 'Receipts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetReceipt' => [
          'method' => 'GET',
          'resource' => 'Receipts/{ReceiptID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'UpdateReceipt' => [
          'method' => 'POST',
          'resource' => 'Receipts/{ReceiptID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateReceiptRestResponse',
          'params' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\unitdp',
        ],
        'GetReceiptAttachments' => [
          'method' => 'GET',
          'resource' => 'Receipts/{ReceiptID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentsRestResponse',
          'params' => [
            'ReceiptID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetReceiptAttachmentById' => [
          'method' => 'GET',
          'resource' => 'Receipts/{ReceiptID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetReceiptAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'Receipts/{ReceiptID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateReceiptAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'Receipts/{ReceiptID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateReceiptAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateReceiptAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'Receipts/{ReceiptID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetReceiptHistory' => [
          'method' => 'GET',
          'resource' => 'Receipts/{ReceiptID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptHistoryRestResponse',
          'params' => [
            'ReceiptID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateReceiptHistory' => [
          'method' => 'PUT',
          'resource' => 'Receipts/{ReceiptID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptHistoryRestResponse',
          'params' => [
            'ReceiptID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetRepeatingInvoices' => [
          'method' => 'GET',
          'resource' => 'RepeatingInvoices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoicesRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetRepeatingInvoice' => [
          'method' => 'GET',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceRestResponse',
          'params' => [
            'RepeatingInvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetRepeatingInvoiceAttachments' => [
          'method' => 'GET',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/Attachments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentsRestResponse',
          'params' => [
            'RepeatingInvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetRepeatingInvoiceAttachmentById' => [
          'method' => 'GET',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/Attachments/{AttachmentID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentByIdRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetRepeatingInvoiceAttachmentByFileName' => [
          'method' => 'GET',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentByFileNameRestResponse',
          'params' => [
            'contentType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateRepeatingInvoiceAttachmentByFileName' => [
          'method' => 'POST',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateRepeatingInvoiceAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateRepeatingInvoiceAttachmentByFileName' => [
          'method' => 'PUT',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/Attachments/{FileName}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateRepeatingInvoiceAttachmentByFileNameRestResponse',
          'params' => [
            'FileName' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetRepeatingInvoiceHistory' => [
          'method' => 'GET',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceHistoryRestResponse',
          'params' => [
            'RepeatingInvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateRepeatingInvoiceHistory' => [
          'method' => 'PUT',
          'resource' => 'RepeatingInvoices/{RepeatingInvoiceID}/History',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateRepeatingInvoiceHistoryRestResponse',
          'params' => [
            'RepeatingInvoiceID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetReportTenNinetyNine' => [
          'method' => 'GET',
          'resource' => 'Reports/TenNinetyNine',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportTenNinetyNineRestResponse',
          'params' => [
            'reportYear' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetReportAgedPayablesByContact' => [
          'method' => 'GET',
          'resource' => 'Reports/AgedPayablesByContact',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportAgedPayablesByContactRestResponse',
          'params' => [
            'toDate' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetReportAgedReceivablesByContact' => [
          'method' => 'GET',
          'resource' => 'Reports/AgedReceivablesByContact',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportAgedReceivablesByContactRestResponse',
          'params' => [
            'toDate' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetReportBalanceSheet' => [
          'method' => 'GET',
          'resource' => 'Reports/BalanceSheet',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBalanceSheetRestResponse',
          'params' => [
            'paymentsOnly' => [
              'valid' => [
          'boolean',
              ],
            ],
          ],
        ],
        'GetReportBankSummary' => [
          'method' => 'GET',
          'resource' => 'Reports/BankSummary',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBankSummaryRestResponse',
          'params' => [
            'toDate' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetReportBASorGSTList' => [
          'method' => 'GET',
          'resource' => 'Reports',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBASorGSTListRestResponse',
          'params' => [
          ],
        ],
        'GetReportBASorGST' => [
          'method' => 'GET',
          'resource' => 'Reports/{ReportID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBASorGSTRestResponse',
          'params' => [
            'ReportID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetReportBudgetSummary' => [
          'method' => 'GET',
          'resource' => 'Reports/BudgetSummary',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBudgetSummaryRestResponse',
          'params' => [
            'timeframe' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'GetReportExecutiveSummary' => [
          'method' => 'GET',
          'resource' => 'Reports/ExecutiveSummary',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportExecutiveSummaryRestResponse',
          'params' => [
            'date' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetReportProfitAndLoss' => [
          'method' => 'GET',
          'resource' => 'Reports/ProfitAndLoss',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportProfitAndLossRestResponse',
          'params' => [
            'paymentsOnly' => [
              'valid' => [
          'boolean',
              ],
            ],
          ],
        ],
        'GetReportTrialBalance' => [
          'method' => 'GET',
          'resource' => 'Reports/TrialBalance',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportTrialBalanceRestResponse',
          'params' => [
            'paymentsOnly' => [
              'valid' => [
          'boolean',
              ],
            ],
          ],
        ],
        'PostSetup' => [
          'method' => 'POST',
          'resource' => 'Setup',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\PostSetupRestResponse',
          'params' => [
          ],
        ],
        'GetTaxRates' => [
          'method' => 'GET',
          'resource' => 'TaxRates',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTaxRatesRestResponse',
          'params' => [
            'TaxType' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateTaxRates' => [
          'method' => 'PUT',
          'resource' => 'TaxRates',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTaxRatesRestResponse',
          'params' => [
          ],
        ],
        'UpdateTaxRate' => [
          'method' => 'POST',
          'resource' => 'TaxRates',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTaxRateRestResponse',
          'params' => [
          ],
        ],
        'GetTrackingCategories' => [
          'method' => 'GET',
          'resource' => 'TrackingCategories',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTrackingCategoriesRestResponse',
          'params' => [
            'includeArchived' => [
              'valid' => [
          'boolean',
              ],
            ],
          ],
        ],
        'CreateTrackingCategory' => [
          'method' => 'PUT',
          'resource' => 'TrackingCategories',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTrackingCategoryRestResponse',
          'params' => [
          ],
        ],
        'GetTrackingCategory' => [
          'method' => 'GET',
          'resource' => 'TrackingCategories/{TrackingCategoryID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTrackingCategoryRestResponse',
          'params' => [
            'TrackingCategoryID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateTrackingCategory' => [
          'method' => 'POST',
          'resource' => 'TrackingCategories/{TrackingCategoryID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTrackingCategoryRestResponse',
          'params' => [
            'TrackingCategoryID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteTrackingCategory' => [
          'method' => 'DELETE',
          'resource' => 'TrackingCategories/{TrackingCategoryID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteTrackingCategoryRestResponse',
          'params' => [
            'TrackingCategoryID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateTrackingOptions' => [
          'method' => 'PUT',
          'resource' => 'TrackingCategories/{TrackingCategoryID}/Options',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTrackingOptionsRestResponse',
          'params' => [
            'TrackingCategoryID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateTrackingOptions' => [
          'method' => 'POST',
          'resource' => 'TrackingCategories/{TrackingCategoryID}/Options/{TrackingOptionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTrackingOptionsRestResponse',
          'params' => [
            'TrackingOptionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteTrackingOptions' => [
          'method' => 'DELETE',
          'resource' => 'TrackingCategories/{TrackingCategoryID}/Options/{TrackingOptionID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteTrackingOptionsRestResponse',
          'params' => [
            'TrackingOptionID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetUsers' => [
          'method' => 'GET',
          'resource' => 'Users',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUsersRestResponse',
          'params' => [
            'order' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetUser' => [
          'method' => 'GET',
          'resource' => 'Users/{UserID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUserRestResponse',
          'params' => [
            'UserID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getAccounts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountsRestRequest $request)
    {
        return $this->getAccountsAsync($request)->wait();
    }

    public function getAccountsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountsRestRequest $request)
    {
        return $this->callOperationAsync('GetAccounts', $request);
    }

    public function createAccount(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateAccountRestRequest $request)
    {
        return $this->createAccountAsync($request)->wait();
    }

    public function createAccountAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateAccountRestRequest $request)
    {
        return $this->callOperationAsync('CreateAccount', $request);
    }

    public function getAccount(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountRestRequest $request)
    {
        return $this->getAccountAsync($request)->wait();
    }

    public function getAccountAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountRestRequest $request)
    {
        return $this->callOperationAsync('GetAccount', $request);
    }

    public function updateAccount(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateAccountRestRequest $request)
    {
        return $this->updateAccountAsync($request)->wait();
    }

    public function updateAccountAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateAccountRestRequest $request)
    {
        return $this->callOperationAsync('UpdateAccount', $request);
    }

    public function deleteAccount(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteAccountRestRequest $request)
    {
        return $this->deleteAccountAsync($request)->wait();
    }

    public function deleteAccountAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteAccountRestRequest $request)
    {
        return $this->callOperationAsync('DeleteAccount', $request);
    }

    public function getAccountAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentsRestRequest $request)
    {
        return $this->getAccountAttachmentsAsync($request)->wait();
    }

    public function getAccountAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetAccountAttachments', $request);
    }

    public function getAccountAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentByIdRestRequest $request)
    {
        return $this->getAccountAttachmentByIdAsync($request)->wait();
    }

    public function getAccountAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetAccountAttachmentById', $request);
    }

    public function getAccountAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentByFileNameRestRequest $request)
    {
        return $this->getAccountAttachmentByFileNameAsync($request)->wait();
    }

    public function getAccountAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetAccountAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetAccountAttachmentByFileName', $request);
    }

    public function updateAccountAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateAccountAttachmentByFileNameRestRequest $request)
    {
        return $this->updateAccountAttachmentByFileNameAsync($request)->wait();
    }

    public function updateAccountAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateAccountAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateAccountAttachmentByFileName', $request);
    }

    public function createAccountAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateAccountAttachmentByFileNameRestRequest $request)
    {
        return $this->createAccountAttachmentByFileNameAsync($request)->wait();
    }

    public function createAccountAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateAccountAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateAccountAttachmentByFileName', $request);
    }

    public function getBatchPayments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBatchPaymentsRestRequest $request)
    {
        return $this->getBatchPaymentsAsync($request)->wait();
    }

    public function getBatchPaymentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBatchPaymentsRestRequest $request)
    {
        return $this->callOperationAsync('GetBatchPayments', $request);
    }

    public function createBatchPayment(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBatchPaymentRestRequest $request)
    {
        return $this->createBatchPaymentAsync($request)->wait();
    }

    public function createBatchPaymentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBatchPaymentRestRequest $request)
    {
        return $this->callOperationAsync('CreateBatchPayment', $request);
    }

    public function getBatchPaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBatchPaymentHistoryRestRequest $request)
    {
        return $this->getBatchPaymentHistoryAsync($request)->wait();
    }

    public function getBatchPaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBatchPaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetBatchPaymentHistory', $request);
    }

    public function createBatchPaymentHistoryRecord(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBatchPaymentHistoryRecordRestRequest $request)
    {
        return $this->createBatchPaymentHistoryRecordAsync($request)->wait();
    }

    public function createBatchPaymentHistoryRecordAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBatchPaymentHistoryRecordRestRequest $request)
    {
        return $this->callOperationAsync('CreateBatchPaymentHistoryRecord', $request);
    }

    public function getBankTransactions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionsRestRequest $request)
    {
        return $this->getBankTransactionsAsync($request)->wait();
    }

    public function getBankTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionsRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransactions', $request);
    }

    public function createBankTransactions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionsRestRequest $request)
    {
        return $this->createBankTransactionsAsync($request)->wait();
    }

    public function createBankTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionsRestRequest $request)
    {
        return $this->callOperationAsync('CreateBankTransactions', $request);
    }

    public function updateOrCreateBankTransactions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateBankTransactionsRestRequest $request)
    {
        return $this->updateOrCreateBankTransactionsAsync($request)->wait();
    }

    public function updateOrCreateBankTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateBankTransactionsRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateBankTransactions', $request);
    }

    public function getBankTransaction(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionRestRequest $request)
    {
        return $this->getBankTransactionAsync($request)->wait();
    }

    public function getBankTransactionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransaction', $request);
    }

    public function updateBankTransaction(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransactionRestRequest $request)
    {
        return $this->updateBankTransactionAsync($request)->wait();
    }

    public function updateBankTransactionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransactionRestRequest $request)
    {
        return $this->callOperationAsync('UpdateBankTransaction', $request);
    }

    public function getBankTransactionAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentsRestRequest $request)
    {
        return $this->getBankTransactionAttachmentsAsync($request)->wait();
    }

    public function getBankTransactionAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransactionAttachments', $request);
    }

    public function getBankTransactionAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentByIdRestRequest $request)
    {
        return $this->getBankTransactionAttachmentByIdAsync($request)->wait();
    }

    public function getBankTransactionAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransactionAttachmentById', $request);
    }

    public function getBankTransactionAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentByFileNameRestRequest $request)
    {
        return $this->getBankTransactionAttachmentByFileNameAsync($request)->wait();
    }

    public function getBankTransactionAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransactionAttachmentByFileName', $request);
    }

    public function updateBankTransactionAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransactionAttachmentByFileNameRestRequest $request)
    {
        return $this->updateBankTransactionAttachmentByFileNameAsync($request)->wait();
    }

    public function updateBankTransactionAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransactionAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateBankTransactionAttachmentByFileName', $request);
    }

    public function createBankTransactionAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionAttachmentByFileNameRestRequest $request)
    {
        return $this->createBankTransactionAttachmentByFileNameAsync($request)->wait();
    }

    public function createBankTransactionAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateBankTransactionAttachmentByFileName', $request);
    }

    public function getBankTransactionsHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionsHistoryRestRequest $request)
    {
        return $this->getBankTransactionsHistoryAsync($request)->wait();
    }

    public function getBankTransactionsHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransactionsHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransactionsHistory', $request);
    }

    public function createBankTransactionHistoryRecord(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionHistoryRecordRestRequest $request)
    {
        return $this->createBankTransactionHistoryRecordAsync($request)->wait();
    }

    public function createBankTransactionHistoryRecordAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransactionHistoryRecordRestRequest $request)
    {
        return $this->callOperationAsync('CreateBankTransactionHistoryRecord', $request);
    }

    public function getBankTransfers(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransfersRestRequest $request)
    {
        return $this->getBankTransfersAsync($request)->wait();
    }

    public function getBankTransfersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransfersRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransfers', $request);
    }

    public function createBankTransfer(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferRestRequest $request)
    {
        return $this->createBankTransferAsync($request)->wait();
    }

    public function createBankTransferAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferRestRequest $request)
    {
        return $this->callOperationAsync('CreateBankTransfer', $request);
    }

    public function getBankTransfer(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferRestRequest $request)
    {
        return $this->getBankTransferAsync($request)->wait();
    }

    public function getBankTransferAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransfer', $request);
    }

    public function getBankTransferAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentsRestRequest $request)
    {
        return $this->getBankTransferAttachmentsAsync($request)->wait();
    }

    public function getBankTransferAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransferAttachments', $request);
    }

    public function getBankTransferAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentByIdRestRequest $request)
    {
        return $this->getBankTransferAttachmentByIdAsync($request)->wait();
    }

    public function getBankTransferAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransferAttachmentById', $request);
    }

    public function getBankTransferAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentByFileNameRestRequest $request)
    {
        return $this->getBankTransferAttachmentByFileNameAsync($request)->wait();
    }

    public function getBankTransferAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransferAttachmentByFileName', $request);
    }

    public function updateBankTransferAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransferAttachmentByFileNameRestRequest $request)
    {
        return $this->updateBankTransferAttachmentByFileNameAsync($request)->wait();
    }

    public function updateBankTransferAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateBankTransferAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateBankTransferAttachmentByFileName', $request);
    }

    public function createBankTransferAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferAttachmentByFileNameRestRequest $request)
    {
        return $this->createBankTransferAttachmentByFileNameAsync($request)->wait();
    }

    public function createBankTransferAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateBankTransferAttachmentByFileName', $request);
    }

    public function getBankTransferHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferHistoryRestRequest $request)
    {
        return $this->getBankTransferHistoryAsync($request)->wait();
    }

    public function getBankTransferHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBankTransferHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetBankTransferHistory', $request);
    }

    public function createBankTransferHistoryRecord(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferHistoryRecordRestRequest $request)
    {
        return $this->createBankTransferHistoryRecordAsync($request)->wait();
    }

    public function createBankTransferHistoryRecordAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBankTransferHistoryRecordRestRequest $request)
    {
        return $this->callOperationAsync('CreateBankTransferHistoryRecord', $request);
    }

    public function getBrandingThemes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemesRestRequest $request)
    {
        return $this->getBrandingThemesAsync($request)->wait();
    }

    public function getBrandingThemesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemesRestRequest $request)
    {
        return $this->callOperationAsync('GetBrandingThemes', $request);
    }

    public function getBrandingTheme(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemeRestRequest $request)
    {
        return $this->getBrandingThemeAsync($request)->wait();
    }

    public function getBrandingThemeAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemeRestRequest $request)
    {
        return $this->callOperationAsync('GetBrandingTheme', $request);
    }

    public function getBrandingThemePaymentServices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemePaymentServicesRestRequest $request)
    {
        return $this->getBrandingThemePaymentServicesAsync($request)->wait();
    }

    public function getBrandingThemePaymentServicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetBrandingThemePaymentServicesRestRequest $request)
    {
        return $this->callOperationAsync('GetBrandingThemePaymentServices', $request);
    }

    public function createBrandingThemePaymentServices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBrandingThemePaymentServicesRestRequest $request)
    {
        return $this->createBrandingThemePaymentServicesAsync($request)->wait();
    }

    public function createBrandingThemePaymentServicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateBrandingThemePaymentServicesRestRequest $request)
    {
        return $this->callOperationAsync('CreateBrandingThemePaymentServices', $request);
    }

    public function getContacts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestRequest $request)
    {
        return $this->getContactsAsync($request)->wait();
    }

    public function getContactsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactsRestRequest $request)
    {
        return $this->callOperationAsync('GetContacts', $request);
    }

    public function createContacts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactsRestRequest $request)
    {
        return $this->createContactsAsync($request)->wait();
    }

    public function createContactsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactsRestRequest $request)
    {
        return $this->callOperationAsync('CreateContacts', $request);
    }

    public function updateOrCreateContacts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateContactsRestRequest $request)
    {
        return $this->updateOrCreateContactsAsync($request)->wait();
    }

    public function updateOrCreateContactsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateContactsRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateContacts', $request);
    }

    public function getContactByContactNumber(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactByContactNumberRestRequest $request)
    {
        return $this->getContactByContactNumberAsync($request)->wait();
    }

    public function getContactByContactNumberAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactByContactNumberRestRequest $request)
    {
        return $this->callOperationAsync('GetContactByContactNumber', $request);
    }

    public function getContact(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactRestRequest $request)
    {
        return $this->getContactAsync($request)->wait();
    }

    public function getContactAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactRestRequest $request)
    {
        return $this->callOperationAsync('GetContact', $request);
    }

    public function updateContact(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactRestRequest $request)
    {
        return $this->updateContactAsync($request)->wait();
    }

    public function updateContactAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactRestRequest $request)
    {
        return $this->callOperationAsync('UpdateContact', $request);
    }

    public function getContactAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentsRestRequest $request)
    {
        return $this->getContactAttachmentsAsync($request)->wait();
    }

    public function getContactAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetContactAttachments', $request);
    }

    public function getContactAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestRequest $request)
    {
        return $this->getContactAttachmentByIdAsync($request)->wait();
    }

    public function getContactAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetContactAttachmentById', $request);
    }

    public function getContactAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByFileNameRestRequest $request)
    {
        return $this->getContactAttachmentByFileNameAsync($request)->wait();
    }

    public function getContactAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetContactAttachmentByFileName', $request);
    }

    public function updateContactAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactAttachmentByFileNameRestRequest $request)
    {
        return $this->updateContactAttachmentByFileNameAsync($request)->wait();
    }

    public function updateContactAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateContactAttachmentByFileName', $request);
    }

    public function createContactAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactAttachmentByFileNameRestRequest $request)
    {
        return $this->createContactAttachmentByFileNameAsync($request)->wait();
    }

    public function createContactAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateContactAttachmentByFileName', $request);
    }

    public function getContactCISSettings(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactCISSettingsRestRequest $request)
    {
        return $this->getContactCISSettingsAsync($request)->wait();
    }

    public function getContactCISSettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactCISSettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetContactCISSettings', $request);
    }

    public function getContactHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactHistoryRestRequest $request)
    {
        return $this->getContactHistoryAsync($request)->wait();
    }

    public function getContactHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetContactHistory', $request);
    }

    public function createContactHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactHistoryRestRequest $request)
    {
        return $this->createContactHistoryAsync($request)->wait();
    }

    public function createContactHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateContactHistory', $request);
    }

    public function getContactGroups(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupsRestRequest $request)
    {
        return $this->getContactGroupsAsync($request)->wait();
    }

    public function getContactGroupsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupsRestRequest $request)
    {
        return $this->callOperationAsync('GetContactGroups', $request);
    }

    public function createContactGroup(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactGroupRestRequest $request)
    {
        return $this->createContactGroupAsync($request)->wait();
    }

    public function createContactGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactGroupRestRequest $request)
    {
        return $this->callOperationAsync('CreateContactGroup', $request);
    }

    public function getContactGroup(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupRestRequest $request)
    {
        return $this->getContactGroupAsync($request)->wait();
    }

    public function getContactGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetContactGroupRestRequest $request)
    {
        return $this->callOperationAsync('GetContactGroup', $request);
    }

    public function updateContactGroup(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactGroupRestRequest $request)
    {
        return $this->updateContactGroupAsync($request)->wait();
    }

    public function updateContactGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateContactGroupRestRequest $request)
    {
        return $this->callOperationAsync('UpdateContactGroup', $request);
    }

    public function createContactGroupContacts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactGroupContactsRestRequest $request)
    {
        return $this->createContactGroupContactsAsync($request)->wait();
    }

    public function createContactGroupContactsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateContactGroupContactsRestRequest $request)
    {
        return $this->callOperationAsync('CreateContactGroupContacts', $request);
    }

    public function deleteContactGroupContacts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteContactGroupContactsRestRequest $request)
    {
        return $this->deleteContactGroupContactsAsync($request)->wait();
    }

    public function deleteContactGroupContactsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteContactGroupContactsRestRequest $request)
    {
        return $this->callOperationAsync('DeleteContactGroupContacts', $request);
    }

    public function deleteContactGroupContact(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteContactGroupContactRestRequest $request)
    {
        return $this->deleteContactGroupContactAsync($request)->wait();
    }

    public function deleteContactGroupContactAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteContactGroupContactRestRequest $request)
    {
        return $this->callOperationAsync('DeleteContactGroupContact', $request);
    }

    public function getCreditNotes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNotesRestRequest $request)
    {
        return $this->getCreditNotesAsync($request)->wait();
    }

    public function getCreditNotesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNotesRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNotes', $request);
    }

    public function createCreditNotes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNotesRestRequest $request)
    {
        return $this->createCreditNotesAsync($request)->wait();
    }

    public function createCreditNotesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNotesRestRequest $request)
    {
        return $this->callOperationAsync('CreateCreditNotes', $request);
    }

    public function updateOrCreateCreditNotes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateCreditNotesRestRequest $request)
    {
        return $this->updateOrCreateCreditNotesAsync($request)->wait();
    }

    public function updateOrCreateCreditNotesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateCreditNotesRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateCreditNotes', $request);
    }

    public function getCreditNote(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteRestRequest $request)
    {
        return $this->getCreditNoteAsync($request)->wait();
    }

    public function getCreditNoteAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNote', $request);
    }

    public function updateCreditNote(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateCreditNoteRestRequest $request)
    {
        return $this->updateCreditNoteAsync($request)->wait();
    }

    public function updateCreditNoteAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateCreditNoteRestRequest $request)
    {
        return $this->callOperationAsync('UpdateCreditNote', $request);
    }

    public function getCreditNoteAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentsRestRequest $request)
    {
        return $this->getCreditNoteAttachmentsAsync($request)->wait();
    }

    public function getCreditNoteAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNoteAttachments', $request);
    }

    public function getCreditNoteAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentByIdRestRequest $request)
    {
        return $this->getCreditNoteAttachmentByIdAsync($request)->wait();
    }

    public function getCreditNoteAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNoteAttachmentById', $request);
    }

    public function getCreditNoteAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentByFileNameRestRequest $request)
    {
        return $this->getCreditNoteAttachmentByFileNameAsync($request)->wait();
    }

    public function getCreditNoteAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNoteAttachmentByFileName', $request);
    }

    public function updateCreditNoteAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateCreditNoteAttachmentByFileNameRestRequest $request)
    {
        return $this->updateCreditNoteAttachmentByFileNameAsync($request)->wait();
    }

    public function updateCreditNoteAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateCreditNoteAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateCreditNoteAttachmentByFileName', $request);
    }

    public function createCreditNoteAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteAttachmentByFileNameRestRequest $request)
    {
        return $this->createCreditNoteAttachmentByFileNameAsync($request)->wait();
    }

    public function createCreditNoteAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateCreditNoteAttachmentByFileName', $request);
    }

    public function getCreditNoteAsPdf(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAsPdfRestRequest $request)
    {
        return $this->getCreditNoteAsPdfAsync($request)->wait();
    }

    public function getCreditNoteAsPdfAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteAsPdfRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNoteAsPdf', $request);
    }

    public function createCreditNoteAllocation(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteAllocationRestRequest $request)
    {
        return $this->createCreditNoteAllocationAsync($request)->wait();
    }

    public function createCreditNoteAllocationAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteAllocationRestRequest $request)
    {
        return $this->callOperationAsync('CreateCreditNoteAllocation', $request);
    }

    public function getCreditNoteHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteHistoryRestRequest $request)
    {
        return $this->getCreditNoteHistoryAsync($request)->wait();
    }

    public function getCreditNoteHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCreditNoteHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetCreditNoteHistory', $request);
    }

    public function createCreditNoteHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteHistoryRestRequest $request)
    {
        return $this->createCreditNoteHistoryAsync($request)->wait();
    }

    public function createCreditNoteHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCreditNoteHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateCreditNoteHistory', $request);
    }

    public function getCurrencies(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCurrenciesRestRequest $request)
    {
        return $this->getCurrenciesAsync($request)->wait();
    }

    public function getCurrenciesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetCurrenciesRestRequest $request)
    {
        return $this->callOperationAsync('GetCurrencies', $request);
    }

    public function createCurrency(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCurrencyRestRequest $request)
    {
        return $this->createCurrencyAsync($request)->wait();
    }

    public function createCurrencyAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateCurrencyRestRequest $request)
    {
        return $this->callOperationAsync('CreateCurrency', $request);
    }

    public function getEmployees(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetEmployeesRestRequest $request)
    {
        return $this->getEmployeesAsync($request)->wait();
    }

    public function getEmployeesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetEmployeesRestRequest $request)
    {
        return $this->callOperationAsync('GetEmployees', $request);
    }

    public function createEmployees(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateEmployeesRestRequest $request)
    {
        return $this->createEmployeesAsync($request)->wait();
    }

    public function createEmployeesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateEmployeesRestRequest $request)
    {
        return $this->callOperationAsync('CreateEmployees', $request);
    }

    public function updateOrCreateEmployees(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateEmployeesRestRequest $request)
    {
        return $this->updateOrCreateEmployeesAsync($request)->wait();
    }

    public function updateOrCreateEmployeesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateEmployeesRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateEmployees', $request);
    }

    public function getEmployee(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetEmployeeRestRequest $request)
    {
        return $this->getEmployeeAsync($request)->wait();
    }

    public function getEmployeeAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetEmployeeRestRequest $request)
    {
        return $this->callOperationAsync('GetEmployee', $request);
    }

    public function getExpenseClaims(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimsRestRequest $request)
    {
        return $this->getExpenseClaimsAsync($request)->wait();
    }

    public function getExpenseClaimsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimsRestRequest $request)
    {
        return $this->callOperationAsync('GetExpenseClaims', $request);
    }

    public function createExpenseClaims(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateExpenseClaimsRestRequest $request)
    {
        return $this->createExpenseClaimsAsync($request)->wait();
    }

    public function createExpenseClaimsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateExpenseClaimsRestRequest $request)
    {
        return $this->callOperationAsync('CreateExpenseClaims', $request);
    }

    public function getExpenseClaim(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimRestRequest $request)
    {
        return $this->getExpenseClaimAsync($request)->wait();
    }

    public function getExpenseClaimAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimRestRequest $request)
    {
        return $this->callOperationAsync('GetExpenseClaim', $request);
    }

    public function updateExpenseClaim(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateExpenseClaimRestRequest $request)
    {
        return $this->updateExpenseClaimAsync($request)->wait();
    }

    public function updateExpenseClaimAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateExpenseClaimRestRequest $request)
    {
        return $this->callOperationAsync('UpdateExpenseClaim', $request);
    }

    public function getExpenseClaimHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimHistoryRestRequest $request)
    {
        return $this->getExpenseClaimHistoryAsync($request)->wait();
    }

    public function getExpenseClaimHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetExpenseClaimHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetExpenseClaimHistory', $request);
    }

    public function createExpenseClaimHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateExpenseClaimHistoryRestRequest $request)
    {
        return $this->createExpenseClaimHistoryAsync($request)->wait();
    }

    public function createExpenseClaimHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateExpenseClaimHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateExpenseClaimHistory', $request);
    }

    public function getInvoices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoicesRestRequest $request)
    {
        return $this->getInvoicesAsync($request)->wait();
    }

    public function getInvoicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoicesRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoices', $request);
    }

    public function createInvoices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoicesRestRequest $request)
    {
        return $this->createInvoicesAsync($request)->wait();
    }

    public function createInvoicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoicesRestRequest $request)
    {
        return $this->callOperationAsync('CreateInvoices', $request);
    }

    public function updateOrCreateInvoices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateInvoicesRestRequest $request)
    {
        return $this->updateOrCreateInvoicesAsync($request)->wait();
    }

    public function updateOrCreateInvoicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateInvoicesRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateInvoices', $request);
    }

    public function getInvoice(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceRestRequest $request)
    {
        return $this->getInvoiceAsync($request)->wait();
    }

    public function getInvoiceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoice', $request);
    }

    public function updateInvoice(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateInvoiceRestRequest $request)
    {
        return $this->updateInvoiceAsync($request)->wait();
    }

    public function updateInvoiceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateInvoiceRestRequest $request)
    {
        return $this->callOperationAsync('UpdateInvoice', $request);
    }

    public function getInvoiceAsPdf(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAsPdfRestRequest $request)
    {
        return $this->getInvoiceAsPdfAsync($request)->wait();
    }

    public function getInvoiceAsPdfAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAsPdfRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoiceAsPdf', $request);
    }

    public function getInvoiceAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentsRestRequest $request)
    {
        return $this->getInvoiceAttachmentsAsync($request)->wait();
    }

    public function getInvoiceAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoiceAttachments', $request);
    }

    public function getInvoiceAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentByIdRestRequest $request)
    {
        return $this->getInvoiceAttachmentByIdAsync($request)->wait();
    }

    public function getInvoiceAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoiceAttachmentById', $request);
    }

    public function getInvoiceAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->getInvoiceAttachmentByFileNameAsync($request)->wait();
    }

    public function getInvoiceAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoiceAttachmentByFileName', $request);
    }

    public function updateInvoiceAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->updateInvoiceAttachmentByFileNameAsync($request)->wait();
    }

    public function updateInvoiceAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateInvoiceAttachmentByFileName', $request);
    }

    public function createInvoiceAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->createInvoiceAttachmentByFileNameAsync($request)->wait();
    }

    public function createInvoiceAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateInvoiceAttachmentByFileName', $request);
    }

    public function getOnlineInvoice(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOnlineInvoiceRestRequest $request)
    {
        return $this->getOnlineInvoiceAsync($request)->wait();
    }

    public function getOnlineInvoiceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOnlineInvoiceRestRequest $request)
    {
        return $this->callOperationAsync('GetOnlineInvoice', $request);
    }

    public function emailInvoice(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\EmailInvoiceRestRequest $request)
    {
        return $this->emailInvoiceAsync($request)->wait();
    }

    public function emailInvoiceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\EmailInvoiceRestRequest $request)
    {
        return $this->callOperationAsync('EmailInvoice', $request);
    }

    public function getInvoiceHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceHistoryRestRequest $request)
    {
        return $this->getInvoiceHistoryAsync($request)->wait();
    }

    public function getInvoiceHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoiceHistory', $request);
    }

    public function createInvoiceHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoiceHistoryRestRequest $request)
    {
        return $this->createInvoiceHistoryAsync($request)->wait();
    }

    public function createInvoiceHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateInvoiceHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateInvoiceHistory', $request);
    }

    public function getInvoiceReminders(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceRemindersRestRequest $request)
    {
        return $this->getInvoiceRemindersAsync($request)->wait();
    }

    public function getInvoiceRemindersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetInvoiceRemindersRestRequest $request)
    {
        return $this->callOperationAsync('GetInvoiceReminders', $request);
    }

    public function getItems(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemsRestRequest $request)
    {
        return $this->getItemsAsync($request)->wait();
    }

    public function getItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemsRestRequest $request)
    {
        return $this->callOperationAsync('GetItems', $request);
    }

    public function createItems(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateItemsRestRequest $request)
    {
        return $this->createItemsAsync($request)->wait();
    }

    public function createItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateItemsRestRequest $request)
    {
        return $this->callOperationAsync('CreateItems', $request);
    }

    public function updateOrCreateItems(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateItemsRestRequest $request)
    {
        return $this->updateOrCreateItemsAsync($request)->wait();
    }

    public function updateOrCreateItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateItemsRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateItems', $request);
    }

    public function getItem(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemRestRequest $request)
    {
        return $this->getItemAsync($request)->wait();
    }

    public function getItemAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemRestRequest $request)
    {
        return $this->callOperationAsync('GetItem', $request);
    }

    public function updateItem(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateItemRestRequest $request)
    {
        return $this->updateItemAsync($request)->wait();
    }

    public function updateItemAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateItemRestRequest $request)
    {
        return $this->callOperationAsync('UpdateItem', $request);
    }

    public function deleteItem(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteItemRestRequest $request)
    {
        return $this->deleteItemAsync($request)->wait();
    }

    public function deleteItemAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteItemRestRequest $request)
    {
        return $this->callOperationAsync('DeleteItem', $request);
    }

    public function getItemHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemHistoryRestRequest $request)
    {
        return $this->getItemHistoryAsync($request)->wait();
    }

    public function getItemHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetItemHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetItemHistory', $request);
    }

    public function createItemHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateItemHistoryRestRequest $request)
    {
        return $this->createItemHistoryAsync($request)->wait();
    }

    public function createItemHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateItemHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateItemHistory', $request);
    }

    public function getJournals(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetJournalsRestRequest $request)
    {
        return $this->getJournalsAsync($request)->wait();
    }

    public function getJournalsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetJournalsRestRequest $request)
    {
        return $this->callOperationAsync('GetJournals', $request);
    }

    public function getJournal(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetJournalRestRequest $request)
    {
        return $this->getJournalAsync($request)->wait();
    }

    public function getJournalAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetJournalRestRequest $request)
    {
        return $this->callOperationAsync('GetJournal', $request);
    }

    public function getLinkedTransactions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetLinkedTransactionsRestRequest $request)
    {
        return $this->getLinkedTransactionsAsync($request)->wait();
    }

    public function getLinkedTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetLinkedTransactionsRestRequest $request)
    {
        return $this->callOperationAsync('GetLinkedTransactions', $request);
    }

    public function createLinkedTransaction(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateLinkedTransactionRestRequest $request)
    {
        return $this->createLinkedTransactionAsync($request)->wait();
    }

    public function createLinkedTransactionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateLinkedTransactionRestRequest $request)
    {
        return $this->callOperationAsync('CreateLinkedTransaction', $request);
    }

    public function getLinkedTransaction(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetLinkedTransactionRestRequest $request)
    {
        return $this->getLinkedTransactionAsync($request)->wait();
    }

    public function getLinkedTransactionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetLinkedTransactionRestRequest $request)
    {
        return $this->callOperationAsync('GetLinkedTransaction', $request);
    }

    public function updateLinkedTransaction(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateLinkedTransactionRestRequest $request)
    {
        return $this->updateLinkedTransactionAsync($request)->wait();
    }

    public function updateLinkedTransactionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateLinkedTransactionRestRequest $request)
    {
        return $this->callOperationAsync('UpdateLinkedTransaction', $request);
    }

    public function deleteLinkedTransaction(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteLinkedTransactionRestRequest $request)
    {
        return $this->deleteLinkedTransactionAsync($request)->wait();
    }

    public function deleteLinkedTransactionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteLinkedTransactionRestRequest $request)
    {
        return $this->callOperationAsync('DeleteLinkedTransaction', $request);
    }

    public function getManualJournals(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalsRestRequest $request)
    {
        return $this->getManualJournalsAsync($request)->wait();
    }

    public function getManualJournalsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalsRestRequest $request)
    {
        return $this->callOperationAsync('GetManualJournals', $request);
    }

    public function createManualJournals(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalsRestRequest $request)
    {
        return $this->createManualJournalsAsync($request)->wait();
    }

    public function createManualJournalsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalsRestRequest $request)
    {
        return $this->callOperationAsync('CreateManualJournals', $request);
    }

    public function updateOrCreateManualJournals(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateManualJournalsRestRequest $request)
    {
        return $this->updateOrCreateManualJournalsAsync($request)->wait();
    }

    public function updateOrCreateManualJournalsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateManualJournalsRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateManualJournals', $request);
    }

    public function getManualJournal(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalRestRequest $request)
    {
        return $this->getManualJournalAsync($request)->wait();
    }

    public function getManualJournalAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalRestRequest $request)
    {
        return $this->callOperationAsync('GetManualJournal', $request);
    }

    public function updateManualJournal(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateManualJournalRestRequest $request)
    {
        return $this->updateManualJournalAsync($request)->wait();
    }

    public function updateManualJournalAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateManualJournalRestRequest $request)
    {
        return $this->callOperationAsync('UpdateManualJournal', $request);
    }

    public function getManualJournalAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentsRestRequest $request)
    {
        return $this->getManualJournalAttachmentsAsync($request)->wait();
    }

    public function getManualJournalAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetManualJournalAttachments', $request);
    }

    public function getManualJournalAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentByIdRestRequest $request)
    {
        return $this->getManualJournalAttachmentByIdAsync($request)->wait();
    }

    public function getManualJournalAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetManualJournalAttachmentById', $request);
    }

    public function getManualJournalAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentByFileNameRestRequest $request)
    {
        return $this->getManualJournalAttachmentByFileNameAsync($request)->wait();
    }

    public function getManualJournalAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetManualJournalAttachmentByFileName', $request);
    }

    public function updateManualJournalAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateManualJournalAttachmentByFileNameRestRequest $request)
    {
        return $this->updateManualJournalAttachmentByFileNameAsync($request)->wait();
    }

    public function updateManualJournalAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateManualJournalAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateManualJournalAttachmentByFileName', $request);
    }

    public function createManualJournalAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalAttachmentByFileNameRestRequest $request)
    {
        return $this->createManualJournalAttachmentByFileNameAsync($request)->wait();
    }

    public function createManualJournalAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateManualJournalAttachmentByFileName', $request);
    }

    public function getManualJournalsHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalsHistoryRestRequest $request)
    {
        return $this->getManualJournalsHistoryAsync($request)->wait();
    }

    public function getManualJournalsHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetManualJournalsHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetManualJournalsHistory', $request);
    }

    public function createManualJournalHistoryRecord(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalHistoryRecordRestRequest $request)
    {
        return $this->createManualJournalHistoryRecordAsync($request)->wait();
    }

    public function createManualJournalHistoryRecordAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateManualJournalHistoryRecordRestRequest $request)
    {
        return $this->callOperationAsync('CreateManualJournalHistoryRecord', $request);
    }

    public function getOrganisations(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationsRestRequest $request)
    {
        return $this->getOrganisationsAsync($request)->wait();
    }

    public function getOrganisationsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationsRestRequest $request)
    {
        return $this->callOperationAsync('GetOrganisations', $request);
    }

    public function getOrganisationActions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationActionsRestRequest $request)
    {
        return $this->getOrganisationActionsAsync($request)->wait();
    }

    public function getOrganisationActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationActionsRestRequest $request)
    {
        return $this->callOperationAsync('GetOrganisationActions', $request);
    }

    public function getOrganisationCISSettings(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationCISSettingsRestRequest $request)
    {
        return $this->getOrganisationCISSettingsAsync($request)->wait();
    }

    public function getOrganisationCISSettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationCISSettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetOrganisationCISSettings', $request);
    }

    public function getOverpayments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentsRestRequest $request)
    {
        return $this->getOverpaymentsAsync($request)->wait();
    }

    public function getOverpaymentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentsRestRequest $request)
    {
        return $this->callOperationAsync('GetOverpayments', $request);
    }

    public function getOverpayment(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentRestRequest $request)
    {
        return $this->getOverpaymentAsync($request)->wait();
    }

    public function getOverpaymentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentRestRequest $request)
    {
        return $this->callOperationAsync('GetOverpayment', $request);
    }

    public function createOverpaymentAllocations(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateOverpaymentAllocationsRestRequest $request)
    {
        return $this->createOverpaymentAllocationsAsync($request)->wait();
    }

    public function createOverpaymentAllocationsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateOverpaymentAllocationsRestRequest $request)
    {
        return $this->callOperationAsync('CreateOverpaymentAllocations', $request);
    }

    public function getOverpaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentHistoryRestRequest $request)
    {
        return $this->getOverpaymentHistoryAsync($request)->wait();
    }

    public function getOverpaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOverpaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetOverpaymentHistory', $request);
    }

    public function createOverpaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateOverpaymentHistoryRestRequest $request)
    {
        return $this->createOverpaymentHistoryAsync($request)->wait();
    }

    public function createOverpaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateOverpaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateOverpaymentHistory', $request);
    }

    public function getPayments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentsRestRequest $request)
    {
        return $this->getPaymentsAsync($request)->wait();
    }

    public function getPaymentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentsRestRequest $request)
    {
        return $this->callOperationAsync('GetPayments', $request);
    }

    public function createPayments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentsRestRequest $request)
    {
        return $this->createPaymentsAsync($request)->wait();
    }

    public function createPaymentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentsRestRequest $request)
    {
        return $this->callOperationAsync('CreatePayments', $request);
    }

    public function createPayment(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentRestRequest $request)
    {
        return $this->createPaymentAsync($request)->wait();
    }

    public function createPaymentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentRestRequest $request)
    {
        return $this->callOperationAsync('CreatePayment', $request);
    }

    public function getPayment(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentRestRequest $request)
    {
        return $this->getPaymentAsync($request)->wait();
    }

    public function getPaymentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentRestRequest $request)
    {
        return $this->callOperationAsync('GetPayment', $request);
    }

    public function deletePayment(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeletePaymentRestRequest $request)
    {
        return $this->deletePaymentAsync($request)->wait();
    }

    public function deletePaymentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeletePaymentRestRequest $request)
    {
        return $this->callOperationAsync('DeletePayment', $request);
    }

    public function getPaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentHistoryRestRequest $request)
    {
        return $this->getPaymentHistoryAsync($request)->wait();
    }

    public function getPaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetPaymentHistory', $request);
    }

    public function createPaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentHistoryRestRequest $request)
    {
        return $this->createPaymentHistoryAsync($request)->wait();
    }

    public function createPaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreatePaymentHistory', $request);
    }

    public function getPaymentServices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentServicesRestRequest $request)
    {
        return $this->getPaymentServicesAsync($request)->wait();
    }

    public function getPaymentServicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPaymentServicesRestRequest $request)
    {
        return $this->callOperationAsync('GetPaymentServices', $request);
    }

    public function createPaymentService(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentServiceRestRequest $request)
    {
        return $this->createPaymentServiceAsync($request)->wait();
    }

    public function createPaymentServiceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePaymentServiceRestRequest $request)
    {
        return $this->callOperationAsync('CreatePaymentService', $request);
    }

    public function getPrepayments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentsRestRequest $request)
    {
        return $this->getPrepaymentsAsync($request)->wait();
    }

    public function getPrepaymentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentsRestRequest $request)
    {
        return $this->callOperationAsync('GetPrepayments', $request);
    }

    public function getPrepayment(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentRestRequest $request)
    {
        return $this->getPrepaymentAsync($request)->wait();
    }

    public function getPrepaymentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentRestRequest $request)
    {
        return $this->callOperationAsync('GetPrepayment', $request);
    }

    public function createPrepaymentAllocations(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePrepaymentAllocationsRestRequest $request)
    {
        return $this->createPrepaymentAllocationsAsync($request)->wait();
    }

    public function createPrepaymentAllocationsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePrepaymentAllocationsRestRequest $request)
    {
        return $this->callOperationAsync('CreatePrepaymentAllocations', $request);
    }

    public function getPrepaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentHistoryRestRequest $request)
    {
        return $this->getPrepaymentHistoryAsync($request)->wait();
    }

    public function getPrepaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPrepaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetPrepaymentHistory', $request);
    }

    public function createPrepaymentHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePrepaymentHistoryRestRequest $request)
    {
        return $this->createPrepaymentHistoryAsync($request)->wait();
    }

    public function createPrepaymentHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePrepaymentHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreatePrepaymentHistory', $request);
    }

    public function getPurchaseOrders(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestRequest $request)
    {
        return $this->getPurchaseOrdersAsync($request)->wait();
    }

    public function getPurchaseOrdersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrders', $request);
    }

    public function createPurchaseOrders(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrdersRestRequest $request)
    {
        return $this->createPurchaseOrdersAsync($request)->wait();
    }

    public function createPurchaseOrdersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrdersRestRequest $request)
    {
        return $this->callOperationAsync('CreatePurchaseOrders', $request);
    }

    public function updateOrCreatePurchaseOrders(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreatePurchaseOrdersRestRequest $request)
    {
        return $this->updateOrCreatePurchaseOrdersAsync($request)->wait();
    }

    public function updateOrCreatePurchaseOrdersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreatePurchaseOrdersRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreatePurchaseOrders', $request);
    }

    public function getPurchaseOrderAsPdf(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAsPdfRestRequest $request)
    {
        return $this->getPurchaseOrderAsPdfAsync($request)->wait();
    }

    public function getPurchaseOrderAsPdfAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAsPdfRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrderAsPdf', $request);
    }

    public function getPurchaseOrder(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderRestRequest $request)
    {
        return $this->getPurchaseOrderAsync($request)->wait();
    }

    public function getPurchaseOrderAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrder', $request);
    }

    public function updatePurchaseOrder(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdatePurchaseOrderRestRequest $request)
    {
        return $this->updatePurchaseOrderAsync($request)->wait();
    }

    public function updatePurchaseOrderAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdatePurchaseOrderRestRequest $request)
    {
        return $this->callOperationAsync('UpdatePurchaseOrder', $request);
    }

    public function getPurchaseOrderByNumber(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderByNumberRestRequest $request)
    {
        return $this->getPurchaseOrderByNumberAsync($request)->wait();
    }

    public function getPurchaseOrderByNumberAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderByNumberRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrderByNumber', $request);
    }

    public function getPurchaseOrderHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestRequest $request)
    {
        return $this->getPurchaseOrderHistoryAsync($request)->wait();
    }

    public function getPurchaseOrderHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrderHistory', $request);
    }

    public function createPurchaseOrderHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrderHistoryRestRequest $request)
    {
        return $this->createPurchaseOrderHistoryAsync($request)->wait();
    }

    public function createPurchaseOrderHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrderHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreatePurchaseOrderHistory', $request);
    }

    public function getPurchaseOrderAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestRequest $request)
    {
        return $this->getPurchaseOrderAttachmentsAsync($request)->wait();
    }

    public function getPurchaseOrderAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrderAttachments', $request);
    }

    public function getPurchaseOrderAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentByIdRestRequest $request)
    {
        return $this->getPurchaseOrderAttachmentByIdAsync($request)->wait();
    }

    public function getPurchaseOrderAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrderAttachmentById', $request);
    }

    public function getPurchaseOrder≠AttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrder≠AttachmentByFileNameRestRequest $request)
    {
        return $this->getPurchaseOrder≠AttachmentByFileNameAsync($request)->wait();
    }

    public function getPurchaseOrder≠AttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrder≠AttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetPurchaseOrder≠AttachmentByFileName', $request);
    }

    public function updatePurchaseOrderAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdatePurchaseOrderAttachmentByFileNameRestRequest $request)
    {
        return $this->updatePurchaseOrderAttachmentByFileNameAsync($request)->wait();
    }

    public function updatePurchaseOrderAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdatePurchaseOrderAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdatePurchaseOrderAttachmentByFileName', $request);
    }

    public function createPurchaseOrderAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrderAttachmentByFileNameRestRequest $request)
    {
        return $this->createPurchaseOrderAttachmentByFileNameAsync($request)->wait();
    }

    public function createPurchaseOrderAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreatePurchaseOrderAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreatePurchaseOrderAttachmentByFileName', $request);
    }

    public function getQuotes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuotesRestRequest $request)
    {
        return $this->getQuotesAsync($request)->wait();
    }

    public function getQuotesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuotesRestRequest $request)
    {
        return $this->callOperationAsync('GetQuotes', $request);
    }

    public function createQuotes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuotesRestRequest $request)
    {
        return $this->createQuotesAsync($request)->wait();
    }

    public function createQuotesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuotesRestRequest $request)
    {
        return $this->callOperationAsync('CreateQuotes', $request);
    }

    public function updateOrCreateQuotes(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateQuotesRestRequest $request)
    {
        return $this->updateOrCreateQuotesAsync($request)->wait();
    }

    public function updateOrCreateQuotesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateOrCreateQuotesRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOrCreateQuotes', $request);
    }

    public function getQuote(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteRestRequest $request)
    {
        return $this->getQuoteAsync($request)->wait();
    }

    public function getQuoteAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteRestRequest $request)
    {
        return $this->callOperationAsync('GetQuote', $request);
    }

    public function updateQuote(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateQuoteRestRequest $request)
    {
        return $this->updateQuoteAsync($request)->wait();
    }

    public function updateQuoteAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateQuoteRestRequest $request)
    {
        return $this->callOperationAsync('UpdateQuote', $request);
    }

    public function getQuoteHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteHistoryRestRequest $request)
    {
        return $this->getQuoteHistoryAsync($request)->wait();
    }

    public function getQuoteHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetQuoteHistory', $request);
    }

    public function createQuoteHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuoteHistoryRestRequest $request)
    {
        return $this->createQuoteHistoryAsync($request)->wait();
    }

    public function createQuoteHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuoteHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateQuoteHistory', $request);
    }

    public function getQuoteAsPdf(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAsPdfRestRequest $request)
    {
        return $this->getQuoteAsPdfAsync($request)->wait();
    }

    public function getQuoteAsPdfAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAsPdfRestRequest $request)
    {
        return $this->callOperationAsync('GetQuoteAsPdf', $request);
    }

    public function getQuoteAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentsRestRequest $request)
    {
        return $this->getQuoteAttachmentsAsync($request)->wait();
    }

    public function getQuoteAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetQuoteAttachments', $request);
    }

    public function getQuoteAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentByIdRestRequest $request)
    {
        return $this->getQuoteAttachmentByIdAsync($request)->wait();
    }

    public function getQuoteAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetQuoteAttachmentById', $request);
    }

    public function getQuoteAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentByFileNameRestRequest $request)
    {
        return $this->getQuoteAttachmentByFileNameAsync($request)->wait();
    }

    public function getQuoteAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetQuoteAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetQuoteAttachmentByFileName', $request);
    }

    public function updateQuoteAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateQuoteAttachmentByFileNameRestRequest $request)
    {
        return $this->updateQuoteAttachmentByFileNameAsync($request)->wait();
    }

    public function updateQuoteAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateQuoteAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateQuoteAttachmentByFileName', $request);
    }

    public function createQuoteAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuoteAttachmentByFileNameRestRequest $request)
    {
        return $this->createQuoteAttachmentByFileNameAsync($request)->wait();
    }

    public function createQuoteAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateQuoteAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateQuoteAttachmentByFileName', $request);
    }

    public function getReceipts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptsRestRequest $request)
    {
        return $this->getReceiptsAsync($request)->wait();
    }

    public function getReceiptsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptsRestRequest $request)
    {
        return $this->callOperationAsync('GetReceipts', $request);
    }

    public function createReceipt(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptRestRequest $request)
    {
        return $this->createReceiptAsync($request)->wait();
    }

    public function createReceiptAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptRestRequest $request)
    {
        return $this->callOperationAsync('CreateReceipt', $request);
    }

    public function getReceipt(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptRestRequest $request)
    {
        return $this->getReceiptAsync($request)->wait();
    }

    public function getReceiptAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptRestRequest $request)
    {
        return $this->callOperationAsync('GetReceipt', $request);
    }

    public function updateReceipt(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateReceiptRestRequest $request)
    {
        return $this->updateReceiptAsync($request)->wait();
    }

    public function updateReceiptAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateReceiptRestRequest $request)
    {
        return $this->callOperationAsync('UpdateReceipt', $request);
    }

    public function getReceiptAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentsRestRequest $request)
    {
        return $this->getReceiptAttachmentsAsync($request)->wait();
    }

    public function getReceiptAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetReceiptAttachments', $request);
    }

    public function getReceiptAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentByIdRestRequest $request)
    {
        return $this->getReceiptAttachmentByIdAsync($request)->wait();
    }

    public function getReceiptAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetReceiptAttachmentById', $request);
    }

    public function getReceiptAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentByFileNameRestRequest $request)
    {
        return $this->getReceiptAttachmentByFileNameAsync($request)->wait();
    }

    public function getReceiptAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetReceiptAttachmentByFileName', $request);
    }

    public function updateReceiptAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateReceiptAttachmentByFileNameRestRequest $request)
    {
        return $this->updateReceiptAttachmentByFileNameAsync($request)->wait();
    }

    public function updateReceiptAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateReceiptAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateReceiptAttachmentByFileName', $request);
    }

    public function createReceiptAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptAttachmentByFileNameRestRequest $request)
    {
        return $this->createReceiptAttachmentByFileNameAsync($request)->wait();
    }

    public function createReceiptAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateReceiptAttachmentByFileName', $request);
    }

    public function getReceiptHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptHistoryRestRequest $request)
    {
        return $this->getReceiptHistoryAsync($request)->wait();
    }

    public function getReceiptHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReceiptHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetReceiptHistory', $request);
    }

    public function createReceiptHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptHistoryRestRequest $request)
    {
        return $this->createReceiptHistoryAsync($request)->wait();
    }

    public function createReceiptHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateReceiptHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateReceiptHistory', $request);
    }

    public function getRepeatingInvoices(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoicesRestRequest $request)
    {
        return $this->getRepeatingInvoicesAsync($request)->wait();
    }

    public function getRepeatingInvoicesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoicesRestRequest $request)
    {
        return $this->callOperationAsync('GetRepeatingInvoices', $request);
    }

    public function getRepeatingInvoice(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceRestRequest $request)
    {
        return $this->getRepeatingInvoiceAsync($request)->wait();
    }

    public function getRepeatingInvoiceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceRestRequest $request)
    {
        return $this->callOperationAsync('GetRepeatingInvoice', $request);
    }

    public function getRepeatingInvoiceAttachments(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentsRestRequest $request)
    {
        return $this->getRepeatingInvoiceAttachmentsAsync($request)->wait();
    }

    public function getRepeatingInvoiceAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('GetRepeatingInvoiceAttachments', $request);
    }

    public function getRepeatingInvoiceAttachmentById(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentByIdRestRequest $request)
    {
        return $this->getRepeatingInvoiceAttachmentByIdAsync($request)->wait();
    }

    public function getRepeatingInvoiceAttachmentByIdAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentByIdRestRequest $request)
    {
        return $this->callOperationAsync('GetRepeatingInvoiceAttachmentById', $request);
    }

    public function getRepeatingInvoiceAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->getRepeatingInvoiceAttachmentByFileNameAsync($request)->wait();
    }

    public function getRepeatingInvoiceAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('GetRepeatingInvoiceAttachmentByFileName', $request);
    }

    public function updateRepeatingInvoiceAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateRepeatingInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->updateRepeatingInvoiceAttachmentByFileNameAsync($request)->wait();
    }

    public function updateRepeatingInvoiceAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateRepeatingInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('UpdateRepeatingInvoiceAttachmentByFileName', $request);
    }

    public function createRepeatingInvoiceAttachmentByFileName(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateRepeatingInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->createRepeatingInvoiceAttachmentByFileNameAsync($request)->wait();
    }

    public function createRepeatingInvoiceAttachmentByFileNameAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateRepeatingInvoiceAttachmentByFileNameRestRequest $request)
    {
        return $this->callOperationAsync('CreateRepeatingInvoiceAttachmentByFileName', $request);
    }

    public function getRepeatingInvoiceHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceHistoryRestRequest $request)
    {
        return $this->getRepeatingInvoiceHistoryAsync($request)->wait();
    }

    public function getRepeatingInvoiceHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetRepeatingInvoiceHistoryRestRequest $request)
    {
        return $this->callOperationAsync('GetRepeatingInvoiceHistory', $request);
    }

    public function createRepeatingInvoiceHistory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateRepeatingInvoiceHistoryRestRequest $request)
    {
        return $this->createRepeatingInvoiceHistoryAsync($request)->wait();
    }

    public function createRepeatingInvoiceHistoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateRepeatingInvoiceHistoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateRepeatingInvoiceHistory', $request);
    }

    public function getReportTenNinetyNine(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportTenNinetyNineRestRequest $request)
    {
        return $this->getReportTenNinetyNineAsync($request)->wait();
    }

    public function getReportTenNinetyNineAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportTenNinetyNineRestRequest $request)
    {
        return $this->callOperationAsync('GetReportTenNinetyNine', $request);
    }

    public function getReportAgedPayablesByContact(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportAgedPayablesByContactRestRequest $request)
    {
        return $this->getReportAgedPayablesByContactAsync($request)->wait();
    }

    public function getReportAgedPayablesByContactAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportAgedPayablesByContactRestRequest $request)
    {
        return $this->callOperationAsync('GetReportAgedPayablesByContact', $request);
    }

    public function getReportAgedReceivablesByContact(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportAgedReceivablesByContactRestRequest $request)
    {
        return $this->getReportAgedReceivablesByContactAsync($request)->wait();
    }

    public function getReportAgedReceivablesByContactAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportAgedReceivablesByContactRestRequest $request)
    {
        return $this->callOperationAsync('GetReportAgedReceivablesByContact', $request);
    }

    public function getReportBalanceSheet(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBalanceSheetRestRequest $request)
    {
        return $this->getReportBalanceSheetAsync($request)->wait();
    }

    public function getReportBalanceSheetAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBalanceSheetRestRequest $request)
    {
        return $this->callOperationAsync('GetReportBalanceSheet', $request);
    }

    public function getReportBankSummary(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBankSummaryRestRequest $request)
    {
        return $this->getReportBankSummaryAsync($request)->wait();
    }

    public function getReportBankSummaryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBankSummaryRestRequest $request)
    {
        return $this->callOperationAsync('GetReportBankSummary', $request);
    }

    public function getReportBASorGSTList(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBASorGSTListRestRequest $request)
    {
        return $this->getReportBASorGSTListAsync($request)->wait();
    }

    public function getReportBASorGSTListAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBASorGSTListRestRequest $request)
    {
        return $this->callOperationAsync('GetReportBASorGSTList', $request);
    }

    public function getReportBASorGST(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBASorGSTRestRequest $request)
    {
        return $this->getReportBASorGSTAsync($request)->wait();
    }

    public function getReportBASorGSTAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBASorGSTRestRequest $request)
    {
        return $this->callOperationAsync('GetReportBASorGST', $request);
    }

    public function getReportBudgetSummary(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBudgetSummaryRestRequest $request)
    {
        return $this->getReportBudgetSummaryAsync($request)->wait();
    }

    public function getReportBudgetSummaryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportBudgetSummaryRestRequest $request)
    {
        return $this->callOperationAsync('GetReportBudgetSummary', $request);
    }

    public function getReportExecutiveSummary(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportExecutiveSummaryRestRequest $request)
    {
        return $this->getReportExecutiveSummaryAsync($request)->wait();
    }

    public function getReportExecutiveSummaryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportExecutiveSummaryRestRequest $request)
    {
        return $this->callOperationAsync('GetReportExecutiveSummary', $request);
    }

    public function getReportProfitAndLoss(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportProfitAndLossRestRequest $request)
    {
        return $this->getReportProfitAndLossAsync($request)->wait();
    }

    public function getReportProfitAndLossAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportProfitAndLossRestRequest $request)
    {
        return $this->callOperationAsync('GetReportProfitAndLoss', $request);
    }

    public function getReportTrialBalance(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportTrialBalanceRestRequest $request)
    {
        return $this->getReportTrialBalanceAsync($request)->wait();
    }

    public function getReportTrialBalanceAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetReportTrialBalanceRestRequest $request)
    {
        return $this->callOperationAsync('GetReportTrialBalance', $request);
    }

    public function postSetup(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\PostSetupRestRequest $request)
    {
        return $this->postSetupAsync($request)->wait();
    }

    public function postSetupAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\PostSetupRestRequest $request)
    {
        return $this->callOperationAsync('PostSetup', $request);
    }

    public function getTaxRates(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTaxRatesRestRequest $request)
    {
        return $this->getTaxRatesAsync($request)->wait();
    }

    public function getTaxRatesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTaxRatesRestRequest $request)
    {
        return $this->callOperationAsync('GetTaxRates', $request);
    }

    public function createTaxRates(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTaxRatesRestRequest $request)
    {
        return $this->createTaxRatesAsync($request)->wait();
    }

    public function createTaxRatesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTaxRatesRestRequest $request)
    {
        return $this->callOperationAsync('CreateTaxRates', $request);
    }

    public function updateTaxRate(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTaxRateRestRequest $request)
    {
        return $this->updateTaxRateAsync($request)->wait();
    }

    public function updateTaxRateAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTaxRateRestRequest $request)
    {
        return $this->callOperationAsync('UpdateTaxRate', $request);
    }

    public function getTrackingCategories(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTrackingCategoriesRestRequest $request)
    {
        return $this->getTrackingCategoriesAsync($request)->wait();
    }

    public function getTrackingCategoriesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTrackingCategoriesRestRequest $request)
    {
        return $this->callOperationAsync('GetTrackingCategories', $request);
    }

    public function createTrackingCategory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTrackingCategoryRestRequest $request)
    {
        return $this->createTrackingCategoryAsync($request)->wait();
    }

    public function createTrackingCategoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTrackingCategoryRestRequest $request)
    {
        return $this->callOperationAsync('CreateTrackingCategory', $request);
    }

    public function getTrackingCategory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTrackingCategoryRestRequest $request)
    {
        return $this->getTrackingCategoryAsync($request)->wait();
    }

    public function getTrackingCategoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTrackingCategoryRestRequest $request)
    {
        return $this->callOperationAsync('GetTrackingCategory', $request);
    }

    public function updateTrackingCategory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTrackingCategoryRestRequest $request)
    {
        return $this->updateTrackingCategoryAsync($request)->wait();
    }

    public function updateTrackingCategoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTrackingCategoryRestRequest $request)
    {
        return $this->callOperationAsync('UpdateTrackingCategory', $request);
    }

    public function deleteTrackingCategory(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteTrackingCategoryRestRequest $request)
    {
        return $this->deleteTrackingCategoryAsync($request)->wait();
    }

    public function deleteTrackingCategoryAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteTrackingCategoryRestRequest $request)
    {
        return $this->callOperationAsync('DeleteTrackingCategory', $request);
    }

    public function createTrackingOptions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTrackingOptionsRestRequest $request)
    {
        return $this->createTrackingOptionsAsync($request)->wait();
    }

    public function createTrackingOptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\CreateTrackingOptionsRestRequest $request)
    {
        return $this->callOperationAsync('CreateTrackingOptions', $request);
    }

    public function updateTrackingOptions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTrackingOptionsRestRequest $request)
    {
        return $this->updateTrackingOptionsAsync($request)->wait();
    }

    public function updateTrackingOptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\UpdateTrackingOptionsRestRequest $request)
    {
        return $this->callOperationAsync('UpdateTrackingOptions', $request);
    }

    public function deleteTrackingOptions(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteTrackingOptionsRestRequest $request)
    {
        return $this->deleteTrackingOptionsAsync($request)->wait();
    }

    public function deleteTrackingOptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\DeleteTrackingOptionsRestRequest $request)
    {
        return $this->callOperationAsync('DeleteTrackingOptions', $request);
    }

    public function getUsers(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUsersRestRequest $request)
    {
        return $this->getUsersAsync($request)->wait();
    }

    public function getUsersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUsersRestRequest $request)
    {
        return $this->callOperationAsync('GetUsers', $request);
    }

    public function getUser(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUserRestRequest $request)
    {
        return $this->getUserAsync($request)->wait();
    }

    public function getUserAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUserRestRequest $request)
    {
        return $this->callOperationAsync('GetUser', $request);
    }
}