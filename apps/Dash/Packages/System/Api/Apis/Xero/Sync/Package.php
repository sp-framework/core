<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema\SystemApiXeroAttachments;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        try {
            // if ($dropTables) {
                // Attachments
                // $this->createTable('system_api_xero_attachments', '', (new SystemApiXeroAttachments)->columns(), $dropTables);
                //Contacts
                // $this->createTable('system_api_xero_contacts', '', (new SystemApiXeroContacts)->columns(), $dropTables);
                // $this->createTable('system_api_xero_contacts_phones', '', (new SystemApiXeroContactsPhones)->columns(), $dropTables);
                // $this->createTable('system_api_xero_contacts_Addresses', '', (new SystemApiXeroContactsAddresses)->columns(), $dropTables);

                // $this->createTable('system_api_xero_purchase_orders', '', (new SystemApiXeroPurchaseOrders)->columns(), $dropTables);
                // $this->createTable('system_api_xero_purchase_orders_lineitems', '', (new SystemApiXeroPurchaseOrdersLineitems)->columns(), $dropTables);
                // $this->createTable('system_api_xero_purchase_orders_attachments', '', (new SystemApiXeroPurchaseOrdersAttachments)->columns(), $dropTables);
                // $this->createTable('system_api_xero_purchase_orders_history_records', '', (new SystemApiXeroPurchaseOrdersHistoryRecords)->columns(), $dropTables);
            // } else {
            //     $this->createTable('system_api_xero_purchase_orders', '', (new SystemApiXeroPurchaseOrders)->columns());
            //     $this->createTable('system_api_xero_purchase_orders_lineitems', '', (new SystemApiXeroPurchaseOrdersLineitems)->columns());
            //     $this->createTable('system_api_xero_purchase_orders_attachments', '', (new SystemApiXeroPurchaseOrdersAttachments)->columns());
            //     $this->createTable('system_api_xero_purchase_orders_history_records', '', (new SystemApiXeroPurchaseOrdersHistoryRecords)->columns());
            //     $this->createTable('system_api_xero_contacts', '', (new SystemApiXeroContacts)->columns());
            //     $this->createTable('system_api_xero_contacts_phones', '', (new SystemApiXeroContactsPhones)->columns());
            //     $this->createTable('system_api_xero_contacts_Addresses', '', (new SystemApiXeroContactsAddresses)->columns());
            // }

            return true;
        } catch (\PDOException $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }
}