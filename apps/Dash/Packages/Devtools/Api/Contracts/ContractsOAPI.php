<?php

namespace Apps\Dash\Packages\Devtools\Api\Contracts;

use Phalcon\Helper\Arr;

class ContractsOAPI
{
    protected $contract = null;

    protected $localContent;

    protected $servicesClass = null;

    protected $servicesDirectory = null;

    public function init($contract, $localContent)
    {
        $this->contract = $contract;

        $this->servicesClass = '\\Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']);

        $this->servicesDirectory = 'apps/Dash/Packages/System/Api/Apis/'. ucfirst($this->contract['api_type']) . '/';

        $this->localContent = $localContent;

        return $this;
    }

    public function buildBaseServicesFileContent()
    {
        $file = '';

        $file .= $this->generateBaseServicesHeader();

        if ($this->contract['api_type'] === 'ebay') {
            $const = '
    const HDR_MARKETPLACE_ID = \'X-EBAY-C-MARKETPLACE-ID\';';
        } else if ($this->contract['api_type'] === 'xero') {
            $const = '
    const HDR_XERO_TENANT_ID = \'xero-tenant-id\';';
        } else if ($this->contract['api_type'] === 'gitea') {
            $const = '';
        } else if ($this->contract['api_type'] === 'binarylane') {
            $const = '';
        }

        $file .=
'    protected static $endPoints =
        ' . $this->generateProperties('endPoints') . ';';

        if ($this->contract['api_type'] === 'ebay') {
            $headers =
'    protected function getEbayHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'Bearer \' . $this->getConfig(\'user_access_token\');

        // Add optional headers.
        if ($this->getConfig(\'marketplaceId\')) {
            $headers[self::HDR_MARKETPLACE_ID] = $this->getConfig(\'marketplaceId\');
        }

        return $headers;
    }';
        } else if ($this->contract['api_type'] === 'xero') {
            $headers =
'    protected function getXeroHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'Bearer \' . $this->getConfig(\'user_access_token\');

        $headers[self::HDR_XERO_TENANT_ID] = $this->getConfig("tenantId");

        return $headers;
    }';
        } else if ($this->contract['api_type'] === 'gitea') {
            $headers =
'    protected function getGiteaHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'token \' . $this->getConfig(\'user_access_token\');

        return $headers;
    }';
        } else if ($this->contract['api_type'] === 'binarylane') {
            $headers =
'    protected function getBinarylaneHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'token \' . $this->getConfig(\'user_access_token\');

        return $headers;
    }';
        }

        $file .= '

    const HDR_AUTHORIZATION = \'Authorization\';
' . $const . '

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

' . $headers . '
}';
        return $file;
    }

    protected function generateBaseServicesHeader()
    {
        if ($this->contract['api_type'] === 'ebay') {
            $baseRestServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayRESTService';
            $baseRestService = 'EbayRESTService';
        } else if ($this->contract['api_type'] === 'xero') {
            $baseRestServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroRESTService';
            $baseRestService = 'XeroRESTService';
        } else if ($this->contract['api_type'] === 'gitea') {
            $baseRestServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaRESTService';
            $baseRestService = 'GiteaRESTService';
        } else if ($this->contract['api_type'] === 'binarylane') {
            $baseRestServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneRESTService';
            $baseRestService = 'GiteaRESTService';
        }

        return '<?php

namespace Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Services;

use ' . $baseRestServiceNamespace . ';

class ' . $this->contract['name'] . 'BaseService extends ' . $baseRestService . '
{
';
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
                                $endpoints[$operationId]['production'] = $url[0] . $server['variables'][$urlKey]['default'];
                            } else if (strpos($description, 'sand') !== false) {
                                $endpoints[$operationId]['sandbox'] = $url[0] . $server['variables'][$urlKey]['default'];
                            }
                        }
                        // $operations[ucfirst($method['operationId'])] = [];
                        // $operations[ucfirst($method['operationId'])]['method'] = strtoupper($methodKey);
                        // $operations[ucfirst($method['operationId'])]['resource'] = ltrim($pathKey, '/');
                        // $operations[ucfirst($method['operationId'])]['responseClass'] =
                        //     $this->servicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestResponse';
                        // if (isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0) {
                        //     $operations[ucfirst($method['operationId'])]['params'] = [];
                        //     foreach ($method['parameters'] as $parameterKey => $parameter) {
                        //         if ($parameter['in'] === 'path' || $parameter['in'] === 'query') {
                        //             $operations[ucfirst($method['operationId'])]['params'][$parameter['name']] = [];
                        //             $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'] = [];
                        //             array_push($operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'], $parameter['schema']['type']);
                        //             if ($parameter['required'] === true) {
                        //                 $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['required'] = $parameter['required'];
                        //             }
                        //         }
                        //     }
                        // } else {
                        //     $operations[ucfirst($method['operationId'])]['params'] = [];
                        // }
                    }
                }
            }
            return $this->generatePropertyFromArray($endpoints);

        } else if ($type === 'operations') {
            $operations = [];
            foreach ($this->contract['content']['paths'] as $pathKey => $path) {
                if (is_array($path)) {
                    foreach ($path as $methodKey => $method) {
                        if (isset($method['tags']) || isset($method['operationId'])) {
                            if (!isset($method['operationId']) && isset($method['tags'])) {
                                $method['operationId'] = $method['tags'][0];
                            }
                        } else {
                            continue;
                        }

                        $operations[ucfirst($method['operationId'])] = [];
                        $operations[ucfirst($method['operationId'])]['method'] = strtoupper($methodKey);
                        $operations[ucfirst($method['operationId'])]['resource'] = ltrim($pathKey, '/');
                        $operations[ucfirst($method['operationId'])]['responseClass'] =
                            $this->servicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestResponse';

                        if (isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0) {
                            $operations[ucfirst($method['operationId'])]['params'] = [];

                            foreach ($method['parameters'] as $parameterKey => $parameter) {
                                if (isset($parameter['name'])) {
                                    $operations[ucfirst($method['operationId'])]['params'][$parameter['name']] = [];
                                    $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'] = [];

                                    if (isset($parameter['in']) &&
                                        ($parameter['in'] === 'path' || $parameter['in'] === 'query')
                                    ) {
                                        if (isset($parameter['schema']['type'])) {
                                            array_push($operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'], $parameter['schema']['type']);
                                        } else if (isset($parameter['schema']['$ref'])) {
                                            array_push($operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'], $this->getRefClass($parameter['schema']['$ref']));
                                        }
                                    } else {
                                        if (isset($parameter['$ref'])) {
                                            array_push($operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'], $this->getRefClass($parameter['$ref']));
                                        }
                                    }

                                    if (isset($parameter['required']) && $parameter['required'] === true) {
                                        $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['required'] = $parameter['required'];
                                    }
                                } else if (isset($parameter['$ref'])) {
                                    // var_dump($operations[ucfirst($method['operationId'])]);
                                    // var_dump($operations[ucfirst($method['operationId'])]['params']);
                                    // var_dump($this->getRefClass($parameter['$ref']));
                                    $paramName = $this->getRefClass($parameter['$ref'], true);
                                    if ($paramName !== 'summarizeErrors' &&
                                        $paramName !== 'ifModifiedSince' &&
                                        $paramName !== 'ContentType' &&
                                        $paramName !== 'unitdp'
                                    ) {
                                        $operations[ucfirst($method['operationId'])]['params'][$paramName] = [];
                                        $operations[ucfirst($method['operationId'])]['params'][$paramName]['valid'] = ['string'];
                                    }
                                }
                            }
                        } else {
                            $operations[ucfirst($method['operationId'])]['params'] = [];
                        }
                    }
                }
            }

            return $this->generatePropertyFromArray($operations);
        }
    }

    public function buildServicesFileContent()
    {
        $file = '';

        $file .= $this->generateServicesHeader();

        $file .=
'    protected static $operations =
        ' . $this->generateProperties('operations') . ';';

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

namespace Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Services;

use Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Services\\' . $this->contract['name'] . 'BaseService;

class ' . $this->contract['name'] . 'Service extends ' . $this->contract['name'] . 'BaseService
{
';
    }

    protected function getOperationMethods()
    {
        $methods = '';

        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    if (isset($method['tags']) || isset($method['operationId'])) {
                        if (!isset($method['operationId']) && isset($method['tags'])) {
                            $method['operationId'] = $method['tags'][0];
                        }
                    } else {
                        continue;
                    }

                    $methods .=
                    '

    public function ' . $method['operationId'] . '(' . $this->servicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestRequest $request)
    {
        return $this->' . $method['operationId'] . 'Async($request)->wait();
    }

    public function ' . $method['operationId'] . 'Async(' . $this->servicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestRequest $request)
    {
        return $this->callOperationAsync(\'' . ucfirst($method['operationId']) . '\', $request);
    }';
                }
            }
        }

        return $methods;
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
        try {
            $this->localContent->write(
                $this->servicesDirectory .
                $this->contract['name'] .
                '/Services/' .
                $this->contract['name'] .
                'BaseService.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    public function writeServicesFileContent($file)
    {
        try {
            $this->localContent->write(
                $this->servicesDirectory .
                $this->contract['name'] .
                '/Services/' .
                $this->contract['name'] .
                'Service.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    protected function writeTypesFileContent($filename, $file)
    {
        try {
            $this->localContent->write(
                $this->servicesDirectory .
                $this->contract['name'] .
                '/Types/' .
                $filename .
                '.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    public function buildTypesFile()
    {
        foreach ($this->contract['content']['components']['schemas'] as $typeKey => $type) {
            $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
        }
        foreach ($this->contract['content']['components']['responses'] as $typeKey => $type) {
            $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
        }
    }

    protected function buildTypesFileContent($typeKey, $type)
    {
        if ($this->contract['api_type'] === 'ebay') {
            $baseTypeServiceNamespace = 'Apps\Dash\Packages\System\Api\Base\Types\BaseType';
            $baseTypeService = 'BaseType';
        } else if ($this->contract['api_type'] === 'xero') {
            $baseTypeServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroType';
            $baseTypeService = 'XeroType';
        } else if ($this->contract['api_type'] === 'gitea') {
            $baseTypeServiceNamespace = 'Apps\Dash\Packages\System\Api\Base\Types\BaseType';
            $baseTypeService = 'BaseType';
        } else if ($this->contract['api_type'] === 'binarylane') {
            $baseTypeServiceNamespace = 'Apps\Dash\Packages\System\Api\Base\Types\BaseType';
            $baseTypeService = 'BaseType';
        }

        $file = '<?php

namespace Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Types;

use ' . $baseTypeServiceNamespace . ';

class ' . $typeKey . ' extends ' . $baseTypeService . '
{';

        $propertyTypes = [];

        if (isset($type['properties'])) {
            foreach ($type['properties'] as $propertyKey => $property) {
                $propertyTypes[$propertyKey] = [];

                if (isset($property['type'])) {
                    if ($property['type'] === 'number') {
                        $property['type'] = 'double';
                    }
                    // if ($property['type'] === 'string' || $property['type'] === 'integer') {
                    //     $propertyTypes[$propertyKey]['type'] = $property['type'];
                    //     $propertyTypes[$propertyKey]['repeatable'] = false;
                    // } else if ($property['type'] === 'array') {
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
        } else if (isset($type['content']['application/json']['schema']['$ref'])) {
            $propertyName = $this->getRefClass($type['content']['application/json']['schema']['$ref'], true);

            $propertyTypes[$propertyName] = [];
            $propertyTypes[$propertyName]['type'] = $this->getRefClass($type['content']['application/json']['schema']['$ref']);
            $propertyTypes[$propertyName]['repeatable'] = true;
            $propertyTypes[$propertyName]['attribute'] = false;
            $propertyTypes[$propertyName]['elementName'] = $propertyName;
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

        $this->setValues(__CLASS__, $childValues);
    }
}';

        return $file;
    }

    protected function getRefClass($item, $onlyLast = false)
    {
        if (isset($item['items']['$ref'])) {
            $itemArr = explode('/', $item['items']['$ref']);
        } else if (isset($item['$ref'])) {
            $itemArr = explode('/', $item['$ref']);
        } else if (isset($item['items']['type']) && is_string($item['items']['type'])) {
            return $item['items']['type'];
        } else if (is_string($item)) {
            $itemArr = explode('/', $item);
        } else if (isset($item['properties']['data']['items']['$ref'])) {
            $itemArr = explode('/', $item['properties']['data']['items']['$ref']);
        } else if (isset($item['additionalProperties']['type']) && is_string($item['additionalProperties']['type'])) {
            return $item['additionalProperties']['type'];
        }

        if ($onlyLast) {
            return Arr::last($itemArr);
        }

        return 'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Types\\' . Arr::last($itemArr);
    }

    public function buildOperationsFile()
    {
        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    $methodKey = strtolower($methodKey);

                    if (isset($method['tags']) || isset($method['operationId'])) {
                        if (!isset($method['operationId']) && isset($method['tags'])) {
                            $method['operationId'] = $method['tags'][0];
                        }
                    } else {
                        continue;
                    }

                    if ($methodKey === 'get' &&
                        (isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0)
                    ) {
                        $requestParams = [];

                        foreach ($method['parameters'] as $parameterKey => $parameter) {
                            if (isset($parameter['in']) &&
                                ($parameter['in'] === 'path' || $parameter['in'] === 'query')
                            ) {
                                if (!isset($parameter['name'])) {
                                    $parameter['name'] = $method['operationId'];
                                }

                                $requestParams[$parameter['name']] = [];
                                if (isset($parameter['schema']['type'])) {
                                    if ($parameter['schema']['type'] === 'string') {
                                        $requestParams[$parameter['name']]['type'] = $parameter['schema']['type'];
                                        $requestParams[$parameter['name']]['repeatable'] = false;
                                    } else if ($parameter['schema']['type'] === 'array') {
                                        if (isset($property['items'])) {
                                            foreach ($property['items'] as $itemKey => $item) {
                                                if ($itemKey === '$ref') {
                                                    $requestParams[$parameter['name']]['type'] = $this->getRefClass($item);
                                                }
                                            }
                                        }
                                        $requestParams[$parameter['name']]['repeatable'] = true;
                                    }
                                } else if (isset($parameter['schema']['$ref'])) {
                                    $requestParams[$parameter['name']]['type'] = $this->getRefClass($parameter['schema']['$ref']);
                                    $requestParams[$parameter['name']]['repeatable'] = false;
                                }

                                $requestParams[$parameter['name']]['attribute'] = false;
                                $requestParams[$parameter['name']]['elementName'] = $parameter['name'];
                            } else {
                                if ($this->contract['api_type'] === 'xero') {
                                    if (isset($parameter['$ref'])) {
                                        $refName = $this->getRefClass($parameter['$ref'], true);
                                        $requestParams[$refName]['type'] = 'string';
                                        $requestParams[$refName]['attribute'] = false;
                                        $requestParams[$refName]['repeatable'] = false;
                                        $requestParams[$refName]['elementName'] = $refName;
                                    }
                                } else {
                                    $requestParams[$parameter['name']] = [];
                                    if (isset($parameter['$ref'])) {
                                        $requestParams[$parameter['name']]['type'] = $this->getRefClass($parameter['$ref']);
                                        $requestParams[$parameter['name']]['attribute'] = false;
                                        $requestParams[$parameter['name']]['repeatable'] = true;
                                        $requestParams[$parameter['name']]['elementName'] = $parameter['name'];
                                    }
                                }
                            }

                            if (isset($parameter['required']) && $parameter['required'] === true) {
                                $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['required'] = $parameter['required'];
                            }
                        }
                    } else if (isset($method['requestBody']) && is_array($method['requestBody']) && count($method['requestBody']) > 0) {
                        $requestParams = [];
                        // if ($this->contract['api_type'] === 'xero') {
                            if (isset($method['requestBody']['content']['application/json']['schema'])) {
                                $refName = $this->getRefClass($method['requestBody']['content']['application/json']['schema'], true);
                                $requestParams[$refName]['type'] = $this->getRefClass($method['requestBody']['content']['application/json']['schema']);
                                $requestParams[$refName]['attribute'] = false;
                                $requestParams[$refName]['repeatable'] = true;
                                $requestParams[$refName]['elementName'] = $refName;
                            }
                        // } else {
                            //FIX THIS FOR OTHER APIs!!! (ebay)
                            // $requestParams[$parameter['name']] = [];
                            // if (isset($parameter['$ref'])) {
                            //     $requestParams[$parameter['name']]['type'] = $this->getRefClass($parameter['$ref']);
                            //     $requestParams[$parameter['name']]['attribute'] = false;
                            //     $requestParams[$parameter['name']]['repeatable'] = true;
                            //     $requestParams[$parameter['name']]['elementName'] = $parameter['name'];
                            // }
                        // }
                    } else {
                        $requestParams = [];
                    }
                    $this->buildOperationsRequestFileContent(ucfirst($method['operationId']), $requestParams);
                    $this->buildOperationsResponseFileContent();
                }
            }
        }
    }

    protected function buildOperationsRequestFileContent($operationId, $requestParams)
    {
        $file = '<?php

namespace Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ' . $operationId . 'RestRequest extends BaseType
{
    private static $propertyTypes = ';

    $file .= $this->generatePropertyFromArray($requestParams) . ';';

    $file .= '

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}';
        $this->writeOperationsRequestFileContent($operationId . 'RestRequest', $file);
    }

    protected function writeOperationsRequestFileContent($filename, $file)
    {
        try {
            $this->localContent->write(
                $this->servicesDirectory .
                $this->contract['name'] .
                '/Operations/' .
                $filename .
                '.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    protected function buildOperationsResponseFileContent()
    {
        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    if (isset($method['tags']) || isset($method['operationId'])) {
                        if (!isset($method['operationId']) && isset($method['tags'])) {
                            $method['operationId'] = $method['tags'][0];
                        }
                    } else {
                        continue;
                    }

                    $file = '<?php

namespace Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Operations;

use Apps\Dash\Packages\System\Api\Base\Traits\HttpHeadersTrait;
use Apps\Dash\Packages\System\Api\Base\Traits\StatusCodeTrait;
';
        if ($methodKey === 'get') {
            if (!isset($method['responses']['200'])) {
                $file .= '
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ' . ucfirst($method['operationId']) . 'RestResponse extends BaseType';
            } else {
                if (isset($method['responses']['200']['$ref'])) {
                    $class = $method['responses']['200']['$ref'];
                } else if (isset($method['responses']['200']['content']['application/json']['schema']['$ref'])) {
                    $class = $method['responses']['200']['content']['application/json']['schema']['$ref'];
                }
                $file .= '
class ' . ucfirst($method['operationId']) . 'RestResponse extends \\' .
                $this->getRefClass($class);
            }
        } else {
            $file .= '
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ' . ucfirst($method['operationId']) . 'RestResponse extends BaseType';
        }

        $file .= '
{
    use StatusCodeTrait;
    use HttpHeadersTrait;

    private static $propertyTypes = [
        \'errors\' => [
            \'type\' => \'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Types\Error\',
            \'repeatable\' => true,
            \'attribute\' => false,
            \'elementName\' => \'errors\'
        ],
        \'warnings\' => [
            \'type\' => \'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['api_type']) . '\\' . $this->contract['name'] . '\Types\Error\',
            \'repeatable\' => true,
            \'attribute\' => false,
            \'elementName\' => \'warnings\'
        ]
    ];

    public function __construct(array $values = [], $statusCode = 200, array $headers = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);

        $this->statusCode = (int)$statusCode;

        $this->setHeaders($headers);
    }
}';

                    $this->writeOperationsResponseFileContent(ucfirst($method['operationId']) . 'RestResponse', $file);
                }
            }
        }
    }

    protected function writeOperationsResponseFileContent($filename, $file)
    {
        try {
            $this->localContent->write(
                $this->servicesDirectory .
                $this->contract['name'] .
                '/Operations/' .
                $filename .
                '.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            var_dump($exception);die();
            throw $exception;
        }
    }
}