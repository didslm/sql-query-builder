<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Joins;
use Didslm\QueryBuilder\Components\Joins\LeftJoin;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Tests\Builder\SelectQueryBuilderTest;
use PHPUnit\Framework\TestCase;

class LeftJoinTest extends TestCase
{
    public function testBasicLeftJoin(): void
    {
        $join = new LeftJoin('users', 'users.id', 'posts.user_id');
        $this->assertEquals('LEFT JOIN users ON users.id = posts.user_id', $join->toSql());
    }

    public function testLeftJoinWithAlias(): void
    {
        $join = new LeftJoin(new Table('users', 'u'), 'id', 'user_id');
        $this->assertEquals('LEFT JOIN users AS u ON u.id = user_id', $join->toSql());
    }
}
