<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesPages;

class Pages extends BasePackage
{
    protected $modelToUse = BasepackagesPages::class;

    protected $packageName = 'pages';

    public $pages;

    protected $pagesDir;

    protected $engine;

    protected $renderer;

    public function addPage(array $data)
    {
        if (strpos($data['html_code'], '<script>') || strpos($data['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in pages.', 1, []);

            return false;
        }

        if ($this->add($data)) {
            $this->addResponse('Added new page');
        } else {
            $this->addResponse('Error adding new page', 1);
        }
    }

    public function updatePage(array $data)
    {
        if (strpos($data['html_code'], '<script>') || strpos($data['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in pages.', 1, []);

            return false;
        }

        $page = $this->getById($data['id']);

        if (!$page) {
            $this->addResponse('Page with ID does not exist', 1);

            return false;
        }

        if ($this->update($data)) {
            $this->addResponse('Updated new page');
        } else {
            $this->addResponse('Error updating page', 1);
        }
    }

    public function removePage(array $data)
    {
        $page = $this->getById($data['id']);

        if (!$page) {
            $this->addResponse('Page with ID does not exist', 1);

            return false;
        }

        $murls = $this->basepackages->murls->getMurlByUrl('pages/q/id/' . $data['id']);

        if ($murls) {
            foreach ($murls as $murl) {
                if (!$this->basepackages->murls->removeMurl($murl)) {
                    $this->addResponse('Cannot remove murl for the page!', 1);

                    return false;
                }
            }
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Page removed');
        } else {
            $this->addResponse('Error removing page', 1);
        }
    }
}