<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Conditions;

use Didslm\QueryBuilder\Components\In;
use PHPUnit\Framework\TestCase;

class InTest extends TestCase 
{
    function testBasicCondition(): void
    {
        $where = new In('name', ['John Doe', 'Jane Doe']);
        $this->assertEquals("name IN ('John Doe', 'Jane Doe')", $where->toSql());
    }

    function testInWithNumericValues(): void
    {
        $where = new In('age', [18, 19]);
        $this->assertEquals("age IN (18, 19)", $where->toSql());
    }


    function testInWithNumbersAndStrings(): void
    {
        $where = new In('age', [18, 'test']);
        $this->assertEquals("age IN (18, 'test')", $where->toSql());
    }

    function testInWithEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array cannot be empty');
        $where = new In('age', []);
    }
}
