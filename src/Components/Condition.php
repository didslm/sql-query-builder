<?php

namespace Didslm\QueryBuilder\Components;

interface Condition
{
    const ALL_OPERATORS = [
        '=',
        '>',
        '<',
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
    ];

    public function getColumn(): string;
    public function getValue() : string;
    public function getOperator(): string;
    public function toSql(): string;
    public function __toString(): string;
}