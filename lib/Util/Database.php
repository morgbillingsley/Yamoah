<?php

namespace Yamoah\Util;

/**
 * Class: Database
 */
class Database
{
    /**
     * @param string name of the table to search for
     * @return bool whether or not the table currently exists in the database
     */
    public static function table_exists(string $table_name)
    {
        global $wpdb;
        $query = $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->esc_like($table_name)
        );

        return $wpdb->get_var($query) == $table_name;
    }
}

?>