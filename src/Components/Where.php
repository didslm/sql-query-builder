<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\QueryBuilderInterface;
use Didslm\QueryBuilder\Utilities\Cleaner;

class Where implements QueryBuilderInterface
{
    private string $field;
    private string $value;
    private string $operator;

    public function __construct(string $field, string $value, string $operator = '=')
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    public function toSql(): string
    {
        //check if string contains : and some string after like :test
        if (str_contains($this->value, ':') && strlen($this->value) > 1) {
            return sprintf('%s %s %s', $this->field, $this->operator, Cleaner::escapeString($this->value));
        }
        return sprintf("%s %s '%s'", $this->field, $this->operator, Cleaner::escapeString($this->value));
    }
}
