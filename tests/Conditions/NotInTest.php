<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Conditions;

use Didslm\QueryBuilder\Components\NotIn;
use PHPUnit\Framework\TestCase;

class NotInTest extends TestCase
{
    function testBasicCondition(): void
    {
        $where = new NotIn('name', ['John Doe', 'Jane Doe']);
        $this->assertEquals("name NOT IN ('John Doe', 'Jane Doe')", $where->toSql());
    }

    function testInWithNumericValues(): void
    {
        $where = new NotIn('age', [18, 19]);
        $this->assertEquals("age NOT IN (18, 19)", $where->toSql());
    }


    function testInWithNumbersAndStrings(): void
    {
        $where = new NotIn('age', [18, 'test']);
        $this->assertEquals("age NOT IN (18, 'test')", $where->toSql());
    }

    function testInWithEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array cannot be empty');
        $where = new NotIn('age', []);
    }
}
