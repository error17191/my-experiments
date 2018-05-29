<?php

/**
 * Checks whether the input array is numeric
 *
 * @param array $array
 * @return bool
 */
function is_numeric_array(array $array): bool
{
    if ([] === $array) return true;
    return array_keys($array) === range(0, count($array) - 1);
}

/**
 * Checks whether the input array is associative
 * 
 * @param array $array
 * @return bool
 */

function is_assoc_array(array $array): bool
{
    return !is_numeric_array($array);
}