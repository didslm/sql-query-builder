<?php

namespace Didslm\QueryBuilder\Components;

use Closure;
use Didslm\QueryBuilder\Interface\ConditionInterface;
use Didslm\QueryBuilder\Interface\GroupConditionInterface;
use Didslm\QueryBuilder\Utilities\Cleaner;

class Where extends AbstractCondition implements GroupConditionInterface
{
    private const DEFAULT_OPERATOR = '=';

    private array $conditions = [];

    public function __construct(string|Closure $field, string|float|int|null $value = null, ?string $operator = null)
    {

        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator ?? self::DEFAULT_OPERATOR;

        if (!in_array($this->operator, self::ALL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator %s', $this->operator));
        }
    }

    public function toSql(): string
    {
        $sql = $this->buildCondition();
        if (count($this->conditions) === 0) {
            return $sql;
        }

        foreach ($this->conditions as $condition) {
            $sql = sprintf('%s AND %s', $sql, $condition->toSql());
        }

        if(count($this->conditions) > 0) {
            return sprintf('(%s)', $sql);
        } else {
            return $sql;
        }
    }

    private function buildCondition(): string
    {
        if (is_null($this->value)) {
            return sprintf('%s %s', $this->field, self::DEFAULT_OPERATORS_FOR_NULL[$this->operator] ?? $this->operator);
        }

        if (is_numeric($this->value)) {
            return sprintf('%s %s %s', $this->field, $this->operator, $this->value);
        }

        if (str_contains($this->value, ':') && strlen($this->value) > 1) {
            return sprintf('%s %s %s', $this->field, $this->operator, Cleaner::escapeString($this->value));
        }

        return sprintf("%s %s '%s'", $this->field, $this->operator, Cleaner::escapeString($this->value));
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function addCondition(ConditionInterface $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public static function orInstance($obj)
    {
        $relevantClasses = [OrImpl::class, OrImplRaw::class];
        return in_array(get_class($obj), $relevantClasses);
    }
}
