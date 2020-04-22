<?php

namespace Yamoah;

use Data_Resource;

/**
 * Class Table_Resource
 */
class Table_Resource
{
    /** @var string name of the datatable */
    public $name;

    /**
     * @param string value to set for the table name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param array arguments to query for a row / rows in this table
     * @return array an array of resource objects that were found from the query
     */
    public function find(array $args = array()): array
    {
        global $wpdb;
        $sql = "SELECT * FROM {$this->name};";
        $rows = $wpdb->get_results( $sql, ARRAY_A );
        $results = array();
        foreach($rows as $row) {
            $results[] = new Data_Resource($row, $this->name);
        }
        return $results;
    }

    /**
     * @param int id of the row being searched for
     * @return Data_Resource the resource object of the newly inserted data
     */
    public function find_one(int $id): Data_Resource
    {
        global $wpdb;
        // Write the query
        $sql = $wpdb->prepare("SELECT * FROM {$this->name} WHERE `ID` = %d;", $id);
        // Query for the row with the corresponding ID
        $row = $wpdb->get_row($sql, ARRAY_A);
        if (NULL === $row) {
            throw new Data_Query_Exception($wpdb->last_error, 2);
        } else {
            return new Data_Resource($row, $this->name);
        }
    }

    /**
     * @param array the data to be inserted into the table
     * @return Data_Resource the resource object of the newly inserted data
     */
    public function add(array $data): Data_Resource
    {
        global $wpdb;
        $inserted = $wpdb->insert($this->name, $data);
        if (false === $inserted) {
            throw new Data_Query_Exception($wpdb->last_error, 1);
        }
        // Get the ID of the newly inserted data
        $id = $wpdb->insert_id;
        // Get the resource of the newly inserted data
        return $this->find_one( intval($id) );
    }

    /**
     * @param array the criteria for data in the table that will be deleted
     */
    public function delete(array $args)
    {
        global $wpdb;
        $deleted = $wpdb->delete($this->name, $args);
        // If the query was unsuccessful, throw an error
        if (false === $deleted) {
            throw new Data_Query_Exception($wpdb->last_error, 4);
        }
    }

    /**
     * @param int the id of the row that will be deleted
     */
    public function delete_one(int $id)
    {
        global $wpdb;
        $deleted = $wpdb->delete($this->name, array( "ID" => $id ), "%d");
        // If the query was unsuccessful, throw an error
        if (false === $deleted) {
            throw new Data_Query_Exception($wpdb->last_error, 4);
        }
    }
}

?>