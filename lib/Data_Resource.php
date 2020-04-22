<?php

namespace Yamoah;

use Exception\Data_Query_Exception;

/**
 * Class Data_Resource
 */
class Data_Resource
{
    /** @var string the name of the parent table of this data */
    private $table;
    /** @var string MySQL BIGINT(20) to uniquely identify the row among all others in the table */
    public $ID;
    /** @var string MySQL timestamp of when the row was created */
    public $created_at;
    /** @var string MySQL timestamp of when the row was last updated */
    public $updated_at;

    public function __construct(array $data = array(), string $table)
    {
        // Set the name of the table
        $this->table = $table;
        // Map each value to this object
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return Data_Resource the refreshed resource object
     */
    public function refresh(): Data_Resource
    {
        global $wpdb;
        // Write the query
        $sql = $wpdb->prepare("SELECT * FROM {$this->table} WHERE `ID` = %d;", intval( $this->ID ));
        // Execute the query
        $row = $wpdb->get_row($sql);
        // If there was an error refreshing the object, throw an error, otherwise return the new resource object
        if (false === $row) {
            throw new Data_Query_Exception($wpdb->last_error, 2);
        } else {
            return new Data_Resource($new_data, $this->table);
        }
    }

    /**
     * @param array associative array of field_name => updated_value
     * @return Data_Resource updated Data_Resource object with new data
     */
    public function update(array $data = array())
    {
        global $wpdb;
        $updated = $wpdb->update($this->table, $data, [ "ID" => $this->ID ]);
        if (false === $updated) {
            throw new Data_Query_Exception($wpdb->last_error, 3);
        } else {
            return $this->refresh();
        }
    }

    /**
     * @return self this object after it has been removed from the database
     */
    public function delete()
    {
        global $wpdb;
        $deleted = $wpdb->delete($this->table, [ "ID" => $this->ID ]);
        if (false === $deleted) {
            throw new Data_Query_Exception($wpdb->last_error, 4);
        } else {
            return $this;
        }
    }
}

?>