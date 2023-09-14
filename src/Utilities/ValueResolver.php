<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Utilities;

use Didslm\QueryBuilder\Components\Table;

class ValueResolver
{
    public static function resolve(string $value): string
    {
        if (str_contains($value, ':') && strlen($value) > 1) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value;
        }

        return sprintf("'%s'", Cleaner::escapeString($value));

    }
}