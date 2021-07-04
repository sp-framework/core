<?php

namespace Apps\Dash\Packages\Ims\Stock\Takes;

use Apps\Dash\Packages\Devtools\Api\Contracts\ContractsOAPI;
use Apps\Dash\Packages\Devtools\Api\Contracts\ContractsXML;
use Apps\Dash\Packages\Devtools\Api\Contracts\Model\DevtoolsApiContracts;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class Takes extends BasePackage
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

            if ($response->getStatusCode() === 200) {

                $data['content'] = 'See Link';

                if ($response->getHeaderLine('content-type') === 'application/json') {

                    $bodyArr = Json::decode($body, true);

                    $title = $bodyArr['info']['title'];

                    $data['name'] = ucfirst(strtolower(str_replace(' ', '', $title)));

                } else if ($response->getHeaderLine('content-type') === 'application/wsdl+xml') {
                    $body = $response->getBody();

                    $this->localContent->write($data['filename'], $body);

                } else {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'content-type received is incorrect';

                    return;
                }
            } else {

                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Status code received is not 200';

                return;
            }
        } else {
            $data['link'] = 'See Content';

            $data['filename'] = strtolower(str_replace(' ', '_', $data['name']) . '-' . Str::random(Str::RANDOM_ALNUM) . '.json');

            $this->localContent->write($data['filename'], $data['content']);
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

        if (!$this->contract['wsdl_convert'] || $this->contract['wsdl_convert'] == 0) {
            $oAPIContract = (new ContractsOAPI)->init($this->contract, $this->localContent);

            $oAPIContract->writeBaseServicesFileContent($oAPIContract->buildBaseServicesFileContent());

            $oAPIContract->writeServicesFileContent($oAPIContract->buildServicesFileContent());

            $oAPIContract->buildTypesFile();

            $oAPIContract->buildOperationsFile();
        } else if ($this->contract['wsdl_convert'] == 1) {
            $xmlContract = (new ContractsXML)->init($this->contract, $this->localContent);

            $xmlContract->writeBaseServicesFileContent($xmlContract->buildBaseServicesFileContent());

            $xmlContract->writeServicesFileContent($xmlContract->buildServicesFileContent());

            $xmlContract->buildTypesFile();

            $xmlContract->buildOperationsFile();
        }
    }

    protected function createServiceDirectories()
    {
        if (!$this->localContent->fileExists($this->ebayServicesDirectory . $this->contract['name'])) {
            $this->localContent->createDirectory($this->ebayServicesDirectory . $this->contract['name']);
        }
        if (!$this->localContent->fileExists($this->ebayServicesDirectory . $this->contract['name'] . '/Services')) {
            $this->localContent->createDirectory($this->ebayServicesDirectory . $this->contract['name'] . '/Services');
        }
        if (!$this->localContent->fileExists($this->ebayServicesDirectory . $this->contract['name'] . '/Enums')) {
            $this->localContent->createDirectory($this->ebayServicesDirectory . $this->contract['name'] . '/Enums');
        }
        if (!$this->localContent->fileExists($this->ebayServicesDirectory . $this->contract['name'] . '/Types')) {
            $this->localContent->createDirectory($this->ebayServicesDirectory . $this->contract['name'] . '/Types');
        }
        if (!$this->localContent->fileExists($this->ebayServicesDirectory . $this->contract['name'] . '/Operations')) {
            $this->localContent->createDirectory($this->ebayServicesDirectory . $this->contract['name'] . '/Operations');
        }
    }
}