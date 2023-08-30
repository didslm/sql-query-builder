<?php

namespace Didslm\QueryBuilder\Tests\Builder;

use Didslm\QueryBuilder\Builder\InsertBuilder;
use PHPUnit\Framework\TestCase;

class InsertQueryTest extends TestCase
{
    public function testAddColumns(): void
    {
        $query = InsertBuilder::into('users')
            ->addColumns(['name', 'email']);

        $this->assertInstanceOf(InsertBuilder::class, $query);
    }

    public function testAddValues(): void
    {
        $query = InsertBuilder::into('users')
            ->addColumns(['name', 'email'])
            ->addValues(['John Doe', 'john@example.com']);

        $this->assertInstanceOf(InsertBuilder::class, $query);
    }

    public function testAddValuesWithMismatchedColumns(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Number of columns and values do not match');

        $query = InsertBuilder::into('users')
            ->addColumns(['name', 'email'])
            ->addValues(['John Doe']);

        $this->assertInstanceOf(InsertBuilder::class, $query);
    }

    public function testToSql(): void
    {
        $query = InsertBuilder::into('users')
            ->addColumns(['name', 'email'])
            ->addValues(['John Doe', 'john@example.com']);

        $expectedSql = "INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com')";
        $this->assertEquals($expectedSql, $query->toSql());
    }
}
