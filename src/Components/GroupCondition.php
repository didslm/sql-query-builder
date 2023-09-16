<?php

namespace Didslm\QueryBuilder\Components;

interface GroupCondition
{
    public function type(): string;

    public function toSql(): string;

}