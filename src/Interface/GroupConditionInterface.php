<?php

namespace Didslm\QueryBuilder\Interface;

interface GroupConditionInterface extends ConditionInterface
{
    public function getConditions(): array;

    public function addCondition(ConditionInterface $condition): self;

}
