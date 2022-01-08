<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use Spatie\Ssr\Engines\Node;
use Spatie\Ssr\Renderer;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesTemplates;

class Templates extends BasePackage
{
    protected $modelToUse = BasepackagesTemplates::class;

    protected $packageName = 'templates';

    public $templates;

    protected $templatesDir;

    protected $engine;

    protected $renderer;

    public function addTemplate(array $data)
    {
        if (strpos($data['html_code'], '<script>') || strpos($data['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in templates.', 1, []);

            return false;
        }

        $data['in_use'] = 0;

        $data['file_name'] = time() . '.html';

        if ($this->add($data)) {
            $this->addResponse('Added new template');
        } else {
            $this->addResponse('Error adding new template', 1);
        }
    }

    public function updateTemplate(array $data)
    {
        if (strpos($data['html_code'], '<script>') || strpos($data['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in templates.', 1, []);

            return false;
        }

        if ($this->update($data)) {
            $this->addResponse('Updated new template');
        } else {
            $this->addResponse('Error updating template', 1);
        }
    }

    public function removeTemplate(array $data)
    {
        //
    }

    public function testTemplate($data)
    {
        $testData = [];

        if ($this->postData()['test_data'] !== '') {
            try {
                $testData = Json::decode($this->postData()['test_data'], true);
            } catch (\Exception $e) {
                $this->addResponse('Test data format needs to be JSON.', 1, []);

                return false;
            }
        }

        if (strpos($this->postData()['html_code'], '<script>') || strpos($this->postData()['html_code'], '</script>')) {
            $this->addResponse('JavaScript is not supported in templates.', 1, []);

            return false;
        }

        $file = time();

        $this->templatesDir = $this->modules->views->getPhalconViewPath() . 'system/templates/templates/test/';

        $testDir = str_replace(base_path(), '', $this->modules->views->getPhalconViewPath()) . 'system/templates/templates/test/';

        $this->localContent->write($testDir . $file . '.html', $this->postData()['html_code']);

        $result = $this->generateTemplateData(null, $file, $testData, true);

        if ($result) {
            $this->addResponse('Test Template Generated', 0, ['result' => $result], true);
        }

        $this->localContent->delete($testDir . $file . '.html');
    }

    public function generateTemplateData(
        $id = null,
        $file = null,
        $params = [],
        $viaComponent = false,
        $inclHeaderFooter = true,
        $css = '',
        $headerJs = '',
        $footerJs = ''
    ) {
        if (!$this->templatesDir) {
            $this->templatesDir = $this->modules->views->getPhalconViewPath() . 'system/templates/templates/';
        }

        $this->view->setViewsDir($this->templatesDir);

        if ($id && !$file) {
            $template = $this->templates->getById($id);

            if ($template) {
                $file = $template['file_name'];
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
                $this->logger->logExceptions->debug($e);

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
            $this->tag->getDocType() .
            '
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="description" content="Bazaari">
                    <meta name="keywords" content="HTML, CSS, JavaScript">
                    <meta name="author" content="Guru">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <title>Bazaari</title>' .
                    $css .
                    $js .
                '</head>
                <body id="body" class="sidebar-mini layout-boxed sidebar-collapse">
                    <div class="wrapper">
                        <div class="row">
                            <div class="col">
                                <span class="brand-link">
                                    <img src="http://' . $this->domains->getIdDomain(1)['name'] . '/dash/default/images/baz/logo/justlogo33x30.png" alt="Bazaari Logo" class="brand-image">
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
            throw new \Exception('Unable to create var/templates/js folder.');
        }

        try {
            $this->engine = new Node('/usr/bin/nodejs', base_path('var/templates/js'));

            $this->renderer = (new Renderer($this->engine))->debug($this->config->debug);

            return $this->renderer->entry(base_path($file))->render();
        } catch (\Exception $e) {
            $this->logger->logExceptions->debug($e);

            return false;
        }
    }

    protected function checkJsPath()
    {
        if (!is_dir(base_path('var/templates/js'))) {
            if (!mkdir(base_path('var/templates/js'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}