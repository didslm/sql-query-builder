<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Builder;
interface QueryBuilder
{
    public function table(string $table): QueryBuilder;

    public function where(string $field, string $value, string $operator = '='): QueryBuilder;

    public function innerJoin(string $table, string $column, string $reference): QueryBuilder;
}