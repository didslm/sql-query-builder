<?php declare(strict_types=1);


namespace Didslm\QueryBuilder\Tests\Conditions;

use Didslm\QueryBuilder\Components\Condition;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase {

    public function testBasicCondition(): void
    {
        $where = new Condition('name', 'John Doe');
        $this->assertEquals("name = 'John Doe'", $where->toSql());
    }

    public function testConditionWithOperator(): void
    {
        $where = new Condition('age', 18, '>');
        
        $this->assertEquals("age > 18", $where->toSql());
    }

    public function testConditionWithInvalidOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid operator <=>');
        $where = new Condition('age', 18, '<=>');
    }

    public function testConditionWithNumericValue(): void
    {
        $where = new Condition('age', 18);
        $this->assertEquals("age = 18", $where->toSql());
    }

    public function testConditionWithNullValueAndOperator(): void
    {
        $where = new Condition('age', null);
        $this->assertEquals("age IS NULL", $where->toSql());
    }

    public function testConditionWithNullValueAndOperatorNotEqualTo(): void
    {
        $where = new Condition('age', null, '<>');
        $this->assertEquals("age IS NOT NULL", $where->toSql());
    }

    public function testConditionWithNullValueAndOperatorNotEqualTo2(): void
    {
        $where = new Condition('age', null, '!=');
        $this->assertEquals("age IS NOT NULL", $where->toSql());
    }

    public function testConditionWithLike()
    {
        $where = new Condition('name', 'John Doe', 'LIKE');
        $this->assertEquals("name LIKE 'John Doe'", $where->toSql());
    }

    public function testConditionWithRegexp()
    {
        $where = new Condition('name', 'John Doe', 'REGEXP');
        $this->assertEquals("name REGEXP 'John Doe'", $where->toSql());
    }

}
