<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Builder;
use Didslm\QueryBuilder\Components\Condition;

interface QueryBuilder
{
    public function table(string $table): QueryBuilder;

    public function where(Condition $condition): QueryBuilder;

    public function innerJoin(string $table, string $column, string $reference): QueryBuilder;
}