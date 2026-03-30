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

        if ($this->update($data)) {
            $this->addResponse('Updated new page');
        } else {
            $this->addResponse('Error updating page', 1);
        }
    }

    public function removePage(array $data)
    {
        //Remove Murls associated with the page.
    }

    public function testPage($data)
    {
        $testData = [];

        if ($this->postData()['test_data'] !== '') {
            try {
                $testData = $this->helper->decode($this->postData()['test_data'], true);
            } catch (\Exception $e) {
                $this->addResponse('Test data format needs to be JSON.', 1, []);

                return false;
            }
        }

        if (strpos($this->postData()['html_code'], '<script>') || strpos($this->postData()['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in pages.', 1, []);

            return false;
        }

        $file = time();

        $this->pagesDir = $this->modules->views->getPhalconViewPath() . 'system/pages/pages/test/';

        $testDir = str_replace(base_path(), '', $this->modules->views->getPhalconViewPath()) . 'system/pages/pages/test/';

        $this->localContent->write($testDir . $file . '.html', $this->postData()['html_code']);

        $result = $this->generatePageData(null, $file, $testData, true);

        if ($result) {
            $this->addResponse('Test Page Generated', 0, ['result' => $result], true);
        }

        $this->localContent->delete($testDir . $file . '.html');
    }

    public function generatePageData(
        $id = null,
        $file = null,
        $params = [],
        $viaComponent = false,
        $inclHeaderFooter = true,
        $css = '',
        $headerJs = '',
        $footerJs = ''
    ) {
        if (!$this->pagesDir) {
            $this->pagesDir = $this->modules->views->getPhalconViewPath() . 'system/pages/pages/';
        }

        $this->view->setViewsDir($this->pagesDir);

        if ($id && !$file) {
            $page = $this->pages->getById($id);

            if ($page) {
                $file = $page['file_name'];
            }
        }

        $rendered = '';

        if ($inclHeaderFooter) {
            $rendered .= $this->getHeader($css, $headerJs);
        }

        try {
            $rendered .= $this->view->getPartial($file, $params);
        } catch (\Exception $e) {
            //Clean the output buffer as we render partial content.
            //If we dont clear, the response will include partially rendered content before the JSON response.
            ob_clean();

            if ($e->getCode() === 8) {
                $message = 'Please add test data with key: ' . str_replace('Undefined variable: ', '', $e->getMessage());
            } else {
                $this->logger->logExceptions->critical(json_trace($e));

                $message = 'Error Contact Administrator';
            }

            if ($viaComponent) {
                $this->addResponse($message, 1);

                return false;
            }

            throw $e;
        }

        if ($inclHeaderFooter) {
            $rendered .= $this->getFooter($footerJs);
        }

        return $rendered;
    }

    protected function getHeader($css = '', $js = '')
    {
        if ($css && $css === '') {
            $css =
            '
                <link rel="stylesheet" type="text/css" href="' . $this->links->css('plugins.css') . '">
                <link rel="stylesheet" type="text/css" href="' . $this->links->css('core.css') . '">
            ';
        }

        if ($js && $js === '') {
            $js =
            '
            <script src="' . $this->links->js('/header/jsHeaderDependencies.js') . '"></script>
            <script src="' . $this->links->js('/header/jsHeaderCore.js') . '"></script>
            <script src="' . $this->links->js('/header/jsHeaderPlugins.js') . '"></script>
            ';
        }

        return
            $this->assets->get('doctype')->getCodes()[0]->getContent() .
            '
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="description" content="SP Framework">
                    <meta name="keywords" content="HTML, CSS, JavaScript">
                    <meta name="author" content="Guru">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <title>SP Framework</title>' .
                    $css .
                    $js .
                '</head>
                <body id="body" class="sidebar-mini layout-boxed sidebar-collapse">
                    <div class="wrapper">
                        <div class="row">
                            <div class="col">
                                <span class="brand-link">
                                    <img src="http://' . $this->domains->getDomainById(1)['name'] . '/core/default/images/baz/logo/justlogo33x30.png" alt="Bazaari Logo" class="brand-image">
                                </span>
                            </div>
                        </div>
            ';
    }

    protected function getFooter($js = '')
    {
        if ($js && $js === '') {
            $js = '';
        }

        return
        '
                <footer style="margin-left:0 !important" class="main-footer">
                    <strong>Copyright &copy; </strong> All rights reserved.' .
                    $js .
                '</footer>
                </div>
            </body>
        </html>
        ';
    }

    public function processJs($file)
    {
        if (!$this->checkJsPath()) {
            throw new \Exception('Unable to create var/pages/js folder.');
        }

        try {
            $this->engine = new Node('/usr/bin/nodejs', base_path('var/pages/js'));

            $this->renderer = (new Renderer($this->engine))->debug($this->config->debug);

            return $this->renderer->entry(base_path($file))->render();
        } catch (\Exception $e) {
            $this->logger->logExceptions->critical(json_trace($e));

            return false;
        }
    }

    protected function checkJsPath()
    {
        if (!is_dir(base_path('var/pages/js'))) {
            if (!mkdir(base_path('var/pages/js'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}