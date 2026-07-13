<?php
/**
 * API file for Core component.
 *
 * {@inheritDoc}
 */

namespace Apps\Core\Components\System\Core;

use OpenApi\Attributes as OA;
use System\Base\BaseApi;

class Api extends BaseApi
{
    public function initialize()
    {
        //
    }

    /**
     * @api_acl(name=view)
     */
    #[OA\Get(
        path: '/system/core',
        operationId: 'viewCore',
        description: 'Returns Core Information',
        summary: 'Returns Core Information',
        tags: ['core'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: new OA\JsonContent(
                    ref: BaseApi::class
                )
            )
        ]
    )]
    public function viewAction()
    {
        $data = $this->core->core;
        unset($data['settings']);
        unset($data['id']);

        //Get Core information
        $this->addResponse('Ok', 0, $data ?? []);
    }
}