<?php

declare(strict_types=1);

namespace Didslm\QueryBuilder\Trait;
use Didslm\QueryBuilder\Components\Where;

/**
 * This method used to get where on raw version
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */
trait getWhereTrait {
    public function getWhere() : string
    {
        $where = "";
        if (count($this->conditions) > 0) {
            $first = array_shift($this->conditions);
            $where .= $first->toSql();

            foreach ($this->conditions as $condition) {
                $andOr = Where::orInstance($condition) ? ' OR ' : ' AND ';
                $where .= $andOr.$condition->toSql();
            }
        }

        if(!preg_match("/^\(.+\)$/", $where)) {
            $where = sprintf("%s%s%s", "(", $where, ")");
        }

        return $where;
    }
}
