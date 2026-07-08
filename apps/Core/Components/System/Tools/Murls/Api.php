<?php

namespace Apps\Core\Components\System\Tools\Murls;

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

            $this->addResponse('Ok', 0, ['data' => $murls]);

            return;
        }

        //Get All Murls
        $data = $this->getRows($this->murls);

        $this->addResponse('Ok', 0, ['data' => $data ?? []]);
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