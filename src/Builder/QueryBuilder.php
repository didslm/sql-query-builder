<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Builder;
interface QueryBuilder
{
    public function table(string $table): QueryBuilder;
    public function select(string ...$fields): QueryBuilder;
    public function where(string $field, string $value, string $operator = '='): QueryBuilder;
    public function limit(int $start, int $offset): QueryBuilder;
    public function orderBy(string $field, string $order = 'ASC'): QueryBuilder;

    public function leftJoin(string $table, string $column, string $reference): QueryBuilder;
    public function innerJoin(string $table, string $column, string $reference): QueryBuilder;

}