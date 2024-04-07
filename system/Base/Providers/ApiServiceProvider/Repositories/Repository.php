<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Interfaces\RepositoryInterface;
use System\Base\Providers\ApiServiceProvider\Models\BaseModel;

abstract class Repository extends BasePackage implements RepositoryInterface
{
    private $config;

    private $request;

    protected static $perPage = 15;

    protected $model;

    // public function __construct()
    // {
    //     $class = $this->modelName();

    //     $this->model = new $class();
    // }

    // public function getConfig()
    // {
    //     return $this->config;
    // }

    // abstract public function modelName();

    // public function getQueryBuilder()
    // {
    //     return Di::getDefault()->get('modelsManager')->createBuilder()->addFrom($this->modelName());
    // }

    // public function create(array $data)
    // {
    //     return $this->model->add($data);
    // }

    // public function update(array $condition, array $data)
    // {
    //     /** @var BaseModel $model */
    //     if ($model = $this->findOne($condition)) {
    //         return $model->edit($data);
    //     }

    //     return false;
    // }

    // public function delete($id)
    // {
    //     $field = BaseModel::getPrimaryKeyField($this->model);
    //     if (empty($field)) {
    //         return false;
    //     }

    //     $model = $this->findOne([$field => $id]);

    //     return $model ? $model->delete() : false;
    // }

    // public function findAll(array $conditions, $limit = null)
    // {
    //     $wildCards = $binds = [];

    //     $count = 0;
    //     foreach ($conditions as $key => $value) {
    //         $wildCards[] = "$key=:" . $count . ":";
    //         $binds[$count] = $value;
    //         $count++;
    //     }

    //     $wildCards = implode(" AND ", $wildCards);
    //     $builder = $this->getQueryBuilder();
    //     $builder->where($wildCards, $binds);
    //     if (!empty($limit)) {
    //         $builder->limit($limit);
    //     }

    //     return $builder->getQuery()->execute();
    // }

    // public function findOne(array $conditions)
    // {
    //     $results = $this->findAll($conditions, 1);
    //     return $results->count() === 0 ? null : $results[0];
    // }

    // public function all()
    // {
    //     $builder = $this->getQueryBuilder();
    //     return $builder->getQuery()->execute();
    // }

    // public function getModel()
    // {
    //     return $this->model;
    // }
}