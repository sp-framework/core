<?php
/**
 * API file for Mulrs component.
 *
 * {@inheritDoc}
 */
namespace Apps\Core\Components\System\Tools\Murls;

use OpenApi\Attributes as OA;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\BaseApi;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMurls;

class Api extends BaseApi
{
    protected $murls;

    public function initialize()
    {
        $this->murls = $this->basepackages->murls->init();
    }

    /**
     * @api_acl(name=view)
     */
    #[OA\Get(
        path: '/system/tools/murls',
        operationId: 'viewMurls',
        description: 'Returns All Murls Information',
        summary: 'Returns All Murls Information',
        tags: ['murls'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 403,
                description: 'permission denied',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            )
        ]
    ),
    OA\Get(
        path: '/system/tools/murls/q/id/{murlId}',
        operationId: 'viewMurlById',
        description: 'Returns Murl Information',
        summary: 'Returns Murl Information',
        tags: ['murls'],
        parameters: [
            new OA\Parameter(
                name: 'murlId',
                description: 'ID of murl that needs to be fetched',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 403,
                description: 'permission denied',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'not found',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            )
        ]
    )]
    public function viewAction()
    {
        $this->initialize();

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $murls = $this->murls->getById($this->getData()['id']);

                if (!$murls) {
                    return $this->addResponse('Id ' . $this->getData()['id'] . ' not found!', 404);
                }
            }

            return $this->addResponse('Ok', 0, $murls ?? []);
        }

        //Get All Murls
        $data = $this->getRows($this->murls);

        return $this->addResponse('Ok', 0, $data ?? []);
    }

    /**
     * @api_acl(name=add)
     */
    #[OA\Post(
        path: '/system/tools/murls/add',
        operationId: 'addMurl',
        description: 'Add Murl',
        summary: 'Add Murl',
        tags: ['murls'],
        requestBody: new OA\RequestBody(
            description: 'Add murl',
            required: true,
            content: new OA\JsonContent(
                ref: BasepackagesMurls::class
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 403,
                description: 'permission denied',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'not found',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Incorrect murl data provided',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            )
        ]
    )]
    public function addAction()
    {
        $this->initialize();

        $data = $this->postData();

        $validated = $this->murls->validateDataWithMetaData(data: $data, ignoreFields: ['id']);

        if ($validated !== true) {
            $this->addResponse($validated, 400);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] != 0) {
            $api = $this->api->getById($data['api_id']);

            if (!$api) {
                $this->addResponse('API ID Incorrect', 1);

                return false;
            }

            $data['app_id'] = $api['app_id'];
            $data['domain_id'] = $api['domain_id'];
        }

        $data['account_id'] = $this->api->account()['id'];

        try {
            if ($this->murls->add($data)) {
                $this->addResponse('Murl added', 0, $this->murls->packagesData->last);

                return true;
            }
        } catch (\throwable $e) {
            $this->logException($e);

            if (str_contains($e->getMessage(), 'Duplicate')) {
                $this->addResponse('Duplicate entry found!', 400, ['error' => $e->getMessage()]);
            }

            $this->addResponse('Error adding murl!', 400, ['error' => $e->getMessage()]);

            return false;
        }

        $this->addResponse('Error adding murl', 1);
    }

    /**
     * @api_acl(name=update)
     */
    #[OA\Put(
        path: '/system/tools/murls/update',
        operationId: 'updateMurl',
        description: 'Update Murl',
        summary: 'Update Murl',
        tags: ['murls'],
        requestBody: new OA\RequestBody(
            description: 'Update murl',
            required: true,
            content: new OA\JsonContent(
                ref: BasepackagesMurls::class
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 403,
                description: 'permission denied',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'not found',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Incorrect murl data provided',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            )
        ]
    )]
    public function updateAction()
    {
        $this->initialize();

        $data = $this->postData();
        if (!isset($data['id'])) {
            return $this->addResponse('Id not provided!', 400);
        }

        $murl = $this->murls->getById($data['id']);

        if (!$murl) {
            return $this->addResponse('Id ' . $data['id'] . ' not found!', 404);
        }

        $validated = $this->murls->validateDataWithMetaData($data);

        if ($validated !== true) {
            $this->addResponse($validated, 400);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] != 0) {
            $api = $this->api->getById($data['api_id']);

            if (!$api) {
                $this->addResponse('API ID Incorrect', 1);

                return false;
            }

            $data['app_id'] = $api['app_id'];
            $data['domain_id'] = $api['domain_id'];
        }

        $data['account_id'] = $this->api->account()['id'];

        try {
            if ($this->murls->update($data)) {
                $this->addResponse('Murl updated', 0, $this->murls->packagesData->last);

                return true;
            }
        } catch (\throwable $e) {
            $this->logException($e);

            if (str_contains($e->getMessage(), 'Duplicate')) {
                $this->addResponse('Duplicate entry found!', 400, ['error' => $e->getMessage()]);
            }

            $this->addResponse('Error adding murl!', 400, ['error' => $e->getMessage()]);

            return false;
        }

        $this->addResponse('Error updating murl', 1);
    }

    /**
     * @api_acl(name=remove)
     */
    #[OA\Post(
        path: '/system/tools/murls/remove',
        operationId: 'removeMurl',
        description: 'Remove Murl',
        summary: 'Remove Murl',
        tags: ['murls'],
        requestBody: new OA\RequestBody(
            description: 'Remove murl',
            required: true,
            content: new OA\JsonContent(
                ref: BasepackagesMurls::class
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 403,
                description: 'permission denied',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'not found',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Incorrect murl data provided',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            )
        ]
    )]
    public function removeAction()
    {
        $this->initialize();

        $data = $this->postData();
        if (!isset($data['id'])) {
            return $this->addResponse('Id not provided!', 400);
        }

        $murl = $this->murls->getById($data['id']);

        if (!$murl) {
            return $this->addResponse('Id ' . $data['id'] . ' not found!', 404);
        }

        try {
            if ($this->murls->remove($murl['id'])) {
                $this->addResponse('Murl removed', 0, ['id' => $murl['id']]);

                return true;
            }
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 500);

            return false;
        }

        $this->addResponse('Error removing murl', 1);
    }
}