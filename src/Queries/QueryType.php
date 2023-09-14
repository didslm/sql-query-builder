<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Queries;

interface QueryType
{
    public function toSql(): string;
}