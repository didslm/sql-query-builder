<?php

namespace Didslm\QueryBuilder\Components;

interface GroupCondition extends Condition
{
    public function getConditions(): array;

    public function addCondition(Condition $condition): self;

}