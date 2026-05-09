<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Pages\BasepackagesPagesWidgets;

class PagesWidgets extends BasePackage
{
    protected $modelToUse = BasepackagesPagesWidgets::class;

    protected $packageName = 'pageswidgets';

    public $pageswidgets;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('pageswidgets', 'core', $this->domains->getDomain()['name'])) {
                $this->pageswidgets = $this->opCache->getCache('pageswidgets', 'core', $this->domains->getDomain()['name']);
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('pageswidgets', $this->pageswidgets, 'core', $this->domains->getDomain()['name']);
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function getPageWidgetById($id, $getWidgetDetails = true)
    {
        $pageWidget = $this->getById($id);

        if ($pageWidget && isset($pageWidget['widget_id'])) {
            $pageWidget['widget'] = $this->basepackages->widgets->getById($pageWidget['widget_id']);
        }

        return $pageWidget;
    }

    public function addPagesWidget(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added new page widget');
        } else {
            $this->addResponse('Error adding new page widget', 1);
        }
    }

    public function updatePagesWidget(array $data)
    {
        $pageWidget = $this->getById($data['id']);

        if (!$pageWidget) {
            $this->addResponse('Page widget with ID does not exist', 1);

            return false;
        }

        if ($this->update($data)) {
            $this->addResponse('Updated new page widget');
        } else {
            $this->addResponse('Error updating page widget', 1);
        }
    }

    public function removePagesWidget(array $data)
    {
        $pageWidget = $this->getById($data['id']);

        if (!$pageWidget) {
            $this->addResponse('Page with ID does not exist', 1);

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Page widget removed');
        } else {
            $this->addResponse('Error removing page widget', 1);
        }
    }
}