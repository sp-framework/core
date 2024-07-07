<?php

namespace Apps\Core\Packages\Devtools\Migrator\Install\Schema;

use Phalcon\Db\Column;

class Issues
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
                        'api_id',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'repository_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'source_issue_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'destination_issue_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'issue_details',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'issue_comments',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'issue_timeline',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'migrated',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => false
                        ]
                    ),
                ]
            ];
    }
}