<?php

namespace Didslm\QueryBuilder\Tests\Queries;

use Didslm\QueryBuilder\Components\AndGroup;
use Didslm\QueryBuilder\Components\OrGroup;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Components\Condition;
use Didslm\QueryBuilder\Queries\Select;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testBasicSelect(): void
    {
        $query = (new Select(new Table('users')))->addColumn('*');
        $query->addGroup(AndGroup::create(
            OrGroup::create(
                new Condition('age', 18, '>'),
            ),
            OrGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            )
        ));

        $this->assertEquals("SELECT * FROM users WHERE (age > 18 AND (status = 'active' OR name = 'John'))", $query->toSql());
    }

    public function testSelectWithOrder(): void
    {
        $query = (new Select(new Table('users')))->addColumn('*');
        $query->addGroup(AndGroup::create(
            OrGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            ),
            OrGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            )
        ));

        $this->assertEquals("SELECT * FROM users WHERE ((status = 'active' OR name = 'John') AND (status = 'active' OR name = 'John'))", $query->toSql());
    }

    public function testSelectOrAndWith(): void
    {
        $query = (new Select(new Table('users')))->addColumn('*');
        $query->addGroup(AndGroup::create(
            OrGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            ),
            AndGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            )
        ));

        $this->assertEquals("SELECT * FROM users WHERE ((status = 'active' OR name = 'John') AND (status = 'active' AND name = 'John'))", $query->toSql());
    }

    public function testSelectWithJoin(): void
    {
        $query = (new Select(new Table('users')))->addColumn('*');

        $query->addGroup(OrGroup::create(
            AndGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            ),
            AndGroup::create(
                new Condition('status', 'active'),
                new Condition('name', 'John')
            )
        ));

        $this->assertEquals("SELECT * FROM users WHERE ((status = 'active' AND name = 'John') OR (status = 'active' AND name = 'John'))", $query->toSql());
    }
}