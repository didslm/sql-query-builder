<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Joins;
use Didslm\QueryBuilder\Components\Joins\FullJoin;
use Didslm\QueryBuilder\Components\Table;
use PHPUnit\Framework\TestCase;

class FullJoinTest extends TestCase
{
    public function testBasicFullJoin(): void
    {
        $join = new FullJoin('users', 'users.id', 'posts.user_id');
        $this->assertEquals('FULL OUTER JOIN users ON users.id = posts.user_id', $join->toSql());
    }

    public function testFullJoinWithAlias(): void
    {
        $join = new FullJoin(new Table('users', 'u'), 'id', 'user_id');
        $this->assertEquals('FULL OUTER JOIN users AS u ON u.id = user_id', $join->toSql());
    }
}
