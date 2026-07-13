<?php
/**
 * API file for Core component.
 *
 * {@inheritDoc}
 */

namespace Apps\Core\Components\System\Core;

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
    public function viewAction()
    {
        $data = $this->core->core;
        unset($data['settings']);
        unset($data['id']);

        //Get Core information
        $this->addResponse('Ok', 0, ['data' => $data ?? []]);
    }
}