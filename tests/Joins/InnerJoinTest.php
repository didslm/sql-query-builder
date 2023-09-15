<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Joins;
use Didslm\QueryBuilder\Components\Joins\InnerJoin;
use Didslm\QueryBuilder\Components\Table;
use PHPUnit\Framework\TestCase;

class InnerJoinTest extends TestCase
{
    public function testBasicInnerJoin(): void
    {
        $join = new InnerJoin('users', 'users.id', 'posts.user_id');
        $this->assertEquals('INNER JOIN users ON users.id = posts.user_id', $join->toSql());
    }

    public function testInnerJoinWithAlias(): void
    {
        $join = new InnerJoin(new Table('users', 'u'), 'id', 'user_id');
        $this->assertEquals('INNER JOIN users AS u ON u.id = user_id', $join->toSql());
    }
}
