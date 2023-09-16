<?php

namespace Didslm\QueryBuilder\Components;

interface ConditionInterface
{
    const ALL_OPERATORS = [
        '=',
        '>',
        '<',
        '!=',
        '>=',
        '<=',
        '<>',
        'LIKE',
        'NOT LIKE',
        'IN',
        'NOT IN',
        'BETWEEN',
        'NOT BETWEEN',
        'IS NULL',
        'IS NOT NULL',
        'LIKE',
        'REGEXP',
    ];

    const DEFAULT_OPERATORS_FOR_NULL = [
        '=' => 'IS NULL',
        '<>' => 'IS NOT NULL',
        '!=' => 'IS NOT NULL',
        '<' => 'IS NOT NULL',
    ];

    public function getColumn(): string;
    public function getValue() : string;
    public function getOperator(): string;
    public function toSql(): string;
    public function __toString(): string;
}