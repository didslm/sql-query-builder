<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Tests\Joins;
use Didslm\QueryBuilder\Components\Joins\RightJoin;
use Didslm\QueryBuilder\Components\Table;
use PHPUnit\Framework\TestCase;

class RightJoinTest extends TestCase
{
    public function testBasicRightJoin(): void
    {
        $join = new RightJoin('users', 'users.id', 'posts.user_id');
        $this->assertEquals('RIGHT JOIN users ON users.id = posts.user_id', $join->toSql());
    }

    public function testRightJoinWithAlias(): void
    {
        $join = new RightJoin(new Table('users', 'u'), 'id', 'user_id');
        $this->assertEquals('RIGHT JOIN users AS u ON u.id = user_id', $join->toSql());
    }
}
