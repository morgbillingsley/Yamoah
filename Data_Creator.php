<?php

// Required to use dbDelta function from WordPress
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
// Bring in the data table class
require_once(__DIR__ . DIRECTORY_SEPARATOR . "Data_Table.php");
// Bring in the page creator class
require_once(__DIR__ . DIRECTORY_SEPARATOR . "Page_Creator.php");

/**
 * required parameters:
 * schema - array(
 *  'resource' => 'string',
 *  'fields' => array(
 *      'field' => array(
 *          'type' => 'string',
 *          'null' => 'boolean',
 *          'unique' => 'boolean',
 *      )
 *  ),
 * )
 */

class Data_Creator
{

    public $db_version;
    public $schema = array(
        "create_pages" => true,
        "capability" => "edit_others_posts",
        "dashicon" => "dashicons-screenoptions",
        "position" => null
    );
    public $slug;

    public function __construct(array $schema, float $db_version = 1.0)
    {
        $this->db_version = $db_version;
        $this->schema = array_merge($this->schema, $schema);
        if (isset( $this->schema["resource"] )) {
            $table = str_replace(" ", "_", $this->schema["resource"]);
            $this->schema["resource_slug"] = strtolower($table);
            $this->schema["table"] = $this->schema["resource_slug"] . "s";
        }
        $this->slug = "yamoah/{$this->schema['resource_slug']}";
        $this->init();
    }

    public function activate()
    {
        $option = $this->schema["resource_slug"] . "_db_version";
        if ( get_option($option) !== $this->db_version ) {
            $this->create_db_table();
            $this->create_meta_table();
        }
    }

    private function init()
    {
        add_action("after_switch_theme", array($this, "activate"));
        if ($this->schema["create_pages"]) {
            add_action("admin_menu", array($this, "create_pages"));
        }
    }

    private function create_db_table()
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->schema["table"];
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            ID BIGINT(20) AUTO_INCREMENT NOT NULL,\n";
        foreach ($this->schema["fields"] as $field_name => $field_schema) {
            $data_type = $this->map_data_type_to_sql($field_schema["type"]);
            $statement = $field_name . " " . $data_type;
            // Whether or not the value can be NULL in the database
            $allow_null = isset($field_schema["null"]) &&
                gettype($field_schema["null"]) == "boolean" ? 
                $field_schema["null"] : true;
            // Add the not null keyword to the sql statement
            if (!$allow_null) {
                $statement .= " NOT NULL";
            }
            // Whether or not the value must be unique
            $is_unique = isset($field_schema["unique"]) &&
                gettype($field_schema["unique"]) == "boolean" ?
                $field_schema["unique"] : false;
            // Add the unique keyword to the sql statement
            if ($is_unique) {
                $statement .= " UNIQUE";
            }
            $has_default = isset($field_schema["default"]);
            if ($has_default) {
                $default_value = strval($field_schema["default"]);
                $statement .= " DEFAULT $default_value";
            }
            $statement .= ",\n";
            $sql .= $statement;
        }
        $sql .= "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modified_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (ID)
        ) $charset_collate;";
        // execute the query
        dbDelta($sql);
    }

    private function create_meta_table()
    {
        global $wpdb;
        $parent_table = $wpdb->prefix . $this->schema["table"];
        $parent_id = $this->schema["resource_slug"] . "_id";
        $table = $wpdb->prefix . $this->schema["table"] . "meta";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table(
            meta_id BIGINT(20) NOT NULL AUTO_INCREMENT,
            $parent_id BIGINT(20) NOT NULL,
            meta_key VARCHAR(255) NOT NULL UNIQUE,
            meta_value LONGTEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modified_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (meta_id),
            FOREIGN KEY ($parent_id) REFERENCES $parent_table(ID) ON DELETE CASCADE 
        ) $charset_collate;";
        // execute the query
        dbDelta($sql);
    }

    private function map_data_type_to_sql($data_type)
    {
        switch ($data_type) {
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

    public function create_pages()
    {
        add_menu_page(
            $this->schema["resource"] . "s",
            $this->schema["resource"] . "s",
            $this->schema["capability"],
            $this->slug,
            array($this, 'create_main_page'),
            $this->schema["dashicon"],
            $this->schema["position"]
        );
        // Create the sub pages
        $create_page = new Page_Creator($this);
        add_submenu_page(
            $this->slug,
            "Add " . $this->schema["resource"],
            "Add " . $this->schema["resource"],
            $this->schema["capability"],
            "add-" . $this->schema["resource_slug"],
            array( $create_page, "render_add_page" )
        );
    }

    public function create_main_page()
    {
        $columns = $this->custom_arr_map( $this->schema["fields"] );
        $table = new Data_Table($columns, $this->schema["table"]);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?= $this->schema["resource"] . "s" ?></h1>
            <a href="<?php menu_page_url("add-" . $this->schema["resource_slug"]); ?>" class="page-title-action">Add New</a>
            <?php $table->display(); ?>
        </div>
        <?php
    }

    private function custom_arr_map(array $array)
    {
        $new_arr = array(
            "ID" => "id"
        );
        foreach ($array as $key => $value) {
            if (isset($value["name"])) {
                $new_arr[$key] = $value["name"];
            } else {
                $new_arr[$key] = $key;
            }
        }
        $new_arr["created_at"] = "Created At";
        $new_arr["modified_at"] = "Last Update";
        
        return $new_arr;
    }

}

?>