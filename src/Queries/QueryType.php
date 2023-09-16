<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Queries;

interface QueryType
{
    public function __toString(): string;
    public function toSql(): string;
    public function getWhere(): string;
}
