<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Views as ViewsModel;

class Views extends BasePackage
{
    protected $modelToUse = ViewsModel::class;

    public $views;

    protected $view;

    protected $applications;

    protected $applicationInfo;

    protected $voltCompiledPath;

    protected $phalconViewPath;

    protected $phalconViewLayoutPath;

    protected $phalconViewLayoutFile;

    protected $cache;

    // public function getAll($params = [], bool $resetCache = false)
    // {
    //     if ($this->cacheKey) {
    //         $parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
    //     }

    //     if (!$this->views || $resetCache) {

    //         $this->model = ViewsModel::find($parameters);

    //         $this->views = $this->model->toArray();
    //     }

    //     return $this;
    // }

    // public function get(int $id, bool $resetCache = false)
    // {
    //     $parameters = $this->paramsWithCache($this->getIdParams($id));

    //     $this->model = ViewsModel::find($parameters);

    //     if ($this->model->count() === 1) {
    //         $this->packagesData->responseCode = 0;
    //         $this->packagesData->responseMessage = 'Found';

    //         array_push($this->cacheKeys, $parameters['cache']['key']);

    //         return $this->model->toArray()[0];

    //     } else if ($this->model->count() > 1) {
    //         $this->packagesData->responseCode = 1;
    //         $this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';

    //     } else if ($this->model->count() === 0) {
    //         $this->packagesData->responseCode = 1;
    //         $this->packagesData->responseMessage = 'No Record Found with that ID!';
    //     }

    //     $this->cacheTools->deleteCache($parameters['cache']['key']); //We delete cache on error.

    //     return false;
    // }

    // public function add(array $data)
    // {
    //     try {
    //         $txManager = new Manager();
    //         $transaction = $txManager->get();

    //         $view = new ViewsModel();

    //         $view->setTransaction($transaction);

    //         $view->assign($data);

    //         $create = $view->create();

    //         if (!$create) {
    //             $transaction->rollback('Could not add view.');
    //         }

    //         if ($transaction->commit()) {
    //             $this->resetCache();

    //             $this->packagesData->responseCode = 0;

    //             $this->packagesData->responseMessage = 'Added view!';

    //             return true;
    //         }
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }
    // }

    // public function update(array $data)
    // {
    //     try {
    //         $txManager = new Manager();
    //         $transaction = $txManager->get();

    //         $view = new ViewsModel();

    //         $view->setTransaction($transaction);

    //         $view->assign($data);

    //         if (!$view->update()) {
    //             $transaction->rollback('Could not update view.');
    //         }

    //         if ($transaction->commit()) {
    //             //Delete Old cache if exists and generate new cache
    //             $this->updateCache($data['id']);

    //             $this->packagesData->responseCode = 0;

    //             $this->packagesData->responseMessage = 'View Updated!';

    //             return true;
    //         }
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }
    // }

    // public function remove(int $id)
    // {
    //     //Need to solve dependencies for removal
    //     // $this->get($id);

    //     // if ($this->model->count() === 1) {
    //     //  if ($this->model->delete()) {

    //     //      $this->resetCache($id);

    //     //      $this->packagesData->responseCode = 0;
    //     //      $this->packagesData->responseMessage = 'View Deleted!';
    //     //      return true;
    //     //  } else {
    //     //      $this->packagesData->responseCode = 1;
    //     //      $this->packagesData->responseMessage = 'Could not delete application.';
    //     //  }
    //     // } else if ($this->model->count() > 1) {
    //     //  $this->packagesData->responseCode = 1;
    //     //  $this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';
    //     // } else if ($this->model->count() === 0) {
    //     //  $this->packagesData->responseCode = 1;
    //     //  $this->packagesData->responseMessage = 'No Record Found with that ID!';
    //     // }
    // }
    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        $this->applications = $this->modules->applications;

        $this->setApplicationInfo();

        $this->setVoltCompiledPath();

        $this->setPhalconViewPath();

        $this->setPhalconViewLayoutPath();

        $this->setPhalconViewLayoutFile();

        return $this;
    }

    protected function setVoltCompiledPath()
    {
        if (!isset($this->voltCompiledPath)) {
            $this->voltCompiledPath =
                base_path('applications/' . $this->applicationInfo['name'] .
                          '/Views/' . $this->view['name'] .
                          '/html_compiled/');

            if (!is_dir($this->voltCompiledPath)) {
                mkdir(
                    $this->voltCompiledPath,
                    0777,
                    true
                );
            }
        }
    }

    protected function setPhalconViewPath()
    {
        if (!isset($this->phalconViewPath)) {
            $this->phalconViewPath =
                base_path('applications/' . $this->applicationInfo['name'] .
                          '/Views/' . $this->view['name'] .
                          '/html/');
        }
    }

    protected function setPhalconViewLayoutPath()
    {
        if (!isset($this->phalconViewLayoutPath)) {
            $this->phalconViewLayoutPath =
                base_path('applications/' . $this->applicationInfo['name'] .
                          '/Views/' . $this->view['name'] .
                          '/html/layouts/');
        }
    }

    protected function setPhalconViewLayoutFile()
    {
        if (!isset($this->phalconViewLayoutFile)) {
            $this->phalconViewLayoutFile =
                json_decode($this->view['settings'], true)['layout'];
        }
    }

    public function getVoltCompiledPath()
    {
        return $this->voltCompiledPath;
    }

    public function getPhalconViewPath()
    {
        return $this->phalconViewPath;
    }

    public function getPhalconViewLayoutPath()
    {
        return $this->phalconViewLayoutPath;
    }

    public function getPhalconViewLayoutFile()
    {
        return $this->phalconViewLayoutFile;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getViewInfo()
    {
        return $this->view;
    }

    protected function setApplicationInfo()
    {
        if (!$this->applicationInfo) {
            $this->applicationInfo = $this->applications->getApplicationInfo();

            if ($this->applicationInfo) {

                $applicationDefaults = $this->applications->getApplicationDefaults($this->applicationInfo['name']);
            } else {
                $applicationDefaults = null;
            }
            if ($this->applicationInfo && $applicationDefaults) {

                $applicationName = $applicationDefaults['application'];

                $viewsName = $applicationDefaults['view'];

                if (!$this->view) {
                    $this->view = $this->getApplicationView($this->applicationInfo['id']);
                }

                $this->cache = json_decode($this->view['settings'], true)['cache'];
            }
        }
    }

    protected function getApplicationView($id)
    {
        $filter =
            $this->model->filter(
                function($view) use ($id) {
                    if ($view->application_id === $id) {
                        return $view;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate default application for application ' . $name);
        } else if (count($filter) > 0) {
            return $filter[0]->toArray();
        } else {
            return false;
        }
    }
}