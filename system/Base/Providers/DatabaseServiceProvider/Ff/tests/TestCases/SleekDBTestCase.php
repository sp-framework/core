<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\tests\TestCases;

use System\Base\Providers\DatabaseServiceProvider\Ff\Store;
use System\Base\Providers\DatabaseServiceProvider\Ff\tests\TestCases\SleekDBTestCasePlain;

class SleekDBTestCase extends SleekDBTestCasePlain
{

  /**
   * @var Store[]
   */
  protected $stores = [];

  /**
   * @before
   */
  public function createStore(){
    foreach (self::DATABASE_DATA as $storeName => $databaseData){
      $this->stores[$storeName] = new Store($storeName, self::DATA_DIR);
    }
  }


  /**
   * @after
   */
  public function deleteStore(){
    foreach ($this->stores as $store){
      $store->deleteStore();
    }
  }

//  /**
//   * @after
//   */
//  public function clearStores(){
//    foreach (self::$stores as $store){
//      $store->delete();
//      $storeData = $store->fetch();
//      $this->assertEmpty($storeData);
//    }
//  }
}