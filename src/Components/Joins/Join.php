<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Components\Joins;

interface Join {
    public function toSql(): string;
    public function getColumn(): string;
    public function getTable(): string;
    public function getReference(): string;
    
}
