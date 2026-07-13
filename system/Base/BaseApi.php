<?php
/**
 * Base file for all API classes.
 *
 * @author Guru (email@oyeaussie.dev)
 */
namespace System\Base;

use Phalcon\Mvc\Controller;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ApiResponse', title: 'Api response', description: 'Api response')]
abstract class BaseApi extends Controller
{
    #[OA\Property(title: 'responseCode', description: 'Response Code', format: 'int32')]
    private int $responseCode;

    #[OA\Property(title: 'responseMessage', description: 'Response Message')]
    private string $responseMessage;

    #[OA\Property(title: 'responseData', description: 'Response Data')]
    private object $responseData;

    protected $getQueryArr = [];

    protected $apiResponse = [];

    protected function onConstruct()
    {
        if (count($this->dispatcher->getParams()) > 0) {
            $this->buildGetQueryParamsArr();
        }
    }

    protected function buildGetQueryParamsArr()
    {
        if ($this->request->isGet()) {
            //Murl
            if ($this->apps->isMurl) {
                $arr = $this->helper->chunk(
                    explode('/', explode('/q/', trim($this->apps->isMurl['url'], '/'))[1]),
                    2
                );
            } else {
                $arr = $this->helper->chunk($this->dispatcher->getParams(), 2);
            }

            foreach ($arr as $value) {
                if (isset($value[0]) && isset($value[1])) {
                    if (is_string($value[0]) && is_string($value[1])) {
                        if ($value[1] === '{id}') {
                            $uriId = 0;
                            if (str_contains(trim($this->request->getURI(), '/'), '-')) {
                                $uriArr = explode('-', trim($this->request->getURI(), '/'));

                                if (count($uriArr) > 1) {
                                    if (isset($apiUri)) {
                                        $murlApiUri = $this->helper->first($uriArr);
                                    }
                                    $murlUri = $this->helper->first($uriArr);
                                    $uriId = (int) $this->helper->last($uriArr);
                                }
                            }

                            if ($uriId > 0) {
                                $this->getQueryArr[$value[0]] = $uriId;
                            }
                        } else {
                            $this->getQueryArr[$value[0]] = $value[1];
                        }
                    }
                }
            }

            // getQuery - /admin/setup/q/id/2/filter/4/search//layout/0
            // Will Result to
            // array (size=4)
            //   'id' => string '2' (length=1)
            //   'filter' => string '4' (length=1)
            //   'search' => string '' (length=0)
            //   'layout' => string '0' (length=1)
        }
    }

    protected function getData()
    {
        return $this->getQueryArr;
    }

    protected function postData()
    {
        return $this->request->getPost();
    }

    protected function putData()
    {
        return $this->request->getPut();
    }

    protected function addResponse($responseMessage, int $responseCode = 0, $responseData = null)
    {
        $this->apiResponse['responseMessage'] = $responseMessage;
        $this->apiResponse['responseCode'] = $responseCode;
        $this->apiResponse['responseData'] = $responseData;

        return $this->sendJson($responseCode);
    }

    protected function setHeader($responseCode = 200)
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setHeader('Cache-Control', 'no-store');

        if ($responseCode !== 0 || $responseCode !== 1) {
            $this->response->setStatusCode($responseCode);
        }
    }

    protected function sendJson($responseCode)
    {
        $this->setHeader($responseCode);

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setHeader('Cache-Control', 'no-store');

        if ($this->response->isSent() !== true) {
            $this->response->setJsonContent($this->apiResponse);

            return $this->response->send();
        }
    }

    public function getRows($package, array $columnsForTable = []) {
        if (isset($this->postData()['columns'])) {
            $columnsForTable = array_replace($columnsForTable, explode(',', $this->request->getPost()['columns']));
        }

        $conditions =
            [
                'columns' => $columnsForTable
            ];

        if (isset($this->postData()['conditions'])) {
            $conditions['conditions'] = $this->postData()['conditions'];
        }

        try {
            $rows = $package->getPaged($conditions)->getItems();
        } catch (\Exception $e) {
            if ($this->config->logs->exceptions) {
                $this->logger->logExceptions->critical(json_trace($e));
            }

            $this->addResponse('API Error! Contact administrator.', 1);

            return;
        }

        return ['rows' => $rows, 'counters' => $package->packagesData->paginationCounters];
    }
}