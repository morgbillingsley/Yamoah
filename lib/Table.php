<?php

namespace Yamoah;

use \Yamoah\Util\Database;
use \Yamoah\Util\Data_Types;
use \Yamoah\Util\Admin_Data_Table;
use \Yamoah\Exception\Data_Type_Exception;
use \Yamoah\Exception\Table_Query_Exception;

/**
 * Class Table
 */
class Table
{
    /**
     * @param array the arguments for creating the database
     * @return Table_Resource table object to create and select data
     */
    public static function create(array $args)
    {
        // Check to see if the submitted arguments conform
        $has_pattern = Data_Types::array_has_pattern( $args, ["name","schema"] );
        if (!$has_pattern) {
            throw new Data_Type_Exception("The array submitted does not follow the correct pattern");
        } else {
            // Store the resource name in memory
            $name = $args["name"];
            // Store the resource schema in memory
            $schema = $args["schema"];
            // If set, store the create_pages option in memory, otherwise, set to true
            $create_pages = isset( $args["create_pages"] ) && gettype( $args["create_pages"] ) == "boolean" ? $args["create_pages"] : true;
        }
        // Bring in the WordPress database object
        global $wpdb;
        $table_name = $wpdb->prefix . $name;
        // Retrieve the character set for the database
        $charset_collate = $wpdb->get_charset_collate();
        $do_create_table = true;
        // Check to see if the table already exists
        if ($do_create_table && !Database::table_exists( $table_name )) {
            // Write the SQL query to create the data table in MySQL
            $sql = "CREATE TABLE $table_name (
                ID BIGINT(20) AUTO_INCREMENT NOT NULL,\n"
                . self::build_table_schema( $schema ) .
                "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                modified_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (ID)
            ) $charset_collate;";
            // Execute the query
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }
        $table_resource = new Table_Resource( $name );
        // Check to see if the user wants admin pages to be created
        if ( $create_pages ) {
            // Create the Admin_Menu resource
            $admin_menu = new Admin_Menu( $table_resource, $schema );
            // Create the admin menu
            add_action( "admin_menu", [ $admin_menu, "create" ] );
        }
        // return the table resource
        return $table_resource;
    }

    /**
     * @param array the schema for the database
     * @return string the SQL created based off of the schema provided
     */
    private static function build_table_schema(array $schema): string
    {
        $query = "";
        foreach ($schema as $name => $meta) {
            if (gettype( $meta ) == "string") {
                $query .= $name . " " . self::get_sql_data_type( $meta ) . ",\n";
            } else if (gettype( $meta ) == "array") {
                // Check to see if the user submitted a data type
                $data_type = isset( $meta["type"] ) ? $meta["type"] : "string";
                // Get the SQL data type
                $sql_type = self::get_sql_data_type( $data_type );
                // Check if the user submitted a value for whether or not the field can be NULL
                // Default value is true
                $allow_null = isset( $meta["null"] ) && gettype( $meta["null"] ) ? $meta["null"] : true;
                // Check if field should be unique
                $is_unique = isset( $meta["unique"] ) && gettype( $meta["unique"] ) ? $meta["unique"] : false;
                // Build SQL statement for field
                $statement = $name . " " . $sql_type;
                $statement .= $allow_null ? "" : " NOT NULL";
                $statement .= $is_unique ? " UNIQUE" : "";
                $statement .= ",\n";
                // Add the built SQL statement to the query
                $query .= $statement;
            }
        }
        return $query;
    }

    /**
     * @param string the type of data submitted by the user
     * @return string the equivalent data type that MySQL can understand
     */
    private static function get_sql_data_type(string $type): string
    {
        switch ($type) {
            case "string":
                return "VARCHAR(255)";
                break;
            case "int":
                return "INT";
                break;
            case "integer":
                return "INT";
                break;
            case "text":
                return "TEXT";
                break;
            case "date":
                return "DATETIME";
                break;
            case "datetime":
                return "DATETIME";
                break;
            case "bool":
                return "TINYINT(1)";
                break;
            case "boolean":
                return "TINYINT(1)";
                break;
            case "array":
                return "LONGTEXT";
                break;
            case "currency":
                return "DECIMAL(15,2)";
                break;
            case "media":
                return "LONGTEXT";
                break;
            default:
                return "VARCHAR(255)";
                break;
        }
    }

    /**
     * @param string the name of the data table as shown in the mysql database
     * @return Table_Resource a resource object for the table
     */
    public static function retrieve(string $name): Table_Resource
    {
        global $wpdb;
        if (Database::table_exists( $wpdb->prefix . $name )) {
            return new Table_Resource( $name );
        } else {
            throw new Table_Query_Exception("There are no data tables in the database named $name", 2);
        }
    }

    /**
     * @param string the name of the data table shown in the mysql database
     * @param array the arguments to adjust the data table
     * @return Table_Resource the updated resource object for the table
     */
    public static function update(string $name, array $args): Table_Resource
    {
        global $wpdb;
        // Write the query
        $sql = "ALTER TABLE $name " . self::build_alter_schema($args) . ";";
        // Execute the query
        $altered = $wpdb->query($sql);
        // Check if the query succeeded
        if (false === $altered) {
            throw new Table_Query_Exception($wpdb->last_error, 3);
        } else {
            return new Table_Resource($name);
        }
    }

    /**
     * @param string the schema that needs to be updated in the database
     * @return string the generated SQL Query statements
     */
    private static function build_alter_schema(array $schema): string
    {
        $query = "";
        foreach ($schema as $col => $meta) {
            // Set the action that needs to be taken
            $action = isset( $meta["action"] ) ? $meta["action"] : "add";
            switch ($action) {
                case 'delete':
                    $statement = "DROP COLUMN $col";
                    break;
                case 'change':
                    $statement = "CHANGE COLUMN $col ";
                    $statement .= isset( $meta["name"] ) ? $meta["name"] : $col;
                    $statement .= isset( $meta["type"] ) ? " " . self::get_sql_data_type( $meta["type"] ) : "";
                    $statement .= isset( $meta["null"] ) && gettype( $meta["null"] ) == "boolean" ?
                        $meta["null"] ? "" : "NOT NULL" : "";
                    $statement .= isset( $meta["unique"] ) && gettype( $meta["unique"] ) == "boolean" ?
                        $meta["unique"] ? " UNIQUE" : "" : "";
                default:
                    $statement = "ADD COLUMN $col ";
                    $statement .= isset( $meta["type"] ) ? " " . self::get_sql_data_type( $meta["type"] ) : "";
                    $statement .= isset( $meta["null"] ) && gettype( $meta["null"] ) == "boolean" ?
                        $meta["null"] ? "" : "NOT NULL" : "";
                    $statement .= isset( $meta["unique"] ) && gettype( $meta["unique"] ) == "boolean" ?
                        $meta["unique"] ? " UNIQUE" : "" : "";
                    break;
            }
            $statement .= ", ";
            $query .= $statement;
        }
        return rtrim($query, ", ");
    }

    /**
     * @param string the name of the table to be deleted
     */
    public static function delete(string $name)
    {
        global $wpdb;
        $deleted = $wpdb->query("DROP TABLE IF EXISTS $name");
        if (false === $deleted) {
            throw new Table_Query_Exception($wpdb->last_error, 4);
        }
    }
}

?>