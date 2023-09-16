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
            ->or('name', 'test')
            ->and('email', 'selimi')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'John' AND age = 18 AND email = 'selimi') OR (name = 'test' AND email = 'selimi')", $sql->toSql());
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

    public function testInnerJoinWithSubQuery()
    {
        $subQuery = SelectBuilder::from("test");

        $sql = SelectBuilder::from('users')
            ->innerJoin("({$subQuery->build()->toSql()}) as test", 'users.id', 'test.user_id')
            ->where('test.status', 'published')
            ->build();

        $this->assertEquals("SELECT users.* FROM users INNER JOIN (SELECT test.* FROM test) as test ON users.id = test.user_id WHERE test.status = 'published'", $sql->toSql());
    }

    public function testSelectWithNestedCondition()
    {
        $sql = SelectBuilder::from('users')
            ->where(function ($query) {
                $query->where("name", 'username')
                    ->and("age", 18);
            })
            ->or("email", 'mail@email.com')
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'username' AND age = 18) OR email = 'mail@email.com'", $sql->toSql());
    }

    public function testSelectWithMoreNestedCondition()
    {
        $sql = SelectBuilder::from('users')
            ->where(function ($query) {
                $query->where("name", 'username')
                    ->and("age", 18);
            })
            ->or(function ($query) {
                $query->where("email", 'test@email.com')
                    ->and("name", "john");
            })
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'username' AND age = 18) OR (email = 'test@email.com' AND name = 'john')", $sql->toSql());
    }

    public function testSelectWithMoreDeepNestedCondition()
    {
        $sql = SelectBuilder::from('users')
            ->where(function ($sub1) {
                $sub1->where("name", 'username')
                    ->and("age", 18);
            })
            ->or(function ($sub2) {
                $sub2->where("email", 'test@email.com')
                    ->or(function ($sub3) {
                        $sub3->where("class", "1A")
                            ->or("banned", 0);
                    });
            })
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'username' AND age = 18) OR (email = 'test@email.com' OR (class = '1A' OR banned = 0))", $sql->toSql());
    }

    public function testSelectWithAndNestedCondition()
    {
        $sql = SelectBuilder::from('users')
            ->where(function ($query) {
                $query->where("name", 'username')
                    ->and("age", 18);
            })
            ->and(function ($query) {
                $query->where("email", 'test@email.com')
                    ->or("name", "john");
            })
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'username' AND age = 18) AND (email = 'test@email.com' OR name = 'john')", $sql->toSql());
    }

    public function testSelectWithMoreDeepAndNestedCondition()
    {
        $sql = SelectBuilder::from('users')
            ->where(function ($sub1) {
                $sub1->where("name", 'username')
                    ->and("age", 18);
            })
            ->and(function ($sub2) {
                $sub2->where("email", 'test@email.com')
                    ->and(function ($sub3) {
                        $sub3->where("class", "1A")
                            ->and("banned", 0);
                    });
            })
            ->build();

        $this->assertEquals("SELECT users.* FROM users WHERE (name = 'username' AND age = 18) AND (email = 'test@email.com' AND (class = '1A' AND banned = 0))", $sql->toSql());
    }

    public function testSelectWithSubQuery()
    {
        $this->markTestSkipped('will be patch.');
        $sql = SelectBuilder::from('users')
            ->select("id", "(select option from setting where setting_user_id = id) as option_user")
            ->where('status', 'active')
            ->build();

        $this->assertEquals("SELECT users.id, (select option from setting where setting_user_id = users.id) as option_user FROM users WHERE status = 'active'", $sql->toSql());
    }
}
