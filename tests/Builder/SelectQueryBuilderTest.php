<?php

namespace Didslm\QueryBuilder\Tests\Builder;


use Didslm\QueryBuilder\Builder\SelectQueryBuilder;
use Didslm\QueryBuilder\Components\Select;
use PHPUnit\Framework\TestCase;


class SelectQueryBuilderTest extends TestCase
{
    public function testBasicSelect()
    {
        $query = SelectQueryBuilder::from('users')
            ->select(Select::ALL)->toSql();

        $this->assertEquals('SELECT * FROM users', $query);
    }

    public function testSelectWithWhere()
    {
        $query = SelectQueryBuilder::from('users')
            ->select(Select::ALL)
            ->where('age', '>', 18)->toSql();

        $this->assertEquals("SELECT * FROM users WHERE age > '18'", $query);
    }

    public function testSelectWithMultipleWhere()
    {
        $queryBuilder = new SelectQueryBuilder();

        $query = SelectQueryBuilder::from('users')
            ->select(Select::ALL)
            ->where('age', '>', 18)
            ->where('status', '=', 'active')
            ->toSql();

        $this->assertEquals("SELECT * FROM users WHERE age > '18' AND status = 'active'", $query);
    }

    public function testSelectWithOrder()
    {

        $query = SelectQueryBuilder::from('users')
            ->select(Select::ALL)
            ->orderBy('name')
            ->toSql();

        $this->assertEquals("SELECT * FROM users ORDER BY name ASC", $query);
    }

    public function testSelectWithRegex()
    {
        $query = SelectQueryBuilder::from('users')
            ->select(Select::ALL)
            ->whereRegex('name', implode('|', ['a', 'z']))
            ->toSql();

        $this->assertEquals("SELECT * FROM users WHERE name REGEXP 'a|z'", $query);
    }

    public function testSelectWithMultipleRegexFields()
    {
        $sql = SelectQueryBuilder::from('candidates')
            ->select(Select::ALL)
            ->where('title', 'REGEXP', ':title')
            ->where('level', 'REGEXP', ':level')
            ->toSql();

        $this->assertEquals("SELECT * FROM candidates WHERE title REGEXP ':title' AND level REGEXP ':level'", $sql);
    }

}
