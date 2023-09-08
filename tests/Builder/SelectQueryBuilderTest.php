<?php

namespace Didslm\QueryBuilder\Tests\Builder;


use Didslm\QueryBuilder\Builder\SelectBuilder;
use Didslm\QueryBuilder\Components\Select;
use PHPUnit\Framework\TestCase;


class SelectQueryBuilderTest extends TestCase
{
    public function testBasicSelect()
    {
        $query = SelectBuilder::from('users')
            ->select(Select::ALL)->toSql();

        $this->assertEquals('SELECT users.* FROM users', $query);
    }

    public function testSelectWithWhere()
    {
        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->where('age', 18, '>')->toSql();

        $this->assertEquals("SELECT users.* FROM users WHERE age > '18'", $query);
    }

    public function testSelectWithMultipleWhere()
    {
        $queryBuilder = new SelectBuilder();

        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->where('age', 18, '>')
            ->where('status', 'active', '=')
            ->toSql();

        $this->assertEquals("SELECT users.* FROM users WHERE age > '18' AND status = 'active'", $query);
    }

    public function testSelectWithOrder()
    {

        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->orderBy('name')
            ->toSql();

        $this->assertEquals("SELECT users.* FROM users ORDER BY name ASC", $query);
    }

    public function testSelectWithRegex()
    {
        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->whereRegex('name', implode('|', ['a', 'z']))
            ->toSql();

        $this->assertEquals("SELECT users.* FROM users WHERE name REGEXP 'a|z'", $query);
    }

    public function testSelectWithMultipleRegexFields()
    {
        $sql = SelectBuilder::from('candidates')
            ->select(Select::ALL)
            ->where('title', ':title', 'REGEXP')
            ->where('level', ':level', 'REGEXP')
            ->toSql();

        $this->assertEquals("SELECT candidates.* FROM candidates WHERE title REGEXP :title AND level REGEXP :level", $sql);
    }

    public function testLeftJoinQuery()
    {
        $sql = SelectBuilder::from('users')
            ->leftJoin('posts', 'users.id', 'posts.user_id');

        $this->assertEquals("SELECT users.* FROM users LEFT JOIN posts ON users.id = posts.user_id", $sql);
    }

    public function testMultipleLeftJoins()
    {
        $sql = SelectBuilder::from('users')
            ->leftJoin('posts', 'users.id', 'posts.user_id')
            ->leftJoin('comments', 'users.id', 'comments.user_id');

        $this->assertEquals("SELECT users.* FROM users LEFT JOIN posts ON users.id = posts.user_id LEFT JOIN comments ON users.id = comments.user_id", $sql);
    }

    public function testMultipleLeftJoinsWithMultipleTableSelects()
    {
        $sql = SelectBuilder::from('users')
            ->leftJoin('posts', 'users.id', 'posts.user_id')
            ->leftJoin('comments', 'users.id', 'comments.user_id')
            ->select('users.id', 'posts.title', 'comments.body');

        $this->assertEquals("SELECT users.id, posts.title, comments.body FROM users LEFT JOIN posts ON users.id = posts.user_id LEFT JOIN comments ON users.id = comments.user_id", $sql);
    }

    public function testInnerJoinQuery()
    {
        $sql = SelectBuilder::from('users')
            ->innerJoin('posts', 'users.id', 'posts.user_id')
            ->where('posts.status', 'published', '=')
            ->where('posts.published_at', '2020-01-01', '>')
            ->toSql();

        $this->assertEquals("SELECT users.* FROM users INNER JOIN posts ON users.id = posts.user_id WHERE posts.status = 'published' AND posts.published_at > '2020-01-01'", $sql);
    }

    public function testSelectWithRegexConditionUsingPlaceholders()
    {
        $sql = SelectBuilder::from('users')
            ->where('name', ':regex', 'REGEXP')
            ->toSql();

        $this->assertEquals("SELECT users.* FROM users WHERE name REGEXP :regex", $sql);
    }

    public function testSelectWithRegexMultipleConditionsWithPlaceholders()
    {
        $sql = SelectBuilder::from('users')
            ->where('name', ':regex', 'REGEXP')
            ->where('email', ':regex', 'REGEXP')
            ->toSql();

        $this->assertEquals("SELECT users.* FROM users WHERE name REGEXP :regex AND email REGEXP :regex", $sql);

    }

}
