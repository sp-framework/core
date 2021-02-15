<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

class Addresses extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $addressesParams = [];

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        // addressFieldType - as per addressFieldType, code is generated - single/multiple
        if (!isset($this->params['addressFieldType'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">Error: addressFieldType missing</span>';
            return;
        }

        $this->addressesParams['addressTypes'] = $this->basepackages->addresstypes->addresstypes;

        try {
            $address = 'Apps\\Dash\\Packages\\AdminLTETags\\Tags\\Addresses\\' . ucfirst($this->params['addressFieldType']);

            $this->content .= (new $address($this->view, $this->tag, $this->links, $this->escaper, $this->params, $this->addressesParams))->getContent();

        } catch (\Exception $e) {
            throw $e;
        }

    }
}