<?php

namespace Apps\Core\Packages\Adminltetags\Tags;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Contacts extends Adminltetags
{
    protected $params;

    protected $content = '';

    protected $contactsParams = [];

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        // contactFieldType - as per contactFieldType, code is generated - single/multiple
        if (!isset($this->params['contactFieldType'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">Error: contactFieldType missing</span>';
            return;
        }

        try {
            $contact = 'Apps\\Core\\Packages\\Adminltetags\\Tags\\Contacts\\' . ucfirst($this->params['contactFieldType']);

            $this->content .= (new $contact($this->view, $this->tag, $this->links, $this->escaper, $this->params, $this->contactsParams))->getContent();

        } catch (\Exception $e) {
            throw $e;
        }

    }
}