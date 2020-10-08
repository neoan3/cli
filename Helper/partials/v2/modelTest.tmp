<?php

namespace Neoan3\Model;

use Neoan3\Provider\MySql\Database;
use Neoan3\Provider\MySql\MockDatabaseWrapper;
use PHPUnit\Framework\TestCase;

/**
 * Class {{name.pascal}}Test
 * @package Neoan3\Model
 */
class {{name.pascal}}Test extends TestCase
{
    /**
     * @var Database|MockDatabaseWrapper
     */
    private Database $mockDb;


    function setUp(): void
    {
        $this->mockDb = new MockDatabaseWrapper(['name'=>'test']);
    }


    /**
     * Test retrieval
     */
    public function testGet()
    {
        $model = $this->mockDb->mockGet('{{name.lower}}');
        {{name.pascal}}Model::init($this->mockDb);
        $res = {{name.pascal}}Model::get($model['id']);
        $this->assertIsArray($res);
        $this->assertSame($model, $res);
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $model = $this->mockDb->mockModel('{{name.lower}}');
        $model[array_keys($model)[0]] = 'abc';
        $this->mockDb->mockUpdate('{{name.lower}}',$model);
        {{name.pascal}}Model::init($this->mockDb);
        $result = {{name.pascal}}Model::update($model);
        $this->assertSame($result[array_keys($model)[0]], 'abc');
    }

    /**
     * Test creation
     */
    public function testCreate()
    {
        $model = $this->mockDb->mockModel('{{name.lower}}');
        $this->mockDb->registerResult([['id' => '123456789']]);
        $inserted = $this->mockDb->mockUpdate('{{name.lower}}', $model);
        {{name.pascal}}Model::init($this->mockDb);
        $created = {{name.pascal}}Model::create($model);
        $this->assertSame($inserted, $created);
    }

    /**
     * Test find
     */
    public function testFind()
    {
        $this->mockDb->registerResult([['id' => 'any']]);
        $model = $this->mockDb->mockModel('{{name.lower}}');
        $this->mockDb->mockGet('{{name.lower}}', $model);
        {{name.pascal}}Model::init($this->mockDb);
        $found = {{name.pascal}}Model::find(['id'=>'any']);
        $this->assertSame($model, $found[0]);
    }


}
