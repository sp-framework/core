<?php

namespace Apps\Core\Packages\AdminLTETags\Tags;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;

class Employees extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $employeesParams = [];

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        // employeeFieldType - as per employeeFieldType, code is generated - single/multiple
        if (!isset($this->params['employeeFieldType'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">Error: employeeFieldType missing</span>';
            return;
        }

        try {
            $employee = 'Apps\\Core\\Packages\\AdminLTETags\\Tags\\Employees\\' . ucfirst($this->params['employeeFieldType']);

            $this->content .= (new $employee($this->view, $this->tag, $this->links, $this->escaper, $this->params, $this->employeesParams))->getContent();

        } catch (\Exception $e) {
            throw $e;
        }

    }
}