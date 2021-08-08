<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema;

use Phalcon\Db\Column;

class SystemApiXeroContactsFinance
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'ContactID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'TaxNumber',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'BankAccountDetails',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'AccountsReceivableTaxType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'AccountsPayableTaxType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'SalesDefaultAccountCode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'PurchaseDefaultAccountCode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'DefaultCurrency',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}