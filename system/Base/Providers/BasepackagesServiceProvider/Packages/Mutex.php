<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\UnableToReadFile;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\Url;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMutex;

class Mutex extends BasePackage
{
    protected $modelToUse = BasepackagesMutex::class;

    protected $packageName = 'mutex';

    public $mutex;

    protected $timeout = 300;//5 Min

    protected static $parentLock = null;

    public function init()
    {
        return $this;
    }

    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getMutex($packageName, $packageRowId)
    {
        if (!$this->access->auth->check()) {
            return false;
        }

        if ($mutex = $this->checkMutex($packageName, $packageRowId)) {
            //If a parent has already locked and we try to access the child on its own (as parent)
            //We should reset the parent lock
            if ($mutex['parent_lock_id'] !== 0 && !static::$parentLock) {
                static::$parentLock = $mutex['id'];

                $parent = $this->getById((int) $mutex['parent_lock_id']);

                if ($parent) {
                    $mutex['parent_lock_by_id'] = $parent['parent_lock_id'];
                    $mutex['parent_lock_by_package'] = $parent['package_name'];
                    $mutex['parent_lock_id'] = 0;
                }
            } else if (!static::$parentLock) {
                static::$parentLock = $mutex['id'];
            } else if ($mutex['parent_lock_id'] === 0 && static::$parentLock) {
                //This situation will be when you open a child first and then open a parent.
                //So the child has no parent as it is the parent.
                //The parent in that case should be locked as the child is being modified.
                $parent = $this->getById((int) static::$parentLock);

                $parent['parent_lock_by_id'] = $mutex['id'];
                $parent['parent_lock_by_package'] = $mutex['package_name'];

                if ($parent['account_id'] === $mutex['account_id']) {
                    $parent['self'] = true;
                    $parent['account_name'] = $mutex['account_name'];
                } else {
                    $account = $this->basepackages->accounts->getAccountById($mutex['account_id']);

                    if ($account && isset($account['contact']['full_name'])) {
                        $parent['self'] = false;
                        $parent['account_name'] = $account['contact']['full_name'];
                    }
                }

                return $parent;
            }

            return $mutex;
        } else {
            $newMutex = [];
            $newMutex['package_name'] = $packageName;
            $newMutex['package_row_id'] = $packageRowId;
            $newMutex['account_id'] = $this->access->auth->account()['id'];
            if (static::$parentLock) {
                $newMutex['parent_lock_id'] = static::$parentLock;
            } else {
                $newMutex['parent_lock_id'] = 0;
            }
            $newMutex['locked_at'] = time();

            if ($this->add($newMutex)) {
                $mutex = $this->packagesData->last;

                $account = $this->basepackages->accounts->getAccountById($mutex['account_id']);

                if ($account && isset($account['contact']['full_name'])) {
                    $mutex['account_name'] = $account['contact']['full_name'];
                }

                $mutex['self'] = true;

                if (!static::$parentLock) {
                    static::$parentLock = $mutex['id'];
                }

                return $mutex;
            }
        }

        return false;
    }

    public function releaseMutex($data)
    {
        if (!$this->access->auth->check()) {
            return false;
        }

        if (!isset($data['mutexLock']['id']) && isset($data['id'])) {
            $data['mutexLock'] = $data;
        }

        if ($mutex = $this->getById((int) $data['mutexLock']['id'])) {
            if ($this->access->auth->account()['id'] === 1 ||
                ($mutex['account_id'] === $this->access->auth->account()['id'])
            ) {
                return $this->removeParentAndChilds($data, $mutex);
            } else {
                if (isset($data['forceRelease']) && $data['forceRelease'] == 'true') {
                    return $this->removeParentAndChilds($data, $mutex);
                }

                return true;
            }
        }

        return false;
    }

    protected function removeParentAndChilds($data, $mutex)
    {
        if ($mutex['parent_lock_id'] === 0) {
            $childs = $this->getChilds($data['mutexLock']['id']);

            if ($childs && count($childs) > 0) {
                foreach ($childs as $child) {
                    $this->remove($child['id']);
                }
            }

            if ($this->remove($data['mutexLock']['id'])) {
                return true;
            }
        }
    }

    public function checkMutex($packageName, $packageRowId)
    {
        if (!$this->access->auth->check()) {
            return false;
        }

        if ($this->config->databasetype === 'db') {
            $params =
                [
                    'conditions'    => 'package_name = :packageName: AND package_row_id = :packageRowId:',
                    'bind'          =>
                        [
                            'packageName'           => $packageName,
                            'packageRowId'          => $packageRowId
                        ]
                ];
        } else {
            $params = [
                'conditions' => [
                    ['package_name', '=', $packageName],
                    ['package_row_id', '=', $packageRowId]
                ]
            ];
        }

        $mutex = $this->getByParams($params);

        if ($mutex && count($mutex) > 0 && isset($mutex[0]['id'])) {
            //Remove Mutex that has timed out.
            foreach ($mutex as $mutexKey => $mutexValue) {
                if (isset($mutexValue['locked_at']) && $mutexValue['locked_at'] > 0) {
                    $currentTime = time();

                    $lockedAtTime = $mutexValue['locked_at'] + $this->getTimeout();

                    if ($currentTime > $lockedAtTime) {
                        $childs = $this->getChilds($mutexValue['id']);

                        if ($childs && count($childs) > 0) {
                            foreach ($childs as $child) {
                                $this->remove($child['id']);
                            }
                        }

                        if ($this->remove($mutexValue['id'])) {
                            unset($mutex[$mutexKey]);
                        }
                    }
                }
            }

            if (isset($mutex[0]['account_id'])) {
                $account = $this->basepackages->accounts->getAccountById($mutex[0]['account_id']);

                if ($account && isset($account['contact']['full_name'])) {
                    $mutex[0]['account_name'] = $account['contact']['full_name'];
                    $mutex[0]['self'] = false;

                    if ($mutex[0]['account_id'] === $this->access->auth->account()['id']) {
                        $mutex[0]['self'] = true;
                    } else {
                        $mutex[0]['can_remove_lock'] = false;

                        $mutexComponent = $this->modules->components->getComponentByRoute('system/mutex');

                        if ($mutexComponent) {
                            if ($this->access->auth->account()['role']['id'] == '1' ||
                                (isset($this->access->auth->account()['role']['permissions']['1'][$mutexComponent['id']]['remove']) &&
                                $this->access->auth->account()['role']['permissions']['1'][$mutexComponent['id']]['remove'] == '1')

                            ) {
                                $mutex[0]['can_remove_lock'] = true;
                            }
                        }
                    }
                } else {//User not found, we remove lock
                    $this->releaseMutex($mutex[0]['id']);

                    return false;
                }

                return $mutex[0];
            }
        }

        return false;
    }

    protected function getChilds($parentId)
    {
        if ($this->config->databasetype === 'db') {
            $params =
                [
                    'conditions'    => 'parent_lock_id = :parentId:',
                    'bind'          =>
                        [
                            'parent_lock_id'          => (int) $parentId
                        ]
                ];
        } else {
            $params = [
                'conditions' => [
                    ['parent_lock_id', '=', (int) $parentId]
                ]
            ];
        }

        return $this->getByParams($params);
    }

    protected function dbLock()
    {
        //We can extend this package to actually lock the Ids at DB level using
        //https://github.com/php-lock/lock
        //composer require malkusch/lock
    }
}