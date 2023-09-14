<?php declare(strict_types=1);


namespace Didslm\QueryBuilder\Tests\Conditions;
use Didslm\QueryBuilder\Components\Where;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase {

    public function testBasicCondition(): void
    {
        $where = new Where('name', 'John Doe');
        $this->assertEquals("name = 'John Doe'", $where->toSql());
    }

    public function testConditionWithOperator(): void
    {
        $where = new Where('age', 18, '>');
        
        $this->assertEquals("age > 18", $where->toSql());
    }

    public function testConditionWithInvalidOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid operator <=>');
        $where = new Where('age', 18, '<=>');
    }

    public function testConditionWithNumericValue(): void
    {
        $where = new Where('age', 18);
        $this->assertEquals("age = 18", $where->toSql());
    }

    public function testConditionWithNullValueAndOperator(): void
    {
        $where = new Where('age', null);
        $this->assertEquals("age IS NULL", $where->toSql());
    }

    public function testConditionWithNullValueAndOperatorNotEqualTo(): void
    {
        $where = new Where('age', null, '<>');
        $this->assertEquals("age IS NOT NULL", $where->toSql());
    }

    public function testConditionWithNullValueAndOperatorNotEqualTo2(): void
    {
        $where = new Where('age', null, '!=');
        $this->assertEquals("age IS NOT NULL", $where->toSql());
    }

}
