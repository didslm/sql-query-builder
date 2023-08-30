<?php

namespace Didslm\QueryBuilder\Builder;

interface SelectQueryBuilderInterface extends QueryBuilder
{
    public function limit(int $start, int $offset): QueryBuilder;
    public function orderBy(string $field, string $order = 'ASC'): QueryBuilder;
    public function select(string ...$fields): QueryBuilder;
    public function leftJoin(string $table, string $column, string $reference): QueryBuilder;

}