<?php

/**
 * Checks whether the input array is numeric
 *
 * @param array $array
 * @return bool
 */
function is_array_numeric(array $array): bool
{
    if (array() === $array) return true;
    return array_keys($array) === range(0, count($array) - 1);
}

/**
 * Checks whether the input array is associative
 * 
 * @param array $array
 * @return bool
 */

function is_array_assoc(array $array): bool
{
    return !is_array_numeric($array);
}