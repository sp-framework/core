<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesPages;

class Pages extends BasePackage
{
    protected $modelToUse = BasepackagesPages::class;

    protected $packageName = 'pages';

    public $pages;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('pages', 'core')) {
                $this->pages = $this->opCache->getCache('pages', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('pages', $this->pages, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function addPage(array $data)
    {
        if (strpos($data['html_code'], '<script>') || strpos($data['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in pages.', 1, []);

            return false;
        }

        if ($data['content_source'] === 'file') {
            $data['html_code'] = '';
        } else if ($data['content_source'] === 'code') {
            $data['html_file'] = '';
        }

        if (isset($data['visible_on_apps'])) {
            if (is_string($data['visible_on_apps'])) {
                $data['visible_on_apps'] = $this->helper->decode($data['visible_on_apps'], true);
            }

            if (isset($data['visible_on_apps']['data'])) {
                $data['visible_on_apps'] = $this->helper->encode($data['visible_on_apps']['data']);
            } else {
                $data['visible_on_apps'] = $this->helper->encode($data['visible_on_apps']);
            }
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

        if ($data['content_source'] === 'file') {
            $data['html_code'] = '';
        } else if ($data['content_source'] === 'code') {
            $data['html_file'] = '';
        }

        if (isset($data['visible_on_apps'])) {
            if (is_string($data['visible_on_apps'])) {
                $data['visible_on_apps'] = $this->helper->decode($data['visible_on_apps'], true);
            }

            if (isset($data['visible_on_apps']['data'])) {
                $data['visible_on_apps'] = $this->helper->encode($data['visible_on_apps']['data']);
            } else {
                $data['visible_on_apps'] = $this->helper->encode($data['visible_on_apps']);
            }
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

    public function processWidgets($page)
    {
        preg_match_all('/{{getWidgetContent.*?}}/', $page['html_code'], $pageHasWidgets);

        if (isset($pageHasWidgets[0]) && count($pageHasWidgets[0]) > 0) {
            $pageWidgetsContent = [];

            foreach ($pageHasWidgets[0] as $pageWidgetRef) {
                preg_match('/\d/', $pageWidgetRef, $pageWidgetId);

                if (!isset($pageWidgetId[0])) {
                    continue;
                }

                $pageWidget = null;

                $pageWidget = $this->basepackages->pageswidgets->getById((int) $pageWidgetId[0]);

                if ($pageWidget) {
                    $pageWidget['getWidgetData'] = true;

                    $pageWidgetContent = $this->basepackages->widgets->getWidget($pageWidget['widget_id'], 'content', $pageWidget);

                    if (isset($pageWidgetContent['content'])) {
                        $page['html_code'] = str_replace($pageWidgetRef, trim($pageWidgetContent['content']), $page['html_code']);
                    } else {
                        $page['html_code'] = str_replace($pageWidgetRef, '<p>Please set html code for this widget!</p>', $page['html_code']);
                    }
                } else {
                    $page['html_code'] = str_replace($pageWidgetRef, '<p>Widget with ID ' . $pageWidgetId[0] . ' not found!</p>', $page['html_code']);
                }
            }
        }

        return $page;
    }
}