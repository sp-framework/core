<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\Url;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMurls;

class Murls extends BasePackage
{
    protected $modelToUse = BasepackagesMurls::class;

    protected $packageName = 'murls';

    public $murls;

    public function addMurl($data)
    {
        //
    }

    public function updateMurl($data)
    {
        //
    }

    public function removeMurl($data)
    {
        //
    }

    public function generateMurl($data)
    {
        $this->validation->init()->add('app_id', PresenceOf::class, ["message" => "Please provide app information."]);
        $this->validation->add('domain_id', PresenceOf::class, ["message" => "Please provide domain information."]);
        $this->validation->add('url', PresenceOf::class, ["message" => "Please provide URL."]);

        if (!$this->doValidation($data)) {
            return false;
        }

        $this->validation->init()->add('url', Url::class, ["message" => "VALID."]);//We dont want valid URL

        if ($this->doValidation($data)) {//URL is valid which is incorrect as we dont want URL with domain or http
            $this->addResponse('Please provide URL without http scheme or domain.', 1);

            return false;
        }

        if (isset($data['human_readable']) && $data['human_readable'] == true) {
            //
        } else {
            //
        }
    }

    protected function doValidation($data)
    {
        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }

            $this->addResponse($messages, 1);

            return false;
        }

        return true;
    }
}