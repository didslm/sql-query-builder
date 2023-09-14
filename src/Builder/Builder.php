<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Builder;

use Didslm\QueryBuilder\Queries\QueryType;

interface Builder 
{
    public function build(): QueryType;
}