<?php

/**
 * Capabilities:
 * 
 * activate_plugins
 * delete_others_pages
 * delete_others_posts
 * delete_pages
 * delete_posts
 * delete_private_pages
 * delete_private_posts
 * delete_published_pages
 * delete_published_posts
 * edit_dashboard
 * edit_others_pages
 * edit_others_posts
 * edit_pages
 * edit_posts
 * edit_private_pages
 * edit_private_posts
 * edit_published_pages
 * edit_published_posts
 * edit_theme_options
 * export
 * import
 * list_users
 * manage_categories
 * manage_links
 * manage_options
 * moderate_comments
 * promote_users
 * publish_pages
 * publish_posts
 * read_private_pages
 * read_private_posts
 * read
 * remove_users
 * switch_themes
 * upload_files
 * customize
 * delete_site
 * 
 */

/**
 * required parameters
 * args = array(
 *  'resource' => 'string',
 *  'resource_slug' => 'string,
 *  'parent' => 'string',
 *  'type' => 'string',
 *  'schema' => 'array',
 * )
 */

class Page_Creator
{

    public $success = NULL;
    public $error = NULL;
    public $error_message = "";
    public $parent;
    public $schema;

    public function __construct(Data_Creator $creator, string $capability = "edit_others_pages", int $position = NULL)
    {
        $this->parent = $creator;
        $this->schema = $creator->schema["fields"];
    }

    public function get_page_name(string $type = "add")
    {
        return ucfirst( $type ) . " a " . $this->parent->schema["resource"];
    }

    public function add_data(array $data)
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->parent->schema["resource_slug"] . "s";
        $inserted = $wpdb->insert($table, $data);
        if (false !== $inserted) {
            $this->success = true;
        } else {
            $this->error = true;
            $this->error_message = '' !== $wpdb->last_error ?
                $wpdb->last_error :
                "The " . strtolower($this->parent->schema["resource"]) . " could not be uploaded.";
        }
    }

    public function set_data(array $submitted): array
    {
        $data = array();
        foreach ($this->schema as $field => $args) {
            if (isset( $submitted[$field] )) {
                $data[$field] = $submitted[$field];
            }
        }
        return $data;
    }

    public function render_add_page()
    {
        // Get the current method
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                //...
            break;
            case "POST":
                if (method_exists($this, 'add_data')) {
                    $this->add_data( $this->set_data($_POST) );
                }
            break;
            default:
                //...
        }
        include(__DIR__ . DIRECTORY_SEPARATOR . "add_page.php");
    }

    private function create_input_element(string $field, array $args)
    {
        $name = isset($args["name"]) ?
            $args["name"] :
            $field;
        $id = "Add" . $this->schema["resource"];
        $id .= ucfirst(str_replace(" ", "", $field));
        echo("<tr><th>");
        ?>
        <label for="<?= $id ?>"><?= $name ?></label>
        <?php
        echo("</th><td>");
        switch ($args["type"]) {
            case "string":
                ?>
                <input type="text" class="regular-text" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case "int":
                ?>
                <input type="number" class="regular-text" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case "integer":
                ?>
                <input type="number" class="regular-text" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case "text":
                ?>
                <textarea class="regular-text" name="<?= $field ?>" id="<?= $id ?>"></textarea>
                <?php
                break;
            case "date":
                ?>
                <input type="date" class="regular-text" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case "datetime":
                ?>
                <input type="datetime-local" class="regular-text" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case "bool":
                ?>
                <input type="checkbox" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case "boolean":
                ?>
                <input type="checkbox" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
            case 'media':
                ?>
                <input type="hidden" name="<?= $field ?>" id="<?= $id ?>">
                <button id="<?= $id ?>Upload" data-input="<?= $id ?>" class="button wp_image_dialog">Upload</button>
                <?php
                break;
            case 'currency':
                ?>
                <input type="text" name="<?= $field ?>" id="<?= $id ?>" class="small-text">
                <?php
                break;
            default:
                ?>
                <input type="text" class="regular-text" name="<?= $field ?>" id="<?= $id ?>">
                <?php
                break;
        }
        echo("</td></tr>");
    }

}

?>