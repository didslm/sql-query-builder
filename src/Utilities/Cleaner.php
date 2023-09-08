<?php

namespace Didslm\QueryBuilder\Utilities;

class Cleaner {

    /**
     * Function to escape string inputs.
     *
     * @param string $input
     * @return string
     */
    public static function escapeString(string $input): string {
        return addslashes($input);
    }

    /**
     * Function to escape integer inputs.
     *
     * @param int $input
     * @return int
     */
    public static function escapeInt(int $input): int {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Function to escape array inputs.
     *
     * @param array $input
     * @return array
     */
    public static function escapeArray(array $input): array {
        foreach ($input as &$value) {
            if (is_string($value)) {
                $value = self::escapeString($value);
            } elseif (is_int($value)) {
                $value = self::escapeInt($value);
            } elseif (is_array($value)) {
                $value = self::escapeArray($value);
            } else {
                $value = $value;
            }
        }
        return $input;
    }

    public static function clean(mixed $value)
    {
        if (is_string($value)) {
            return self::escapeString($value);
        } elseif (is_int($value)) {
            return self::escapeInt($value);
        } elseif (is_array($value)) {
            return self::escapeArray($value);
        } else {
            return $value;
        }
    }
}
