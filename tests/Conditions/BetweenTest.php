<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Conditions;

use Didslm\QueryBuilder\Components\Between;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{
    function testBasicCondition(): void
    {
        $where = new Between('id', [2, 5]);
        $this->assertEquals("id BETWEEN 2 AND 5", $where->toSql());
    }

    function testBetweenWithNumericValues(): void
    {
        $where = new Between('age', [18, 25]);
        $this->assertEquals("age BETWEEN 18 AND 25", $where->toSql());
    }

    function testBetweenWithEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array cannot be empty');
        $where = new Between('age', []);
    }

    function testBetweenWithWrongArrayValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The first array index value should be lower than the second array index value.');

        $where = new Between('age', [25, 18]);
        $this->assertEquals("age BETWEEN 18 AND 25", $where->toSql());
    }

    function testBetweenWithLessThanTwoArrayValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The array is limited to only 2 index for its values.');

        $where = new Between('age', [25]);
    }

    function testBetweenWithMoreThanTwoArrayValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The array is limited to only 2 index for its values.');

        $where = new Between('age', [25, 26, 27]);
    }
}
