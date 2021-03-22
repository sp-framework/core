<?php

namespace Apps\Dash\Packages\Devtools\Api\Contracts;

use Phalcon\Helper\Arr;

class ContractsXML
{
    protected $contract = null;

    protected $localContent;

    protected $ebayServicesClass = '\\Apps\Dash\Packages\System\Api\Apis\Ebay';

    protected $ebayServicesDirectory = 'apps/Dash/Packages/System/Api/Apis/Ebay/';

    public function init($contract, $localContent)
    {
        $this->contract = $contract;

        $this->localContent = $localContent;

        return $this;
    }

    public function buildBaseServicesFileContent()
    {
        $file = '';

        $file .= $this->generateBaseServicesHeader();

        $file .= '
    const HDR_API_VERSION = \'X-EBAY-API-COMPATIBILITY-LEVEL\';

    const HDR_APP_ID = \'X-EBAY-API-APP-NAME\';

    const HDR_AUTHORIZATION = \'X-EBAY-API-IAF-TOKEN\';

    const HDR_CERT_ID = \'X-EBAY-API-CERT-NAME\';

    const HDR_DEV_ID = \'X-EBAY-API-DEV-NAME\';

    const HDR_OPERATION_NAME = \'X-EBAY-API-CALL-NAME\';

    const HDR_SITE_ID = \'X-EBAY-API-SITEID\';

    protected static $endPoints =
        ' . $this->generateProperties('endPoints') . ';
        ';

    $file .= '
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public static function getConfigDefinitions()
    {
        $definitions = parent::getConfigDefinitions();

        return $definitions + [
            \'apiVersion\' => [
                \'valid\' => [\'string\'],
                \'default\' => ' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Services\\' . $this->contract['name'] . 'Service::API_VERSION,
                \'required\' => true
            ],
            \'authorization\' => [
                \'valid\' => [\'string\']
            ],
            \'authToken\' => [
                \'valid\' => [\'string\']
            ],
            \'siteId\' => [
                \'valid\' => [\'int\', \'string\'],
                \'required\' => true,
                \'default\' => self::$config[\'ebay_ids\'][self::$config[\'marketplace_id\']][\'site_id\']
            ]
        ];
    }

    protected function getEbayHeaders($operationName)
    {
        $appId =
            $this->getConfig(\'user_credentials_app_id\') !== \'\' ?
            $this->getConfig(\'user_credentials_app_id\') :
            $this->getConfig(\'credentials\')[\'appId\'];

        $devId =
            $this->getConfig(\'user_credentials_dev_id\') !== \'\' ?
            $this->getConfig(\'user_credentials_dev_id\') :
            $this->getConfig(\'credentials\')[\'devId\'];

        $certId =
            $this->getConfig(\'user_credentials_cert_id\') !== \'\' ?
            $this->getConfig(\'user_credentials_cert_id\') :
            $this->getConfig(\'credentials\')[\'certId\'];

        $headers = [];

        // Add required headers first.
        $headers[self::HDR_API_VERSION] = $this->getConfig(\'apiVersion\');
        $headers[self::HDR_OPERATION_NAME] = $operationName;
        $headers[self::HDR_SITE_ID] = $this->getConfig(\'siteId\');

        // Add optional headers.
        if ($appId) {
            $headers[self::HDR_APP_ID] = $appId;
        }

        if ($certId) {
            $headers[self::HDR_CERT_ID] = $certId;
        }

        if ($devId) {
            $headers[self::HDR_DEV_ID] = $devId;
        }

        if ($this->getConfig(\'authorization\')) {
            $headers[self::HDR_AUTHORIZATION] = $this->getConfig(\'authorization\');
        }

        if ($operationName === \'UploadSiteHostedPictures\') {
            $headers[\'Content-Type\'] = \'multipart/form-data;boundary="boundary"\';
        }

        return $headers;
    }

    protected function callOperationAsync($name, BaseType $request, $responseClass)
    {
        if ($this->getConfig(\'authorization\') !== null) {
            /**
             * Don\'t send requester credentials if oauth authentication needed.
             */
            if (isset($request->RequesterCredentials)) {
                unset($request->RequesterCredentials);
            }
        } elseif ($this->getConfig(\'authToken\') !== null) {
            /**
             * Don\'t modify a request if the token already exists.
             */
            if (!isset($request->RequesterCredentials)) {
                $request->RequesterCredentials = new ' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Types\CustomSecurityHeaderType();
            }
            if (!isset($request->RequesterCredentials->eBayAuthToken)) {
                $request->RequesterCredentials->eBayAuthToken = $this->getConfig(\'authToken\');
            }
        }

        return parent::callOperationAsync($name, $request, $responseClass);
    }

    protected function buildRequestBody(BaseType $request)
    {
        if ($request->hasAttachment() && $request instanceof ' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Types\UploadSiteHostedPicturesRequest) {
            return $this->buildMultipartFormDataXMLPayload($request).$this->buildMultipartFormDataFilePayload($request->PictureName, $request->attachment());
        } else {
            return parent::buildRequestBody($request);
        }
    }
}';

        return $file;
    }

    protected function generateBaseServicesHeader()
    {
        return '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\EbayXMLService;
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ' . $this->contract['name'] . 'BaseService extends EbayXMLService
{';
    }

    public function buildServicesFileContent()
    {
        $file = '';

        $file .= $this->generateServicesHeader();

        $file .= '
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }';

    $file .= $this->getOperationMethods() . '
}';

        return $file;
    }

    protected function generateServicesHeader()
    {
        return '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Services\\' . $this->contract['name'] . 'BaseService;

class ' . $this->contract['name'] . 'Service extends ' . $this->contract['name'] . 'BaseService
{
    const API_VERSION = \'' . $this->contract['content']['info']['version'] . '\';
';
    }

    protected function getOperationMethods()
    {
        $methods = '';

        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    if (!isset($method['operationId'])) {
                        continue;
                    }
                    $methods .=
                    '

    public function ' . lcfirst($method['operationId']) . '(' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'Request $request)
    {
        return $this->' . lcfirst($method['operationId']) . 'Async($request)->wait();
    }

    public function ' . lcfirst($method['operationId']) . 'Async(' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'Request $request)
    {
        return $this->callOperationAsync(
            \'' . $method['operationId'] . '\',
            $request,
            \'' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'Response' .
        '\'
        );
    }';
                }
            }
        }

        return $methods;
    }

    public function generateProperties($type)
    {
        if ($type === 'endPoints') {
            $endpoints = [];

            foreach ($this->contract['content']['servers'] as $serverKey => $server) {
                $url = explode('{', $server['url']);

                if (count($url) > 1) {
                    $urlKey = str_replace('}', '', $url[1]);

                    $description = strtolower($server['description']);

                    //Dirty fix to apply production/sandbox as key
                    if (strpos($description, 'prod') !== false) {
                        $endpoints['primary']['production'] = $url[0] . $server['variables'][$urlKey]['default'];
                    } else if (strpos($description, 'sand') !== false) {
                        $endpoints['primary']['sandbox'] = $url[0] . $server['variables'][$urlKey]['default'];
                    }
                } else {
                    $endpoints['primary']['production'] = $url[0];
                }
            }

            foreach ($this->contract['content']['paths'] as $pathKey => $path) {
                if (is_array($path)) {
                    foreach ($path as $methodKey => $method) {
                        if ($methodKey === 'servers') {
                            $url = explode('{', $method[0]['url']);
                            $urlKey = str_replace('}', '', $url[1]);

                            $description = strtolower($method[0]['description']);

                            if (isset($path['get']['operationId'])) {
                                $operationId = $path['get']['operationId'];
                            } else if (isset($path['post']['operationId'])) {
                                $operationId = $path['post']['operationId'];
                            }

                            if (strpos($description, 'prod') !== false) {
                                array_push($endpoints, $url[0] . $server['variables'][$urlKey]['default']);
                            } else if (strpos($description, 'sand') !== false) {
                                array_push($endpoints, $url[0] . $server['variables'][$urlKey]['default']);
                            }
                        }
                    }
                }
            }
            // return implode(',', $endpoints);
            return $this->generatePropertyFromArray($endpoints);

        // } else if ($type === 'operations') {
        //     $operations = [];
        //     foreach ($this->contract['content']['paths'] as $pathKey => $path) {
        //         if (is_array($path)) {
        //             foreach ($path as $methodKey => $method) {
        //                 if (!isset($method['operationId'])) {
        //                     continue;
        //                 }
        //                 $operations[ucfirst($method['operationId'])] = [];
        //                 $operations[ucfirst($method['operationId'])]['method'] = strtoupper($methodKey);
        //                 $operations[ucfirst($method['operationId'])]['resource'] = ltrim($pathKey, '/');
        //                 $operations[ucfirst($method['operationId'])]['responseClass'] =
        //                     $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestResponse';
        //                 if (isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0) {
        //                     $operations[ucfirst($method['operationId'])]['params'] = [];
        //                     foreach ($method['parameters'] as $parameterKey => $parameter) {
        //                         if ($parameter['in'] === 'path' || $parameter['in'] === 'query') {
        //                             $operations[ucfirst($method['operationId'])]['params'][$parameter['name']] = [];
        //                             $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'] = [];
        //                             array_push($operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'], $parameter['schema']['type']);
        //                             if ($parameter['required'] === true) {
        //                                 $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['required'] = $parameter['required'];
        //                             }
        //                         }
        //                     }
        //                 } else {
        //                     $operations[ucfirst($method['operationId'])]['params'] = [];
        //                 }
        //             }
        //         }
        //     }

        //     return $this->generatePropertyFromArray($operations);
        }
    }

    protected function generatePropertyFromArray(array $array)
    {
        $export = var_export($array, TRUE);

        $patterns = [
            "/array \(/"                        => '[',
            "/^([ ]*)\)(,?)$/m"                 => '      $1]$2',
            "/=>[ ]?\n[ ]+\[/"                  => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/"  => '      $1$2 => $3',
            "/0 => /"                           => '',
            "/'string',/"                       => "      'string',",
            "/'required' => true,/"             => "      'required' => true,",
            "/'repeatable' => false,/"          => "      'repeatable' => false,",
            "/'repeatable' => true,/"           => "      'repeatable' => true,",
            "/'attribute' => false,/"           => "      'attribute' => false,",
            "/'attribute' => true,/"            => "      'attribute' => true,",
        ];

        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);

        $export = str_replace('\\\\', '\\', $export);//remove extra back slashes

        return $export;
    }

    public function writeBaseServicesFileContent($file)
    {
        $this->localContent->put(
            $this->ebayServicesDirectory .
            $this->contract['name'] .
            '/Services/' .
            $this->contract['name'] .
            'BaseService.php',
            $file
        );
    }

    public function writeServicesFileContent($file)
    {
        $this->localContent->put(
            $this->ebayServicesDirectory .
            $this->contract['name'] .
            '/Services/' .
            $this->contract['name'] .
            'Service.php',
            $file
        );
    }

    public function buildTypesFile()
    {
        foreach ($this->contract['content']['components']['schemas'] as $typeKey => $type) {
            if (isset($type['properties'])) {
                if (!isset($type['xml'])) {
                    continue;
                } else if (isset($type['xml']['name'][$type['title']])) {
                    continue;
                } else if (isset($type['properties'][$type['title']])) {
                    continue;
                } else {
                    $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
                }
            } else if (!isset($type['allOf'])) {
                $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
            }
        }
    }

    protected function buildTypesFileContent($typeKey, $type)
    {
        $file = '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ' . $typeKey . ' extends BaseType
{';
        $propertyTypes = [];

        if (isset($type['properties'])) {
            foreach ($type['properties'] as $propertyKey => $property) {
                $propertyTypes[$propertyKey] = [];
                if (isset($property['type'])) {
                    if ($property['type'] === 'array') {
                        if (isset($property['items'])) {
                            if (isset($property['items']['$ref'])) {
                                $propertyTypes[$propertyKey]['type'] = $this->getRefClass($property['items']['$ref']);
                            } else if (isset($property['items']['type'])) {
                                $propertyTypes[$propertyKey]['type'] = $property['items']['type'];
                            }
                        }
                        $propertyTypes[$propertyKey]['repeatable'] = true;
                    } else {
                        $propertyTypes[$propertyKey]['type'] = $property['type'];
                        $propertyTypes[$propertyKey]['repeatable'] = false;
                    }
                } else if (isset($property['$ref'])) {
                    $propertyTypes[$propertyKey]['type'] = $this->getRefClass($property['$ref']);
                    $propertyTypes[$propertyKey]['repeatable'] = false;
                }
                $propertyTypes[$propertyKey]['attribute'] = false;
                $propertyTypes[$propertyKey]['elementName'] = $propertyKey;
            }

            if ($typeKey === 'AbstractRequestType') {
                $propertyTypes['RequesterCredentials'] = [];
                $propertyTypes['RequesterCredentials']['type'] = 'Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Types\CustomSecurityHeaderType';
                $propertyTypes['RequesterCredentials']['repeatable'] = false;
                $propertyTypes['RequesterCredentials']['attribute'] = false;
                $propertyTypes['RequesterCredentials']['elementName'] = 'RequesterCredentials';
            }
        } else {
            $propertyTypes[$type['title']] = [];
            $propertyTypes[$type['title']]['type'] = 'string';
            $propertyTypes[$type['title']]['repeatable'] = false;
            $propertyTypes[$type['title']]['attribute'] = false;
            $propertyTypes[$type['title']]['elementName'] = $type['title'];
        }

        $file .= '
    private static $propertyTypes = ' .
        $this->generatePropertyFromArray($propertyTypes) . ';';

        $file .= '

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        if (!array_key_exists(__CLASS__, self::$xmlNamespaces)) {
            self::$xmlNamespaces[__CLASS__] = \'xmlns="' . $type['xml']['namespace'] . '"\';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}';

        return $file;
    }

    protected function getRefClass($item)
    {
        if (isset($item['items']['$ref'])) {
            $itemArr = explode('/', $item['items']['$ref']);
        } else if (isset($item['$ref'])) {
            $itemArr = explode('/', $item['$ref']);
        } else {
            $itemArr = explode('/', $item);
        }

        return 'Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Types\\' . Arr::last($itemArr);
    }

    protected function writeTypesFileContent($filename, $file)
    {
        $this->localContent->put(
            $this->ebayServicesDirectory .
            $this->contract['name'] .
            '/Types/' .
            $filename .
            '.php',
            $file
        );
    }

    public function buildOperationsFile()
    {
        foreach ($this->contract['content']['components']['schemas'] as $typeKey => $type) {
            if (isset($type['allOf'])) {
                if (is_array($type['allOf'])) {
                    $operationId = str_replace('Type', '', $typeKey);
                    // var_dump($operationId);
                    foreach ($type['allOf'] as $allOfKey => $allOf) {
                        $properties = null;
                        if (isset($allOf['$ref'])) {
                            $extends = '\\' . $this->getRefClass($allOf['$ref']);
                        } else if (isset($allOf['properties'])) {
                            $properties = $allOf['properties'];
                        } else if (!isset($allOf['$ref'])) {
                            $extends = '\Apps\Dash\Packages\System\Api\Base\Types\BaseType';
                        }
                        // dump($allOf['properties']);
                        // var_dump($extends);
                        // dump($allOf);
                    }
                    $this->buildOperationsFileContent($operationId, $extends, $properties);
                }
            }
        }
    }

    protected function buildOperationsFileContent($operationId, $extends, $properties = null)
    {
        // dump($operationId, $extends, $properties);
        $file = '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Operations;

class ' . $operationId . ' extends ' . $extends . '
{';
        if ($properties) {
            $propertyTypes = [];

            foreach ($properties as $propertyKey => $property) {
                $propertyTypes[$propertyKey] = [];
                if (isset($property['type'])) {
                    if ($property['type'] === 'array') {
                        if (isset($property['items'])) {
                            if (isset($property['items']['$ref'])) {
                                $propertyTypes[$propertyKey]['type'] = $this->getRefClass($property['items']['$ref']);
                            } else if (isset($property['items']['type'])) {
                                $propertyTypes[$propertyKey]['type'] = $property['items']['type'];
                            }
                        }
                        $propertyTypes[$propertyKey]['repeatable'] = true;
                    } else {
                        $propertyTypes[$propertyKey]['type'] = $property['type'];
                        $propertyTypes[$propertyKey]['repeatable'] = false;
                    }
                } else if (isset($property['$ref'])) {
                    $propertyTypes[$propertyKey]['type'] = $this->getRefClass($property['$ref']);
                    $propertyTypes[$propertyKey]['repeatable'] = false;
                }
                $propertyTypes[$propertyKey]['attribute'] = false;
                $propertyTypes[$propertyKey]['elementName'] = $propertyKey;
            }
            $file .= '
    private static $propertyTypes = ';

    $file .= $this->generatePropertyFromArray($propertyTypes) . ';';

            if (strpos($extends, 'AbstractRequestType')) {
                $file .= $this->buildOperationsRequestFileContent($operationId);
                $this->writeOperationsRequestFileContent($operationId, $file);
            } else if (strpos($extends, 'AbstractResponseType')) {
                $file .= $this->buildOperationsResponseFileContent($operationId);
                $this->writeOperationsResponseFileContent($operationId, $file);
            }
        }
        // var_dump($file);
    }

    protected function buildOperationsRequestFileContent($operationId)
    {
        return '

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        if (!array_key_exists(__CLASS__, self::$xmlNamespaces)) {
            self::$xmlNamespaces[__CLASS__] = \'xmlns="urn:ebay:apis:eBLBaseComponents"\';
        }

        if (!array_key_exists(__CLASS__, self::$requestXmlRootElementNames)) {
            self::$requestXmlRootElementNames[__CLASS__] = \'' . $operationId . '\';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}';
    }

    protected function writeOperationsRequestFileContent($filename, $file)
    {
        $this->localContent->put(
            $this->ebayServicesDirectory .
            $this->contract['name'] .
            '/Operations/' .
            $filename .
            '.php',
            $file
        );
    }

    protected function buildOperationsResponseFileContent()
    {
        return '

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        if (!array_key_exists(__CLASS__, self::$xmlNamespaces)) {
            self::$xmlNamespaces[__CLASS__] = \'xmlns="urn:ebay:apis:eBLBaseComponents"\';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}';
    }

    protected function writeOperationsResponseFileContent($filename, $file)
    {
        $this->localContent->put(
            $this->ebayServicesDirectory .
            $this->contract['name'] .
            '/Operations/' .
            $filename .
            '.php',
            $file
        );
    }
}