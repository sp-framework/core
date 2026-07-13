<?php
/**
 * API file for Mulrs component.
 *
 * {@inheritDoc}
 */
namespace Apps\Core\Components\System\Tools\Murls;

use OpenApi\Attributes as OA;
use System\Base\BaseApi;

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
                    return $this->throwIdNotFound();
                }
            }

            $this->addResponse('Ok', 0, $murls ??[]);

            return;
        }

        //Get All Murls
        $data = $this->getRows($this->murls);

        $this->addResponse('Ok', 0, $data ?? []);
    }

    /**
     * @api_acl(name=add)
     */
    public function addAction()
    {
        trace(['add']);
    }

    /**
     * @api_acl(name=update)
     */
    public function updateAction()
    {
        trace(['update']);
    }

    /**
     * @api_acl(name=remove)
     */
    public function removeAction()
    {
        trace(['remove']);
    }
}