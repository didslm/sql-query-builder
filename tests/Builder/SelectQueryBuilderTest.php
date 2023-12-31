<?php

namespace Didslm\QueryBuilder\Tests\Builder;


use Didslm\QueryBuilder\Builder\SelectBuilder;
use Didslm\QueryBuilder\Components\Joins\LeftJoin;
use Didslm\QueryBuilder\Components\Select;
use PHPUnit\Framework\TestCase;


class SelectQueryBuilderTest extends TestCase
{
    public function testBasicSelect()
    {
        $query = SelectBuilder::from('users')
            ->select(Select::ALL)->build();

        $this->assertEquals('SELECT users.* FROM users', $query->toSql());
    }

    public function testSelectWithWhere()
    {
        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->where(
                'age', 18, '>'
            )->build();

        $this->assertEquals("SELECT users.* FROM users WHERE age > 18", $query->toSql());
    }

    public function testSelectWithMultipleWhere()
    {
        $queryBuilder = new SelectBuilder();

        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->where(
                'age',
                18,
                '>'
            )->where(
                'status',
                'active'
            )
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE age > 18 AND status = 'active'", $query->toSql());
    }

    public function testSelectWithOrder()
    {

        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->orderBy('name')
            ->build();

        $this->assertEquals("SELECT users.* FROM users ORDER BY name ASC", $query->toSql());
    }

    public function testSelectWithRegex()
    {
        $query = SelectBuilder::from('users')
            ->select(Select::ALL)
            ->regex('name', 'a|z')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name REGEXP 'a|z'", $query->toSql());
    }

    public function testSelectWithMultipleRegexFields()
    {
        $sql = SelectBuilder::from('candidates')
            ->select(Select::ALL)
            ->regex('title', ':title')
            ->regex('level', ':level')
            ->build();

        $this->assertEquals("SELECT candidates.* FROM candidates WHERE title REGEXP :title AND level REGEXP :level", $sql->toSql());
    }

    public function testLeftJoinQuery()
    {
        $sql = SelectBuilder::from('users')
            ->join(new LeftJoin('posts', 'users.id', 'posts.user_id'))->build();

        $this->assertEquals("SELECT users.* FROM users LEFT JOIN posts ON users.id = posts.user_id", $sql->toSql());
    }

    public function testMultipleLeftJoins()
    {
        $sql = SelectBuilder::from('users')
            ->leftJoin('posts', 'users.id', 'posts.user_id')
            ->leftJoin('comments', 'users.id', 'comments.user_id')
            ->build();

        $this->assertEquals("SELECT users.* FROM users LEFT JOIN posts ON users.id = posts.user_id LEFT JOIN comments ON users.id = comments.user_id", $sql->toSql());
    }

    public function testMultipleLeftJoinsWithMultipleTableSelects()
    {
        $sql = SelectBuilder::from('users')
            ->leftJoin('posts', 'users.id', 'posts.user_id')
            ->leftJoin('comments', 'users.id', 'comments.user_id')
            ->select('users.id', 'posts.title', 'comments.body')->build();

        $this->assertEquals("SELECT users.id, posts.title, comments.body FROM users LEFT JOIN posts ON users.id = posts.user_id LEFT JOIN comments ON users.id = comments.user_id", $sql);
    }

    public function testInnerJoinQuery()
    {
        $sql = SelectBuilder::from('users')
            ->innerJoin('posts', 'users.id', 'posts.user_id')
            ->where('posts.status', 'published')
            ->where('posts.published_at', '2020-01-01', '>')
            ->build();


        $this->assertEquals("SELECT users.* FROM users INNER JOIN posts ON users.id = posts.user_id WHERE posts.status = 'published' AND posts.published_at > '2020-01-01'", $sql->toSql());
    }

    public function testSelectWithRegexConditionUsingPlaceholders()
    {
        $sql = SelectBuilder::from('users')
            ->regex('name', ':regex')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name REGEXP :regex", $sql->toSql());
    }

    public function testSelectWithRegexMultipleConditionsWithPlaceholders()
    {
        $sql = SelectBuilder::from('users')
            ->regex('name', ':regex')
            ->regex('email', ':regex')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name REGEXP :regex AND email REGEXP :regex", $sql->toSql());

    }

    public function testSelectWithInCondition()
    {
        $sql = SelectBuilder::from('users')
            ->in('name', ['John', 'Doe'])
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name IN ('John', 'Doe')", $sql->toSql());
    }

    public function testSelectWithGroupedConditions()
    {
        $sql = SelectBuilder::from('users')
            ->where('name', 'John')
            ->and('age', 18)
            ->and('email', 'selimi')
            ->and('name', 'test')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'John' AND age = 18 AND email = 'selimi' AND name = 'test')", $sql->toSql());
    }

    public function testSelectWithGroupedConditionsOr()
    {
        $sql = SelectBuilder::from('users')
            ->or('name', 'John')
            ->and('age', 18)
            ->or('email', 'test@gmail.com')
            ->and('name', 'test')
            ->build();


        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'John' OR age = 18) AND (email = 'test@gmail.com' OR name = 'test')", $sql->toSql());
    }

    public function testSelectWithGroupedConditionsOrBetween()
    {
        $sql = SelectBuilder::from('users')
            ->where('name', 'John')
            ->and('age', 18)
            ->where('email', 'test@gmail.com')
            ->and('name', 'test')
            ->orGroup()
            ->build();


        $this->assertEquals("SELECT users.* FROM users WHERE ((name = 'John' AND age = 18) OR (email = 'test@gmail.com' AND name = 'test'))", $sql->toSql());
    }

    public function testSelectWithLikeBeginCondition()
    {
        $sql = SelectBuilder::from('users')
            ->like('name', "doe%")
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name LIKE 'doe%'", $sql->toSql());
    }

    public function testSelectWithLikeEndCondition()
    {
        $sql = SelectBuilder::from('users')
            ->like('name', "%doe")
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name LIKE '%doe'", $sql->toSql());
    }

    public function testSelectWithMultipleLikeConditions()
    {
        $sql = SelectBuilder::from('users')
            ->where('name', "%doe", 'LIKE')
            ->and('email', "%doe", 'LIKE')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name LIKE '%doe' AND email LIKE '%doe')", $sql->toSql());
    }

    public function testSelectWithMultipleOrLikeConditions()
    {
        $sql = SelectBuilder::from('users')
            ->or('name', "%doe", 'LIKE')
            ->and('email', "%doe", 'LIKE')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name LIKE '%doe' OR email LIKE '%doe')", $sql->toSql());
    }

    public function testSelectWithLikeContainsCondition()
    {
        $sql = SelectBuilder::from('users')
            ->like('name', "%doe%")
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE name LIKE '%doe%'", $sql->toSql());
    }

}
