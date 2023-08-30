<?php declare(strict_types=1);

namespace Didslm\QueryBuilder;

interface QueryBuilderInterface
{
    public function toSql(): string;
}