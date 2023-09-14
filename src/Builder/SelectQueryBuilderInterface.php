<?php

namespace Didslm\QueryBuilder\Builder;

use Didslm\QueryBuilder\Components\Condition;

interface SelectQueryBuilderInterface extends QueryBuilder
{
    public function limit(int $start, int $offset): QueryBuilder;
    public function orderBy(string $field, string $order = 'ASC'): QueryBuilder;
    public function select(string ...$fields): QueryBuilder;
    public function where(Condition $condition): QueryBuilder;
    public function leftJoin(string $table, string $column, string $reference): QueryBuilder;

}