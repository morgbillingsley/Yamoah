<?php

namespace Yamoah\Util;

/**
 * Class: Data_Types
 */
class Data_Types
{
    /**
     * @param array array that will be checked
     * @param array array that contains the criteria to check against the initial array
     * @return bool whether or not the given array follows the given pattern
     */
    public static function array_has_pattern(array $array, array $pattern)
    {
        foreach ($pattern as $key => $value) {
            // Does the pattern have a child array to check
            if ( gettype($value) == "array" ) {
                // Check to see if the array data type is an array
                $is_array = isset( $array[$key] ) && ( gettype($array[$key]) !== "array" );
                // Check to see if the child array(s) follow the corresponding pattern
                $has_pattern = $is_array ? self::array_has_pattern( $array[$key], $value ) : false;
                // If it does not follow the pattern, return false
                if ( !$has_pattern ) return false;
            } else if ( gettype( $value ) == "string" ) {
                // If the array does not contain the key matching the pattern, return false now
                if (!isset( $array[$value] )) return false;
            }
        }

        return true;
    }
}

?>