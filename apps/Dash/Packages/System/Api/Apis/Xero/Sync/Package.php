<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Schema\SystemApiXeroAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Schema\SystemApiXeroContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema\SystemApiXeroContactsAddresses;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema\SystemApiXeroContactsContactPersons;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema\SystemApiXeroContactsFinance;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema\SystemApiXeroContactsPhones;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Schema\SystemApiXeroHistory;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items\Schema\SystemApiXeroItems;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema\SystemApiXeroOrganisations;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema\SystemApiXeroOrganisationsAddresses;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema\SystemApiXeroOrganisationsFinance;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema\SystemApiXeroOrganisationsPhones;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Schema\SystemApiXeroPurchaseOrders;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Schema\SystemApiXeroPurchaseOrdersLineitems;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Package extends BasePackage
{
    public function installPackage(bool $dropTables = false)
    {
        $this->init();

        // $this->installSyncTasks();return;

        try {
            if ($dropTables) {
                // Organisations
                $this->createTable('system_api_xero_organisations', '', (new SystemApiXeroOrganisations)->columns(), $dropTables);
                $this->createTable('system_api_xero_organisations_addresses', '', (new SystemApiXeroOrganisationsAddresses)->columns(), $dropTables);
                $this->createTable('system_api_xero_organisations_finance', '', (new SystemApiXeroOrganisationsFinance)->columns(), $dropTables);
                $this->createTable('system_api_xero_organisations_phones', '', (new SystemApiXeroOrganisationsPhones)->columns(), $dropTables);
                // Attachments
                $this->createTable('system_api_xero_attachments', '', (new SystemApiXeroAttachments)->columns(), $dropTables);
                // History
                $this->createTable('system_api_xero_history', '', (new SystemApiXeroHistory)->columns(), $dropTables);
                //Contact Groups
                $this->createTable('system_api_xero_contact_groups', '', (new SystemApiXeroContactGroups)->columns(), $dropTables);
                //Contacts
                $this->createTable('system_api_xero_contacts_addresses', '', (new SystemApiXeroContactsAddresses)->columns(), $dropTables);
                $this->createTable('system_api_xero_contacts_phones', '', (new SystemApiXeroContactsPhones)->columns(), $dropTables);
                $this->createTable('system_api_xero_contacts_contact_persons', '', (new SystemApiXeroContactsContactPersons)->columns(), $dropTables);
                $this->createTable('system_api_xero_contacts_finance', '', (new SystemApiXeroContactsFinance)->columns(), $dropTables);
                $this->createTable('system_api_xero_contacts', '', (new SystemApiXeroContacts)->columns(), $dropTables);
                // Purchase Orders
                $this->createTable('system_api_xero_purchase_orders', '', (new SystemApiXeroPurchaseOrders)->columns(), $dropTables);
                $this->createTable('system_api_xero_purchase_orders_lineitems', '', (new SystemApiXeroPurchaseOrdersLineitems)->columns(), $dropTables);
                //Items
                $this->createTable('system_api_xero_items', '', (new SystemApiXeroItems)->columns(), $dropTables);
            } else {
                // Organisations
                $this->createTable('system_api_xero_organisations', '', (new SystemApiXeroOrganisations)->columns());
                $this->createTable('system_api_xero_organisations_addresses', '', (new SystemApiXeroOrganisationsAddresses)->columns());
                $this->createTable('system_api_xero_organisations_finance', '', (new SystemApiXeroOrganisationsFinance)->columns());
                $this->createTable('system_api_xero_organisations_phones', '', (new SystemApiXeroOrganisationsPhones)->columns());
                // Attachments
                $this->createTable('system_api_xero_attachments', '', (new SystemApiXeroAttachments)->columns());
                // History
                $this->createTable('system_api_xero_history', '', (new SystemApiXeroHistory)->columns());
                //Contact Groups
                $this->createTable('system_api_xero_contact_groups', '', (new SystemApiXeroContactGroups)->columns());
                //Contacts
                $this->createTable('system_api_xero_contacts_addresses', '', (new SystemApiXeroContactsAddresses)->columns());
                $this->createTable('system_api_xero_contacts_phones', '', (new SystemApiXeroContactsPhones)->columns());
                $this->createTable('system_api_xero_contacts_contact_persons', '', (new SystemApiXeroContactsContactPersons)->columns());
                $this->createTable('system_api_xero_contacts_finance', '', (new SystemApiXeroContactsFinance)->columns());
                $this->createTable('system_api_xero_contacts', '', (new SystemApiXeroContacts)->columns());
                // Purchase Orders
                $this->createTable('system_api_xero_purchase_orders', '', (new SystemApiXeroPurchaseOrders)->columns());
                $this->createTable('system_api_xero_purchase_orders_lineitems', '', (new SystemApiXeroPurchaseOrdersLineitems)->columns());
                //Items
                $this->createTable('system_api_xero_items', '', (new SystemApiXeroItems)->columns());
            }

            return true;
        } catch (\PDOException $e) {
            $this->addResponse($e->getMessage(), 1);
        }
    }

    protected function installSyncTasks()
    {
        $availableFunctions = array_keys($this->basepackages->workers->tasks->getAllFunctions());

        $functionsDir = $this->basepackages->workers->tasks->getFunctionsDir();

        $schedules = $this->basepackages->workers->schedules->schedules;

        // $this->addClassFile($functionsDir);

        var_dump($availableFunctions, $functionsDir, $schedules);die();
    }

    protected function addClassFile($functionsDir)
    {
        $file =
'<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class SyncXeroPo extends Functions
{
    public $funcName = "Sync Xero Purchase Orders";

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $this->basepackages->emailqueue->processQueue(1);

            $this->addJobResult($this->basepackages->emailqueue->packagesData, $args);

            $thisFunction->updateJobTask(3, $args);
        };
    }
}';

        $this->localContent->write(
            $functionsDir . 'SyncXeroPo.php',
            $file
        );
    }
}