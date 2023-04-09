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

        if ($contract['type'] === 'system') {
            $this->servicesClass =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']) . '\\';

            $this->servicesDirectory =
                'system/Base/Providers/BasepackagesServiceProvider/Packages/Api/Apis/'. ucfirst($this->contract['category']) . '/';
        } else if ($contract['type'] === 'apps') {
            $this->servicesClass =
                'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']) . '\\';

            $this->servicesDirectory = 'apps/Dash/Packages/System/Api/Apis/'. ucfirst($this->contract['category']) . '/';
        }

        $this->localContent = $localContent;

        return $this;
    }

    public function buildBaseServicesFileContent()
    {
        $file = '';

        $file .= $this->generateBaseServicesHeader();

        if ($this->contract['category'] === 'ecom') {
            $const = '
    const HDR_MARKETPLACE_ID = \'X-EBAY-C-MARKETPLACE-ID\';';
        } else if ($this->contract['category'] === 'finance') {
            $const = '
    const HDR_XERO_TENANT_ID = \'xero-tenant-id\';';
        } else if ($this->contract['category'] === 'repo') {
            $const = '';
        } else if ($this->contract['category'] === 'service_providers') {
            $const = '';
        }

        $file .=
'    protected static $endPoints =
        ' . $this->generateProperties('endPoints') . ';';

        if ($this->contract['category'] === 'ecom') {
            $headers =
'    protected function getEcomHeaders()
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
        } else if ($this->contract['category'] === 'finance') {
            $headers =
'    protected function getXeroHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'Bearer \' . $this->getConfig(\'user_access_token\');

        $headers[self::HDR_XERO_TENANT_ID] = $this->getConfig("tenantId");

        return $headers;
    }';
        } else if ($this->contract['category'] === 'repo') {
            $headers =
'    protected function getRepoHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'token \' . $this->getConfig(\'user_access_token\');

        return $headers;
    }';
        } else if ($this->contract['category'] === 'service_providers') {
            $headers =
'    protected function getProvidersHeaders()
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
        if ($this->contract['type'] === 'system') {
            $baseNamespace = $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Services;';
            $baseRestServiceNamespace = $this->servicesClass . 'RESTService';
            $baseRestService =
                ucfirst($this->contract['category']) . 'RESTService';
        } else if ($this->contract['type'] === 'apps') {
            $baseNamespace =
                $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Services;';
            $baseRestServiceNamespace =
                $this->servicesClass . 'RESTService';
            $baseRestService =
                ucfirst($this->contract['category']) . 'RESTService';
        }

        return '<?php

namespace ' . $baseNamespace . '

use ' . $baseRestServiceNamespace . ';

class ' . ucfirst($this->contract['provider_name']) . 'BaseService extends ' . $baseRestService . '
{
';
    }

    public function generateProperties($type)
    {
        if ($type === 'endPoints') {
            $endpoints = [];

            if(isset($this->contract['content']['servers'])) {
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
            } else if (isset($this->contract['content']['basePath'])) {
                $endpoints['primary']['production'] = $this->contract['content']['basePath'];
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
                        //     '\\' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Operations\\' . ucfirst($method['operationId']) . 'RestResponse';
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
                            '\\' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Operations\\' . ucfirst($method['operationId']) . 'RestResponse';

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
                                        $operations[ucfirst($method['operationId'])]['params'][$paramName]['type'] = 'string';
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
        if ($this->contract['type'] === 'system') {
            $baseNamespace =
                'namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['provider_name']) . '\Services;';
            $baseServiceNamespace =
                'use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['provider_name']) . '\Services\\' . ucfirst($this->contract['provider_name']) . 'BaseService;';
        } else if ($this->contract['type'] === 'apps') {
            $baseNamespace =
                'namespace ' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Services;';
            $baseServiceNamespace =
                'use ' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Services\\' . ucfirst($this->contract['provider_name']) . 'BaseService;';
        }

        return '<?php

' . $baseNamespace . '

' . $baseServiceNamespace . '

class ' . ucfirst($this->contract['provider_name']) . 'Service extends ' . ucfirst($this->contract['provider_name']) . 'BaseService
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

    public function ' . $method['operationId'] . '(\\' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Operations\\' . ucfirst($method['operationId']) . 'RestRequest $request)
    {
        return $this->' . $method['operationId'] . 'Async($request)->wait();
    }

    public function ' . $method['operationId'] . 'Async(\\' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Operations\\' . ucfirst($method['operationId']) . 'RestRequest $request)
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
                ucfirst($this->contract['provider_name']) .
                '/Services/' .
                ucfirst($this->contract['provider_name']) .
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
                ucfirst($this->contract['provider_name']) .
                '/Services/' .
                ucfirst($this->contract['provider_name']) .
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
                ucfirst($this->contract['provider_name']) .
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
        $schemas = [];
        if (isset($this->contract['content']['components']['schemas'])) {
            $schemas = $this->contract['content']['components']['schemas'];
        } else if (isset($this->contract['content']['schemas'])) {
            $schemas = $this->contract['content']['schemas'];
        }
        $responses = [];
        if (isset($this->contract['content']['components']['responses'])) {
            $responses = $this->contract['content']['components']['responses'];
        } else if (isset($this->contract['content']['responses'])) {
            $responses = $this->contract['content']['responses'];
        }

        if (count($schemas) > 0) {
            foreach ($schemas as $typeKey => $type) {
                $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
            }
        }

        if (count($responses) > 0) {
            foreach ($responses as $typeKey => $type) {
                $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
            }
        }
    }

    protected function buildTypesFileContent($typeKey, $type)
    {
        if ($this->contract['type'] === 'system') {
            $categoryServiceNamespace =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['category']) . 'Type';
        } else if ($this->contract['type'] === 'apps') {
            $categoryServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['category']) . 'Type';
        }

        $baseTypeService = ucfirst($this->contract['category']) . 'Type';

        $file = '<?php

namespace ' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Types;

use ' . $categoryServiceNamespace . ';

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
            try {
                return Arr::last($itemArr);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return '' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Types\\' . Arr::last($itemArr);
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
                                    if ($parameter['schema']['type'] === 'string' ||
                                        $parameter['schema']['type'] === 'integer' ||
                                        $parameter['schema']['type'] === 'boolean'
                                    ) {
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
                                if ($this->contract['category'] === 'finance') {
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
                        // if ($this->contract['category'] === 'finance') {
                            if (isset($method['requestBody']['content']['application/json']['schema'])) {
                                dump($method['requestBody']);
                                $refName = $this->getRefClass($method['requestBody']['content']['application/json']['schema'], true);
                                $requestParams[$refName]['type'] = $this->getRefClass($method['requestBody']['content']['application/json']['schema']);
                                $requestParams[$refName]['attribute'] = false;
                                $requestParams[$refName]['repeatable'] = true;
                                $requestParams[$refName]['elementName'] = $refName;
                            }
                        // } else {
                            //FIX THIS FOR OTHER APIs!!! (ecom)
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
        if ($this->contract['type'] === 'system') {
            $categoryServiceNamespace =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['category']) . 'Type';
        } else if ($this->contract['type'] === 'apps') {
            $categoryServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['category']) . 'Type';
        }

        $baseTypeService = ucfirst($this->contract['category']) . 'Type';

        $file = '<?php

namespace ' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Operations;

use ' . $categoryServiceNamespace . ';

class ' . $operationId . 'RestRequest extends ' . ucfirst($this->contract['category']) . 'Type
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
                ucfirst($this->contract['provider_name']) .
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
        if ($this->contract['type'] === 'system') {
            $categoryServiceNamespace =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['category']) . 'Type';
        } else if ($this->contract['type'] === 'apps') {
            $categoryServiceNamespace = 'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']) . '\\' . ucfirst($this->contract['category']) . 'Type';
        }

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

namespace ' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Operations;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Traits\HttpHeadersTrait;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Traits\StatusCodeTrait;';
        if ($methodKey === 'get') {
            if (!isset($method['responses']['200'])) {
                $file .= '
use ' . $categoryServiceNamespace . ';

class ' . ucfirst($method['operationId']) . 'RestResponse extends ' . ucfirst($this->contract['category']) . 'Type';
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
use ' . $categoryServiceNamespace . ';

class ' . ucfirst($method['operationId']) . 'RestResponse extends ' . ucfirst($this->contract['category']) . 'Type';
        }

        $file .= '
{
    use StatusCodeTrait;
    use HttpHeadersTrait;

    private static $propertyTypes = [
        \'errors\' => [
            \'type\' => \'' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Types\Error\',
            \'repeatable\' => true,
            \'attribute\' => false,
            \'elementName\' => \'errors\'
        ],
        \'warnings\' => [
            \'type\' => \'' . $this->servicesClass . ucfirst($this->contract['provider_name']) . '\Types\Error\',
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
                ucfirst($this->contract['provider_name']) .
                '/Operations/' .
                $filename .
                '.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    public function writeProviderBaseFileContent()
    {
        try {
            $existingFile = $this->localContent->read(
                $this->servicesDirectory .
                ucfirst($this->contract['provider_name']) . '/' .
                ucfirst($this->contract['provider_name']) . '.php'
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToReadFile | \Exception $exception) {
            $existingFile = false;
        }

        if ($existingFile) {
            return;
        }

        if ($this->contract['type'] === 'system') {
            $servicesClass =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']);
        } else if ($this->contract['type'] === 'apps') {
            $servicesClass =
                'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']);
        }

        $file =
'<?php

namespace ' . $servicesClass . '\\' . ucfirst($this->contract['provider_name']) . ';

class ' . ucfirst($this->contract['provider_name']) . '
{
    //
}';

        try {
            $this->localContent->write(
                $this->servicesDirectory .
                ucfirst($this->contract['provider_name']) . '/' .
                ucfirst($this->contract['provider_name']) . '.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    public function writeCategoryOperationFileContent()
    {
        try {
            $existingFile = $this->localContent->read(
                $this->servicesDirectory .
                ucfirst($this->contract['category']) . 'Operations.php'
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToReadFile | \Exception $exception) {
            $existingFile = false;
        }

        if ($existingFile) {
            return;
        }

        if ($this->contract['type'] === 'system') {
            $servicesClass =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']);
        } else if ($this->contract['type'] === 'apps') {
            $servicesClass =
                'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']);
        }

        $file =
'<?php

namespace ' . $servicesClass . ';

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Types\BaseType;

class ' . ucfirst($this->contract['category']) . 'Operations extends BaseType
{
    //
}';

        try {
            $this->localContent->write(
                $this->servicesDirectory .
                ucfirst($this->contract['category']) . 'Operations.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    public function writeCategoryRESTFileContent()
    {
        try {
            $existingFile = $this->localContent->read(
                $this->servicesDirectory .
                ucfirst($this->contract['category']) . 'RESTService.php'
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToReadFile | \Exception $exception) {
            $existingFile = false;
        }

        if ($existingFile) {
            return;
        }

        if ($this->contract['type'] === 'system') {
            $servicesClass =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']);
        } else if ($this->contract['type'] === 'apps') {
            $servicesClass =
                'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']);
        }

        $file =
'<?php

namespace ' . $servicesClass . ';

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\BaseRESTService;

class ' . ucfirst($this->contract['category']) . 'RESTService extends BaseRESTService
{
    //
}';

        try {
            $this->localContent->write(
                $this->servicesDirectory .
                ucfirst($this->contract['category']) . 'RESTService.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    public function writeCategoryTypeFileContent()
    {
        try {
            $existingFile = $this->localContent->read(
                $this->servicesDirectory .
                ucfirst($this->contract['category']) . 'Type.php'
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToReadFile | \Exception $exception) {
            $existingFile = false;
        }

        if ($existingFile) {
            return;
        }

        if ($this->contract['type'] === 'system') {
            $servicesClass =
                'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\\' . ucfirst($this->contract['category']);
        } else if ($this->contract['type'] === 'apps') {
            $servicesClass =
                'Apps\Dash\Packages\System\Api\Apis\\' . ucfirst($this->contract['category']);
        }

        $file =
'<?php

namespace ' . $servicesClass . ';

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Types\BaseType;

class ' . ucfirst($this->contract['category']) . 'Type extends BaseType
{
    //
}';

        try {
            $this->localContent->write(
                $this->servicesDirectory .
                ucfirst($this->contract['category']) . 'Type.php',
                $file
            );
        } catch (\League\Flysystem\FilesystemException | \League\Flysystem\UnableToWriteFile $exception) {
            throw $exception;
        }
    }
}