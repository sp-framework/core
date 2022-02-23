<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema;

use Phalcon\Db\Column;

class SystemApiXeroOrganisationsFinance
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
                    'OrganisationID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'BaseCurrency',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'TaxNumber',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'FinancialYearEndDay',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'FinancialYearEndMonth',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'SalesTaxBasis',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'SalesTaxPeriod',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'DefaultSalesTax',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'DefaultPurchasesTax',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'PaymentTermsBillsDay',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 4,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'PaymentTermsBillsType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'PaymentTermsSalesDay',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 4,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'PaymentTermsSalesType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
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