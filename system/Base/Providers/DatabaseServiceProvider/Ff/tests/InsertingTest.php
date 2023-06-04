<?php declare(strict_types=1);

namespace System\Base\Providers\DatabaseServiceProvider\Ff\tests;

use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IdNotAllowedException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\tests\TestCases\SleekDBTestCase;

final class InsertingTest extends SleekDBTestCase
{

  public function testCanInsertSingleData(){
    $userStore = $this->stores["users"];

    $usersData = self::DATABASE_DATA['users'][0];
    $userStore->insert($usersData);

    $users = $userStore->findAll();
    self::assertCount(1, $users);
  }

  public function testCanInsertMultipleData(){
    $userStore = $this->stores["users"];

    $usersData = self::DATABASE_DATA['users'];
    $users = $userStore->insertMany($usersData);
    $usersFetched = $userStore->findAll();
    self::assertSameSize($usersData, $users);
    self::assertSameSize($usersFetched, $users);
  }

  public function testCannotInsertSingleEmptyData(){

    $userStore = $this->stores["users"];
    $this->expectException(InvalidArgumentException::class);
    $usersData = [];
    $userStore->insert($usersData);

  }

  public function testCannotInsertSingleStringData(){

    $userStore = $this->stores["users"];

    $this->expectException(\TypeError::class);
    $usersData = "This is a String";
    $userStore->insert($usersData);
  }

  public function testCannotInsertMultipleEmptyData(){

    $userStore = $this->stores["users"];
    $this->expectException(InvalidArgumentException::class);
    $usersData = [];
    $userStore->insertMany($usersData);

  }

  public function testCannotInsertMultipleStringData(){

    $userStore = $this->stores["users"];

    $this->expectException(\TypeError::class);
    $usersData = "This is a String";
    $userStore->insertMany($usersData);
  }

  public function testCannotInsertDocumentWithPrimaryKey(){

    $userStore = $this->stores["users"];

    $this->expectException(IdNotAllowedException::class);
    $userStore->insert(["id" => 3, "test" => "test"]);
  }


}