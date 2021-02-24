<?php

namespace Apps\Dash\Packages\Devtools\Api\Contracts;

use Apps\Dash\Packages\Devtools\Api\Contracts\Model\DevtoolsApiContracts;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class Contracts extends BasePackage
{
    protected $modelToUse = DevtoolsApiContracts::class;

    protected $packageName = 'contracts';

    protected $contractDirectory = null;

    protected $ebayServicesClass = '\\Apps\Dash\Packages\System\Api\Apis\Ebay';

    protected $ebayServicesDirectory = 'apps/Dash/Packages/System/Api/Apis/Ebay/';

    protected $contractFilename;

    public $contracts;

    public function init()
    {
        $this->app = $this->apps->getAppInfo();

        $this->getContractDirectory();

        return $this;
    }

    protected function setContractDirectory($directory = null)
    {
        if ($directory) {
            $this->contractDirectory = base_path($directory);
        } else {
            //Default Directory
            $this->contractDirectory = 'apps/Dash/Packages/System/Api/Contracts/';
        }

        return $this->contractDirectory;
    }

    public function getContractDirectory($directory = null)
    {
        if (!$this->contractDirectory) {
            return $this->setContractDirectory($directory);
        }

        return $this->contractDirectory;
    }

    protected function checkLink($link)
    {
        $filename = Arr::last(explode('/', $link));

        if (strpos($filename, '.json')) {
            return $filename;
        }

        return false;
    }

    public function addContract(array $data)
    {
        if (isset($data['link']) && $data['link'] !== '') {

            $this->contractFilename = $this->checkLink($data['link']);

            $data['filename'] = $this->contractDirectory . $this->contractFilename;

            if (!$data['filename']) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Only filename with extension .json are accepted';

                return false;
            }

            try {
                $response = $this->remoteContent->request('GET', $data['link']);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $e->getMessage();

                return;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $e->getMessage();

                return;
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $e->getMessage();

                return;
            }

            if ($response->getStatusCode() === 200 &&
                $response->getHeaderLine('content-type') === 'application/json'
            ) {
                $data['content'] = 'See Link';

                $body = $response->getBody();

                $this->localContent->put($data['filename'], $body);
            }

            $bodyArr = Json::decode($body, true);

            $title = $bodyArr['info']['title'];

            $data['name'] = ucfirst(strtolower(str_replace(' ', '', $title)));
        } else {
            $data['link'] = 'See Content';

            $data['filename'] = strtolower(str_replace(' ', '_', $data['name']) . '-' . Str::random(Str::RANDOM_ALNUM) . '.json');

            $this->localContent->put($data['filename'], $data['content']);
        }

        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' api contract.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding api contract.';
        }
    }

    public function updateContract(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' api contract.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating api contract.';
        }
    }

    public function removeContract(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed api contract.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing api contract.';
        }
    }

    public function generateClassesFromContract($id)
    {
        $contract = $this->getById($id);

        if ($contract['link'] !== 'See Content') {
            $contract['content'] = $this->localContent->read($contract['filename']);
        }

        $contract['content'] = Json::decode($contract['content'], true);

        $this->contract = $contract;

        $this->createServiceDirectories();

        $this->writeBaseServicesFileContent($this->buildBaseServicesFileContent());

        $this->writeServicesFileContent($this->buildServicesFileContent());

        $this->buildTypesFile();

        $this->buildOperationsFile();
    }

    protected function buildBaseServicesFileContent()
    {
        $file = '';

        $file .= $this->generateBaseServicesHeader();

        $file .=
'    protected static $endPoints =
        ' . $this->generateProperties('endPoints') . ';';

        $file .= '

    const HDR_AUTHORIZATION = \'Authorization\';

    const HDR_MARKETPLACE_ID = \'X-EBAY-C-MARKETPLACE-ID\';

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
            ]
        ];
    }

    protected function getEbayHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = \'Bearer \' . $this->getConfig(\'user_access_token\');

        // Add optional headers.
        if ($this->getConfig(\'marketplaceId\')) {
            $headers[self::HDR_MARKETPLACE_ID] = $this->getConfig(\'marketplaceId\');
        }

        return $headers;
    }
}';
        return $file;
    }

    protected function buildServicesFileContent()
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

    protected function generateBaseServicesHeader()
    {
        return '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Services;

use Apps\Dash\Packages\System\Api\Base\BaseRestService;

class ' . $this->contract['name'] . 'BaseService extends BaseRestService
{
';
    }

    protected function generateServicesHeader()
    {
        return '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Services\\' . $this->contract['name'] . 'BaseService;

class ' . $this->contract['name'] . 'Service extends ' . $this->contract['name'] . 'BaseService
{
    const API_VERSION = \'v1\';

';
    }

    protected function generateProperties($type)
    {
        if ($type === 'endPoints') {
            $endpoints = [];

            foreach ($this->contract['content']['servers'] as $serverKey => $server) {
                $url = explode('{', $server['url']);
                $urlKey = str_replace('}', '', $url[1]);

                $description = strtolower($server['description']);

                //Dirty fix to apply production/sandbox as key
                if (strpos($description, 'prod') !== false) {
                    $endpoints['production'] = $url[0] . $server['variables'][$urlKey]['default'];
                } else if (strpos($description, 'sand') !== false) {
                    $endpoints['sandbox'] = $url[0] . $server['variables'][$urlKey]['default'];
                }
            }

            return $this->generatePropertyFromArray($endpoints);

        } else if ($type === 'operations') {
            $operations = [];

            foreach ($this->contract['content']['paths'] as $pathKey => $path) {
                if (is_array($path)) {
                    foreach ($path as $methodKey => $method) {
                        $operations[ucfirst($method['operationId'])] = [];
                        $operations[ucfirst($method['operationId'])]['method'] = strtoupper($methodKey);
                        $operations[ucfirst($method['operationId'])]['resource'] = ltrim($pathKey, '/');
                        $operations[ucfirst($method['operationId'])]['responseClass'] =
                            $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestResponse';
                        if (isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0) {
                            $operations[ucfirst($method['operationId'])]['params'] = [];
                            foreach ($method['parameters'] as $parameterKey => $parameter) {
                                if ($parameter['in'] === 'path' || $parameter['in'] === 'query') {
                                    $operations[ucfirst($method['operationId'])]['params'][$parameter['name']] = [];
                                    $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'] = [];
                                    array_push($operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['valid'], $parameter['schema']['type']);
                                    if ($parameter['required'] === true) {
                                        $operations[ucfirst($method['operationId'])]['params'][$parameter['name']]['required'] = $parameter['required'];
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

    protected function getOperationMethods()
    {
        $methods = '';

        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    $methods .=
                    '

    public function ' . $method['operationId'] . '(' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestRequest $request)
    {
        return $this->' . $method['operationId'] . 'Async($request)->wait();
    }

    public function ' . $method['operationId'] . 'Async(' . $this->ebayServicesClass . '\\' . $this->contract['name'] . '\Operations\\' . ucfirst($method['operationId']) . 'RestRequest $request)
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

    protected function createServiceDirectories()
    {
        if (!$this->localContent->has($this->ebayServicesDirectory . $this->contract['name'])) {
            $this->localContent->createDir($this->ebayServicesDirectory . $this->contract['name']);
        }
        if (!$this->localContent->has($this->ebayServicesDirectory . $this->contract['name'] . '/Services')) {
            $this->localContent->createDir($this->ebayServicesDirectory . $this->contract['name'] . '/Services');
        }
        if (!$this->localContent->has($this->ebayServicesDirectory . $this->contract['name'] . '/Enums')) {
            $this->localContent->createDir($this->ebayServicesDirectory . $this->contract['name'] . '/Enums');
        }
        if (!$this->localContent->has($this->ebayServicesDirectory . $this->contract['name'] . '/Types')) {
            $this->localContent->createDir($this->ebayServicesDirectory . $this->contract['name'] . '/Types');
        }
        if (!$this->localContent->has($this->ebayServicesDirectory . $this->contract['name'] . '/Operations')) {
            $this->localContent->createDir($this->ebayServicesDirectory . $this->contract['name'] . '/Operations');
        }
    }

    protected function writeBaseServicesFileContent($file)
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

    protected function writeServicesFileContent($file)
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

    protected function buildTypesFile()
    {
        foreach ($this->contract['content']['components']['schemas'] as $typeKey => $type) {
            $this->writeTypesFileContent($typeKey, $this->buildTypesFileContent($typeKey, $type));
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

        foreach ($type['properties'] as $propertyKey => $property) {
            $propertyTypes[$propertyKey] = [];
            if (isset($property['type'])) {
                if ($property['type'] === 'string' || $property['type'] === 'integer') {
                    $propertyTypes[$propertyKey]['type'] = $property['type'];
                    $propertyTypes[$propertyKey]['repeatable'] = false;
                } else if ($property['type'] === 'array') {
                    if (isset($property['items'])) {
                        foreach ($property['items'] as $itemKey => $item) {
                            if ($itemKey === '$ref') {
                                $propertyTypes[$propertyKey]['type'] = $this->getRefClass($item);
                            }
                        }
                    }
                    $propertyTypes[$propertyKey]['repeatable'] = true;
                }
            } else if (isset($property['$ref'])) {
                $propertyTypes[$propertyKey]['type'] = $this->getRefClass($property['$ref']);
                $propertyTypes[$propertyKey]['repeatable'] = false;
            }
            $propertyTypes[$propertyKey]['attribute'] = false;
            $propertyTypes[$propertyKey]['elementName'] = $propertyKey;
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

    protected function getRefClass($item)
    {
        $itemArr = explode('/', $item);

        return 'Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Types\\' . Arr::last($itemArr);

    }

    protected function buildOperationsFile()
    {
        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    if (isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0) {
                        $requestParams = [];
                        foreach ($method['parameters'] as $parameterKey => $parameter) {
                            if ($parameter['in'] === 'path' || $parameter['in'] === 'query') {
                                $requestParams[$parameter['name']] = [];
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
                                $requestParams[$parameter['name']]['attribute'] = false;
                                $requestParams[$parameter['name']]['elementName'] = $parameter['name'];
                            }
                        }
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

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Operations;

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
        foreach ($this->contract['content']['paths'] as $pathKey => $path) {
            if (is_array($path)) {
                foreach ($path as $methodKey => $method) {
                    $file = '<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Operations;

use Apps\Dash\Packages\System\Api\Base\Traits\HttpHeadersTrait;
use Apps\Dash\Packages\System\Api\Base\Traits\StatusCodeTrait;';

        if ($methodKey === 'get') {
            $file .= '

class ' . ucfirst($method['operationId']) . 'RestResponse extends \\' .
                $this->getRefClass(
                    $method['responses']['200']['content']['application/json']['schema']['$ref']
                );
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
            \'type\' => \'Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Types\Error\',
            \'repeatable\' => true,
            \'attribute\' => false,
            \'elementName\' => \'errors\'
        ],
        \'warnings\' => [
            \'type\' => \'Apps\Dash\Packages\System\Api\Apis\Ebay\\' . $this->contract['name'] . '\Types\Error\',
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