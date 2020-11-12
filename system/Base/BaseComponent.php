<?php

namespace System\Base;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;
use Applications\Admin\Packages\Filters\Filters;
use Phalcon\Assets\Collection;
use Phalcon\Assets\Inline;
use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Tag;

abstract class BaseComponent extends Controller
{
	protected $getQueryArr = [];

	protected $applicationName;

	protected $componentName;

	protected $component;

	protected $viewName;

	protected function onConstruct()
	{
		$this->setDefaultViewResponse();

		$this->application = $this->modules->applications->getApplicationInfo();

		$this->reflection = new \ReflectionClass($this);

		$this->componentName =
			str_replace('Component', '', $this->reflection->getShortName());

		$this->component =
			$this->modules->components->getNamedComponentForApplication(
				$this->componentName, $this->application['id']
			);

		if (!$this->isJson() || $this->request->isAjax()) {
			$this->checkLayout();
			$this->setDefaultViewData();
		}
	}

	protected function setDefaultViewData()
	{
		$this->view->widget = $this->widget;

		$this->view->applicationName = $this->application['name'];

		if (isset($this->application['route']) && $this->application['route'] !== '') {
			$this->view->route = strtolower($this->application['route']);
		} else {
			$this->view->route = strtolower($this->application['name']);
		}

		$this->view->componentName = strtolower($this->componentName);

		$this->view->componentId =
			strtolower($this->view->applicationName) . '-' . strtolower($this->componentName);

		$reflection = Arr::sliceRight(explode('\\', $this->reflection->getName()), 3);

		if (count($reflection) === 1) {
			$parents = str_replace('Component', '', Arr::last($reflection));
			$this->view->parents = $parents;
			$this->view->parent = strtolower($parents);
		} else {
			$reflection[Arr::lastKey($reflection)] =
				str_replace('Component', '', Arr::last($reflection));

			$parents = $reflection;

			$this->view->parents = $parents;
			$this->view->parent = strtolower(Arr::last($parents));
		}

		$this->view->viewName =
			$this->modules->views->getViewInfo()['name'];
	}

	protected function setDefaultViewResponse()
	{
		$this->view->responseCode = '0';

		$this->view->responseMessage = 'Default Response Message';
	}

	protected function sendJson()
	{
		$this->view->disable();

		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setHeader('Cache-Control', 'no-store');

		if ($this->response->isSent() !== true) {
			$this->response->setJsonContent($this->view->getParamsToView());

			return $this->response->send();
		}
	}

	protected function afterExecuteRoute()
	{
		if ($this->isJson()) {

			return $this->sendJson();
		} else {
			$this->view->setViewsDir($this->view->getViewsDir() . $this->getURI());
			// var_dump($this->view->getViewsDir());
		}
	}

	protected function getURI()
	{
		$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);

		$firstKey = Arr::firstKey($url);
		$lastKey = Arr::lastKey($url);

		unset($url[$firstKey]);
		unset($url[$lastKey]);

		return implode('/', $url);
	}

	protected function isJson()
	{
		if ($this->request->getBestAccept() === 'application/json') {
			return true;
		}
		return false;
	}

	protected function checkLayout()
	{
		if ($this->request->isAjax()) {

			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();

				if (Arr::has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '1') {
						$this->buildAssets();
						return;
					} else {
						$this->view->disableLevel(
										[
											View::LEVEL_LAYOUT 		=> true,
											View::LEVEL_MAIN_LAYOUT => true
										]
									);
						return;
					}
				} else {
					$this->view->disableLevel(
										[
											View::LEVEL_LAYOUT 		=> true,
											View::LEVEL_MAIN_LAYOUT => true
										]
									);
						return;
				}
			} else {
				$this->view->disableLevel(
								[
									View::LEVEL_LAYOUT 		=> true,
									View::LEVEL_MAIN_LAYOUT => true
								]
							);
				return;
			}
		} else if ($this->request->isGet()) {
			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();

				if (Arr::has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '0') {
						$this->view->disableLevel(
							[
								View::LEVEL_LAYOUT 		=> true,
								View::LEVEL_MAIN_LAYOUT => true
							]
						);
						return;
					} else {
						$this->buildAssets();
						return;
					}
				} else {
					$this->buildAssets();
					return;
				}
			} else {
				$this->buildAssets();
				return;
			}
		} else if ($this->request->isPost()) {

			if (Arr::has($this->request->getPost(), 'layout')) {
				if ($this->request->getPost('layout') === '0') {
					$this->view->disableLevel(
						[
							View::LEVEL_LAYOUT 		=> true,
							View::LEVEL_MAIN_LAYOUT => true
						]
					);
					return;
				} else {
					$this->buildAssets();
					return;
				}
			} else {
				$this->buildAssets();
				return;
			}
		} else {
			$this->view->disableLevel(
							[
								View::LEVEL_LAYOUT 		=> true,
								View::LEVEL_MAIN_LAYOUT => true
							]
						);
			return;
		}
	}

	protected function buildGetQueryParamsArr()
	{
		$arr = Arr::chunk($this->dispatcher->getParams(), 2);

		foreach ($arr as $value) {
			$this->getQueryArr[$value[0]] = $value[1];
		}

		// getQuery - /admin/setup/q/id/2/filter/4/search//layout/0
		// Will Result to
		// array (size=4)
		//   'id' => string '2' (length=1)
		//   'filter' => string '4' (length=1)
		//   'search' => string '' (length=0)
		//   'layout' => string '0' (length=1)
	}

	protected function getData()
	{
		return $this->getQueryArr;
	}

	protected function postData()
	{
		return $this->request->getPost();
	}

	protected function buildAssets()
	{
		if ($this->modules->views->getViewInfo()) {

			$settings = json_decode($this->modules->views->getViewInfo()['settings'], true);

			$this->tag::setDocType(Tag::XHTML5);

			if (isset($settings['head']['title'])) {
				Tag::setTitle($settings['head']['title']);

				if (isset($this->componentName)) {
					Tag::appendTitle(' - ' . $this->componentName);
				}
			} else {
				Tag::setTitle('Title Missing In Application Configuration');
			}

			//Meta
			$meta = $this->assets->collection('meta');

			if (isset($settings['head']['meta']['charset'])) {
				$charset = $settings['head']['meta']['charset'];
			} else {
				$charset = 'UTF-8';
			}

			$meta->addInline(new Inline('charset', $charset));

			$meta->addInline(
				new Inline('description', $settings['head']['meta']['description'])
			);
			$meta->addInline(
				new Inline('keywords', $settings['head']['meta']['keywords'])
			);
			$meta->addInline(
				new Inline('author', $settings['head']['meta']['author'])
			);
			$meta->addInline(
				new Inline('viewport', $settings['head']['meta']['viewport'])
			);

			//Head - Css
			$headLinks = $this->assets->collection('headLinks');
			$links = $settings['head']['link']['href'];
			if (count($links) > 0) {
				foreach ($links as $link) {
					$headLinks->addCss($link);
				}
			}

			//Head - Style
			$headStyle = $this->assets->collection('headStyle');
			$inlineStyle = $settings['head']['style'] ?? null;
			if ($inlineStyle) {
				$this->assets->addInlineCss($inlineStyle);
			}

			//Head - Js
			$headJs = $this->assets->collection('headJs');
			$scripts = $settings['head']['script']['src'];
			if (count($scripts) > 0) {
				foreach ($scripts as $script) {
					$headJs->addJs($script);
				}
			}

			//Body
			$body = $this->assets->collection('body');
			$body->addInline(new Inline('bodyParams', $settings['body']['params']));

			//Body - Js Scripts right after Body tag
			$body->addInline(new Inline('bodyScript', $settings['body']['jsscript']));

			//Footer - <footer tag parameters>
			$footer = $this->assets->collection('footer');
			$footer->addInline(new Inline('footerParams', $settings['footer']['params']));

			//Footer - Js Scripts
			$footerJs = $this->assets->collection('footerJs');
			$scripts = $settings['footer']['script']['src'];
			if (count($scripts) > 0) {
				foreach ($scripts as $script) {
					$footerJs->addJs($script);
				}
			}

			// Footer inline scripts
			$inlineScript = $settings['footer']['jsscript'] ?? null;
			if ($inlineScript && $inlineScript !== '') {
				$this->assets->addInlineJs($inlineScript);
			}

			$this->view->menus =
				$this->modules->menus->getMenusForApplication($this->application['id']);
		}
	}

	protected function usePackage($packageClass)
	{
		if ($this->checkPackage($packageClass)) {
			return (new $packageClass())->init();
		} else {
			throw new \Exception(
				'Package class : ' . $packageClass .
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getNamedPackageForApplication(
				Arr::last(explode('\\', $packageClass)),
				$this->application['id']
			);
	}

	protected function useComponent($componentClass)
	{
		$this->application = $this->modules->applications->getApplicationInfo();

		if ($this->checkComponent($componentClass)) {
			return new $componentClass();
		} else {
			throw new \Exception(
				'Component class : ' . $componentClass .
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkComponent($componentClass)
	{
		return
			$this->modules->components->getNamedComponentForApplication(
				str_replace('Component', '', Arr::last(explode('\\', $componentClass))),
				$this->application['id']
			);
	}

	protected function useComponentWithView($componentClass, $action = 'view')
	{
		//To Use from Component - $this->useComponentWithView(HomeComponent::class);
		//This will generate 2 view variables 1) {{home}} & {{homeTemplate}}
		$this->application = $this->modules->applications->getApplicationInfo();

		$component = $this->checkComponent($componentClass);

		if ($component) {
			$componentName = strtolower($component['name']);
			$componentAction = $action . 'Action';
			$componentViewName = strtolower($component['name']) . 'Template';

			var_dump($componentName,$componentAction,$componentViewName);

			$this->view->{$componentName} =
				$this->useComponent($componentClass)->{$componentAction}();

			$this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

			$this->view->{$componentViewName} =
				$this->view->render($componentName, $action)->getContent();

			$this->view->setRenderLevel(View::LEVEL_MAIN_LAYOUT);
		}
	}

	//Datatable Content
	protected function generateDTContent(
		$package,
		string $postUrl,
		int $componentId = null,
		array $columnsForTable = [],
		$withFilter = true,
		array $columnsForFilter = [],
		array $controlActions = null
	)
	{
		if (gettype($package) === 'string') {
			$package = $this->usePackage($package);
		}

		if ($this->request->isGet()) {
			$table = [];
			$table['columns'] = $package->getModelsColumnMap($columnsForTable);
			$table['filterColumns'] = $package->getModelsColumnMap($columnsForFilter);
			$table['postUrl'] = $this->links->url($postUrl);
			$table['component'] = $this->component;

			if (!$componentId) {
				$componentId = $this->component['id'];
			}

			$filtersArr = $this->usePackage(Filters::class)->getFiltersForComponent($componentId);

			$table['postUrlParams'] = [];
			foreach ($filtersArr as $key => $filter) {
				$table['filters'][$filter['id']] = $filter;
				$table['filters'][$filter['id']]['data']['name'] = $filter['name'];
				$table['filters'][$filter['id']]['data']['id'] = $filter['id'];
				$table['filters'][$filter['id']]['data']['component_id'] = $filter['component_id'];
				$table['filters'][$filter['id']]['data']['conditions'] = $filter['conditions'];
				$table['filters'][$filter['id']]['data']['permission'] = $filter['permission'];
				$table['filters'][$filter['id']]['data']['is_default'] = $filter['is_default'];
				$table['filters'][$filter['id']]['data']['shared_ids'] = $filter['shared_ids'];

				if ($filter['is_default'] === '1') {
					$table['postUrlParams'] = ['conditions' => $filter['conditions']];
				}
			}

			$this->view->table = $table;

		} else if ($this->request->isPost()) {

			$pagedData =
				$package->getPaged(
					[
						'columns' => $columnsForTable
					]
				);

			$rows = $pagedData->getItems();

			if ($controlActions) {
				// add control action to each row
				foreach($rows as &$row) {
					$actions = [];

					foreach ($controlActions as $key => &$action) {
						$actions[$key] = $this->links->url($action . '/q/id/' . $row['id']);
					}

					$row["__control"] = $actions;
				}
			}

			$adminltetags = new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

			$this->view->rows =
				$adminltetags->useTag('content/listing/table',
					[
						'componentId'                   => $this->view->componentId,
						'dtRows'                        => $rows,
						'dtNotificationTextFromColumn'  => 'email',
						'dtPagination'                  => true,
						'dtPaginationCounters'          => $package->packagesData->paginationCounters
					]
				);
		}
	}
}