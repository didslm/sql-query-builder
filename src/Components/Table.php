<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Components;

class Table {

    public function __construct(private string $table, private ?string $alias){}

    public function getTable(): string
    {
        return $this->table;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function hasAlias(): bool
    {
        return $this->alias !== null;
    }

    public function toSql(): string
    {
        return $this->alias ? "{$this->table} AS {$this->alias}" : $this->table;
    }
}