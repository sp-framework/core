<?php declare(strict_types=1);

namespace System\Base\Providers\DatabaseServiceProvider\Ff\tests;

use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidConfigurationException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\JsonException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;
use System\Base\Providers\DatabaseServiceProvider\Ff\tests\TestCases\SleekDBTestCasePlain;


final class StoreTest extends SleekDBTestCasePlain
{

  /**
   * @after
   */
  public function deleteDefaultStore(){
    $store = new Store(self::STORE_NAME, self::DATA_DIR);
    $store->deleteStore();
  }

  public function testCanCreateAndDeleteStore(){
    $store = new Store(self::STORE_NAME, self::DATA_DIR);
    self::assertInstanceOf(Store::class, $store);

    $storePath = self::DATA_DIR."/".self::STORE_NAME;

    self::assertDirectoryExists($storePath);
    self::assertDirectoryIsWritable($storePath);
    self::assertDirectoryIsReadable($storePath);

    $cachePath = $storePath."/cache";
    self::assertDirectoryExists($cachePath);
    self::assertDirectoryIsWritable($cachePath);
    self::assertDirectoryIsReadable($cachePath);

    $dataPath = $storePath."/data";
    self::assertDirectoryExists($dataPath);
    self::assertDirectoryIsWritable($cachePath);
    self::assertDirectoryIsReadable($cachePath);

    $counterFile = $storePath."/_cnt.sdb";
    self::assertFileExists($counterFile);
    self::assertFileIsWritable($counterFile);
    self::assertFileIsReadable($counterFile);

    $store->deleteStore();

    self::assertDirectoryDoesNotExist($storePath);
  }

  public function testCannotCreateStoreWithEmptyStoreName(){
    $this->expectException(InvalidArgumentException::class);
    new Store("", self::DATA_DIR);
  }

  public function testCannotCreateStoreWithEmptyDataDirectory(){
    $this->expectException(InvalidArgumentException::class);
    new Store(self::STORE_NAME, "");
  }

  public function testCannotCreateStoreWithOptionsNull(){
    $this->expectException(\TypeError::class);
    new Store(self::STORE_NAME, self::DATA_DIR, null);
  }

  public function testCannotCreateStoreWithOptionsString(){
    $this->expectException(\TypeError::class);
    new Store(self::STORE_NAME, self::DATA_DIR, "");
  }

  public function testCannotCreateStoreWithOptionsObject(){
    $this->expectException(\TypeError::class);
    new Store(self::STORE_NAME, self::DATA_DIR, (new \stdClass()));
  }

  public function testCannotCreateStoreWithOptionsInteger(){
    $this->expectException(\TypeError::class);
    new Store(self::STORE_NAME, self::DATA_DIR, 1);
  }

  public function testCanGetLastInsertedId(){
    $store = new Store(self::STORE_NAME, self::DATA_DIR);

    $testDocument = $store->insert(["test" => "test"]);
    $lastId = $store->getLastInsertedId();

    self::assertSame($testDocument["id"], $lastId);
  }

  public function testCanNotGetNextIdIfFileIsDeleted(){
    $counterFile = self::DATA_DIR."/".self::STORE_NAME."/_cnt.sdb";
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR);
    unlink($counterFile);
    $this->expectException(IOException::class);
    $testStore->insert(["test" => "test"]);
  }

  public function testCanApplyConfigurations(){
    $configuration = [
      "auto_cache" => true,
      "cache_lifetime" => 20,
      "primary_key" => "id"
    ];
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR, $configuration);
    self::assertSame($configuration["auto_cache"], $testStore->getUseCache());
    self::assertSame($configuration["cache_lifetime"], $testStore->getDefaultCacheLifetime());
    self::assertSame($configuration["primary_key"], $testStore->getPrimaryKey());
  }

  public function testCanNotApplyAutoCacheConfigNotBoolean(){
    $configuration = [
      "auto_cache" => 1,
      "cache_lifetime" => null,
      "timeout" => 120,
      "primary_key" => "id"
    ];
    $this->expectException(InvalidConfigurationException::class);
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR, $configuration);
  }

  public function testCanNotApplyCacheLifetimeConfigNotNullAndNotInt(){
    $configuration = [
      "auto_cache" => true,
      "cache_lifetime" => "test",
      "timeout" => 120,
      "primary_key" => "id"
    ];
    $this->expectException(InvalidConfigurationException::class);
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR, $configuration);
  }

  public function testCanNotApplyPrimaryKeyConfigNotString(){
    $configuration = [
      "auto_cache" => true,
      "cache_lifetime" => null,
      "timeout" => 125,
      "primary_key" => 3
    ];
    $this->expectException(InvalidConfigurationException::class);
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR, $configuration);
  }

  public function testCanNotInsertInfiniteReferenceArray(){
    $this->expectException(JsonException::class);
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR);
    $a = array(&$a);
    $testStore->insert($a);
  }

  public function testCanChangeStore(){
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR);
    $oldStorePath = $testStore->getStorePath();
    $oldDataPath = $testStore->getDatabasePath();
    $testStore->changeStore(self::STORE_NAME."2", self::DATA_DIR);
    self::assertNotSame($oldStorePath, $testStore->getStorePath());
    self::assertSame($oldDataPath, $testStore->getDatabasePath());
  }

  public function testCanChangeStoreWithEmptyDataDir(){
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR);
    $oldStorePath = $testStore->getStorePath();
    $oldDataPath = $testStore->getDatabasePath();
    $testStore->changeStore(self::STORE_NAME."2");
    self::assertNotSame($oldStorePath, $testStore->getStorePath());
    self::assertSame($oldDataPath, $testStore->getDatabasePath());
    $testStore->deleteStore();
  }

  public function testCanFindById(){
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR);
    $resultOfInsert = $testStore->insert(["test" => "test"]);
    $result = $testStore->findById(1);
    self::assertSame($resultOfInsert, $result);
  }

  public function testCanNotFindByIdWithNotExistingId(){
    $testStore = new Store(self::STORE_NAME, self::DATA_DIR);
    $result = $testStore->findById(1);
    self::assertSame(null, $result);
  }
}