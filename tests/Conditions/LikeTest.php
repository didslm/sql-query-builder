<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Conditions;

use Didslm\QueryBuilder\Components\Like;
use PHPUnit\Framework\TestCase;

class LikeTest extends TestCase 
{
    public function testBasicCondition(): void
    {
        $where = new Like('name', 'John Doe');
        $this->assertEquals("name LIKE 'John Doe'", $where->toSql());
    }

    public function testLikeWithRightWildcard(): void
    {
        $where = new Like('name', 'John%');
        $this->assertEquals("name LIKE 'John%'", $where->toSql());
    }

    public function testLikeWithLeftWildcard(): void
    {
        $where = new Like('name', '%John');
        $this->assertEquals("name LIKE '%John'", $where->toSql());
    }

    public function testLikeWithBothWildcards(): void
    {
        $where = new Like('name', '%John%');
        $this->assertEquals("name LIKE '%John%'", $where->toSql());
    }
}
