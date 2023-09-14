<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Utilities;

use Didslm\QueryBuilder\Components\Table;

class AliasResolver
{
    public static function resolve(?Table $table, string $column): string
    {
        if (str_contains($column, '.') || $table === null) {
            return $column;
        }

        return $table->hasAlias() ? "{$table->getAlias()}.{$column}" : "{$table->getTable()}.{$column}";
    }
}