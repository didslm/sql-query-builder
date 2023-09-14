<?php

namespace Didslm\QueryBuilder\Tests\Builder;


use Didslm\QueryBuilder\Builder\UpdateBuilder;
use Didslm\QueryBuilder\Components\Where;
use PHPUnit\Framework\TestCase;

class UpdateQueryTest extends TestCase
{
    public function testAddColumns(): void
    {
        $query = UpdateBuilder::into('users')
            ->addColumns(['name', 'email']);

        $this->assertInstanceOf(UpdateBuilder::class, $query);
    }

    public function testMismatchColumnsAndValuesThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Number of columns and values do not match');

        $builder = new UpdateBuilder('test_table');
        $builder->addColumns(['column1', 'column2'])->addValues(['value1']);
    }

    public function testPlaceholderInValues()
    {
        $builder = new UpdateBuilder('test_table');
        $sql = $builder->addColumns(['column1'])->addValues([':valueName'])->toSql();

        $this->assertEquals("UPDATE test_table  SET column1 = :valueName ", $sql);
    }

    public function testUpdateMultipleFields()
    {
        $builder = new UpdateBuilder('test_table');
        $sql = $builder->addColumns(['column1', 'column2'])->addValues(['value1', 'value2'])->toSql();

        $this->assertEquals("UPDATE test_table  SET column1 = 'value1', column2 = 'value2' ", $sql);
    }

    public function testMultipleWhereConditions()
    {
        $builder = new UpdateBuilder('test_table');
        $sql = $builder->addColumns(['column1'])
            ->addValues(['value1'])
            ->where(Where::create('column2', 'value2'))
            ->where(Where::create('column3', 'value3'))
            ->toSql();

        $this->assertEquals("UPDATE test_table  SET column1 = 'value1' WHERE column2 = 'value2' AND column3 = 'value3'", $sql);
    }

    /*
     * UPDATE t1
INNER JOIN t2 ON t2.t1_id = t1.id
INNER JOIN t3 ON t2.t3_id = t3.id
SET t1.a = 'something',
    t2.b = 42,
    t3.c = t2.c
WHERE t1.a = 'blah';
     */
    public function testUpdateWithJoins()
    {
        $builder = new UpdateBuilder('t1');
        $sql = $builder->addColumns(['t1.column1'])
            ->addValues(['value1'])
            ->innerJoin('table2', 'table2.user_id', 't1.id')
            ->innerJoin('table3', 'table3.order_id', 'table2.id')
            ->where(Where::create('t1.id', '1'))
            ->where(Where::create('table3.name', 'diar'))
            ->toSql();


        $this->assertEquals("UPDATE t1 INNER JOIN table2 ON table2.user_id = t1.id INNER JOIN table3 ON table3.order_id = table2.id SET t1.column1 = 'value1' WHERE t1.id = 1 AND table3.name = 'diar'", $sql);
    }
}
