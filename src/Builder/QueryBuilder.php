<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Builder;

interface QueryBuilder
{
    public function table(string $table): QueryBuilder;

    public function where(string $column, mixed $value, ?string $operator = null): QueryBuilder;

    public function innerJoin(string $table, string $column, string $reference): QueryBuilder;
}
