<?php

namespace Apps\Dash\Components\System\Api\Ebay\Taxonomy;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\System\Api\Ebay\Taxonomy\EbayTaxonomy;
use Apps\Dash\Packages\System\Api\Ebay\Taxonomy\EbayTaxonomyExtractData;
use System\Base\BaseComponent;

class TaxonomyComponent extends BaseComponent
{
    use DynamicTable;

    protected $taxonomyPackage;

    public function initialize()
    {
        $this->taxonomyPackage = $this->usePackage(EbayTaxonomy::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['extractdata']) &&
            $this->getData()['extractdata'] == true
        ) {
            $this->extractData();

            echo 'Code: ' . $this->view->responseCode . '<br>';

            echo 'Message: ' . $this->view->responseMessage;

            return false;
        }

        $rootIdsArr =
            $this->taxonomyPackage->getByParams([
                'conditions'    => 'root_id = :id:',
                'bind'          =>
                    [
                        'id'    => 0
                    ]
            ]);

        $rootIds = [];

        if (count($rootIdsArr) > 0) {
            foreach ($rootIdsArr as $rootId) {
                array_push($rootIds, (int) $rootId['id']);
            }
        }

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $taxonomy =
                    $this->taxonomyPackage->getById($this->getData()['id']);

                $this->view->taxonomy = $taxonomy;
            } else {
                $this->view->taxonomy = [];
            }

            if (in_array($this->getData()['id'], $rootIds)) {
                $this->view->isRoot = true;
            } else {
                $this->view->isRoot = false;
            }

            $this->view->pick('taxonomy/view');

            return;
        } else {
            $controlActions =
                [
                    'enableActionsForIds'   =>
                    [
                        'edit'      => $rootIds
                    ],
                    'disableActionsForIds'   =>
                    [
                        'view'      => $rootIds
                    ],
                    'actionsToEnable'       =>
                    [
                        'view'      => 'system/api/ebay/taxonomy',
                        'edit'      => 'system/api/ebay/taxonomy'
                    ]
                ];

            // $dtAdditionControlButtons =
            //     [
            //         'includeId'  => true,
            //         // 'includeQ'   => true, //Only true when not adding /q/ in link below.
            //         'buttons'    => [
            //             'sub'    => [
            //                 'title'     => 'Sub-Categories',
            //                 'icon'      => 'sitemap',
            //                 'link'      => 'q/sub/true'
            //             ]
            //         ]
            //     ];

            $replaceColumns =
                [
                    'installed'  =>
                        [
                            'html' =>
                                [
                                    '0' => 'No',
                                    '1' => 'Yes'
                                ]
                        ],
                    'enabled'  =>
                        [
                            'html' =>
                                [
                                    '0' => 'No',
                                    '1' => 'Yes'
                                ]
                        ]
                    ];

            $replaceColumnsTitle = ['hierarchy_str' => 'hierarchy'];

            $filterList =
                ['name', 'hierarchy_str', 'installed', 'enabled', 'product_count', 'root_id', 'taxonomy_version'];

            $headerList =
                ['name', 'hierarchy_str', 'installed', 'enabled', 'product_count', 'taxonomy_version'];

            $postUrl = 'system/api/ebay/taxonomy/view';

            // if ($this->request->isGet()) {
            //     $postUrlParams =
            //         [
            //             'conditions'    => '-:root_id:equals:0&',
            //             'limit'         => 100
            //         ];
            // } else {
            //     $postUrlParams = null;
            // }
        }

        $replaceColumnsTitle =
            [
                'hierarchy_str' => 'Hierarchy'
            ];

        $this->generateDTContent(
            $this->taxonomyPackage,
            $postUrl,
            null,
            $headerList,
            true,
            $filterList,
            $controlActions,
            $replaceColumnsTitle,
            $replaceColumns,
            'name'
        );

        $this->view->pick('taxonomy/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        //
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->taxonomyPackage->updateTaxonomy($this->postData());

            $this->view->responseCode = $this->taxonomyPackage->packagesData->responseCode;

            $this->view->responseMessage = $this->taxonomyPackage->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchTaxonomyAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchCountries = $this->basepackages->taxonomyPackage->searchCountries($searchQuery);

                if ($searchCountries) {
                    $this->view->responseCode = $this->basepackages->taxonomyPackage->packagesData->responseCode;

                    $this->view->countries = $this->basepackages->taxonomyPackage->packagesData->countries;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    protected function extractData()
    {
        if ($this->request->isGet() && isset($this->getData()['api_id'])) {
            $apiId = $this->getData()['api_id'];
        } else if ($this->request->isPost() && isset($this->postData()['api_id'])) {
            $apiId = $this->postData()['api_id'];
        } else {
            throw new \Exception('api_id missing');
        }

        // if (isset($this->getData()['srcFile'])) {
            $account = $this->auth->account();
            $account['id'] = 1;
            if ($account && $account['id'] == 1) {
                $ebayTaxonomyExtractDataPackage = new EbayTaxonomyExtractData;

                $ebayTaxonomyExtractDataPackage->extractData($apiId);

                $this->view->responseCode = $ebayTaxonomyExtractDataPackage->packagesData->responseCode;

                $this->view->responseMessage = $ebayTaxonomyExtractDataPackage->packagesData->responseMessage;
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'Only super admin allowed to extract geo data';
            }
        // } else {
        //     throw new \Exception('Source file missing in url. Example URL: admin/settings/api/ebay/taxonomy/q/extractdata/true/srcFile/src');
        // }
    }

    public function installAction()
    {
        if ($this->request->isPost()) {
            // if (!$this->checkCSRF()) {
            //     return;
            // }

            $this->taxonomyPackage->installTaxonomy($this->postData());

            $this->view->responseCode = $this->taxonomyPackage->packagesData->responseCode;

            $this->view->responseMessage = $this->taxonomyPackage->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function uninstallAction()
    {
        //
    }
}