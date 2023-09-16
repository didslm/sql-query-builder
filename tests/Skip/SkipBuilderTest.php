<?php

namespace Didslm\QueryBuilder\Tests\Skip;


use Didslm\QueryBuilder\Builder\SelectBuilder;
use PHPUnit\Framework\TestCase;


class SkipBuilderTest extends TestCase
{
    /**
     * @group ignore
     */
    public function testSelectWithSubQuery()
    {
        $sql = SelectBuilder::from('users')
            ->select("id", "(select option from setting where setting_user_id = id) as option_user")
            ->where('status', 'active')
            ->build();

        $this->assertEquals("SELECT users.id, (select option from setting where setting_user_id = users.id) as option_user FROM users WHERE status = 'active'", $sql->toSql());
    }
}
