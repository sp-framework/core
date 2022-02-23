<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Schema;

use Phalcon\Db\Column;

class SystemApiXeroAttachments
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
                    'baz_storage_local_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'api_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'xero_package',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'xero_package_row_id',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'AttachmentID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'FileName',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 200,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'Url',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'MimeType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 200,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'ContentLength',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'IncludeOnline',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'size'          => 3,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'resync_local',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'resync_remote',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'conflict',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'conflict_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}