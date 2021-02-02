<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Address;

class Type
{
    public function register($db)
    {
        $types =
            [
                '1'   =>
                    [
                        'name'          => 'Mailing/Shipping Address',
                        'status'        => 1,//Enabled
                        'address_type'  => 1,//System
                        'description'   => 'Used for mailing letters or shipping packages.',
                    ],
                '2'    =>
                    [
                        'name'          => 'Billing Address',
                        'status'        => 1,//Enabled
                        'address_type'  => 1,//System
                        'description'   => 'Used for invoices and bills, can be PO box.',
                    ]
            ];


        foreach ($types as $key => $type) {
            $db->insertAsDict(
                'basepackages_address_types',
                [
                    'name'                      => $type['name'],
                    'status'                    => $type['status'],
                    'address_type'              => $type['address_type'],
                    'description'               => $type['description']
                ]
            );
        }
    }
}