<?php

namespace Yamoah\Util;

include ABSPATH . "/wp-admin/includes/class-wp-list-table.php";

/**
 * Class: Admin_Data_Table
 * Description: Creates a visual datatable using WordPress's WP_List_Table in the admin dashboard
 */
class Admin_Data_Table extends \WP_List_Table
{
    private $db_columns;
    private $columns;
    private $table;
    private $edit_page;
    private $edit_action;
    public function __construct(array $columns, string $table, string $edit_page, string $edit_action = "edit")
    {
        parent::__construct();
        $this->db_columns = array_keys($columns);
        $this->columns = array("cb" => "<input type=\"checkbox\"/>") + $columns;
        $this->table = $table;
        $this->edit_page = $edit_page;
        $this->edit_action = $edit_action;
        $this->prepare_items();
    }

    public function prepare_items()
    {
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $offset = ($currentPage * $perPage) - $perPage;
        $this->set_pagination_args(array(
            'total_items' => $this->getTotalCount(),
            'per_page' => $perPage
        ));
        if ($this->current_action() == "delete") {
            $ids = gettype($_GET["id"]) == "array" ? $_GET["id"] : array($_GET["id"]);
            $this->deleteData($ids);
        }
        $data = $this->tableData($perPage, $offset);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    public function get_columns()
    {
        return $this->columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array();
        foreach ($this->db_columns as $column) {
            $sortable_columns[$column] = array($column, true);
        }
        return $sortable_columns;
    }

    public function column_default($item, $column_name)
    {
        return substr($item[$column_name], 0, 250);
    }

    public function get_bulk_actions()
    {
        $actions = array(
            "delete" => "Delete"
        );
        return $actions;
    }

    public function column_ID($item)
    {
        $actions = array(
            "edit" => sprintf(
                '<a href="?page=%s&action=%s&id=%s">Edit</a>',
                $this->edit_page,
                $this->edit_action,
                $item["ID"]
            ),
            "delete" => sprintf(
                '<a class="submitdelete" href="?page=%s&action=%s&id=%s">Trash</a>',
                $_GET["page"],
                "delete",
                $item["ID"]
            )
        );
        return sprintf(
            '%1$s %2$s',
            $item["ID"],
            $this->row_actions($actions)
        );
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }

    public function deleteData(array $ids) {
        global $wpdb;
        $whereClause = "";
        foreach ($ids as $id) {
            $whereClause .= $wpdb->prepare("ID = %s OR ", $id);
        }
        $whereClause = rtrim($whereClause, " OR ");
        $sql = "
            DELETE
            FROM {$this->table}
            WHERE $whereClause;
        ";
        $wpdb->query($sql);
    }

    public function tableData(int $limit = 10, int $offset = 0): array
    {
        global $wpdb;
        $sortData = $this->getSortData();
        $sql = $wpdb->prepare("
                SELECT *
                FROM {$this->table}
                ORDER BY {$sortData['orderby']} {$sortData['order']}
                LIMIT %d
                OFFSET %d;
            ", array($limit, $offset)
        );
        $data = $wpdb->get_results($sql, ARRAY_A);
        return $data;
    }

    public function getTotalCount(): int
    {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }

    public function getSortData(): array
    {
        // Check if query parameters are set
        if (isset($_GET["orderby"]) &&
            !empty($_GET["orderby"]) &&
            isset($_GET["order"]) &&
            !empty($_GET["order"]))
        {
            // Set from query parameters
            $sortData = array(
                "orderby" => $_GET["orderby"],
                "order" => strtoupper($_GET["order"])
            );
        } else {
            // Set default
            $sortData = array(
                "orderby" => "ID",
                "order" => "DESC"
            );
        }
        return $sortData;
    }

    /**
     * @param array schema for the database
     * @return array columns to pass to Admin_Data_Table constructor
     */
    public static function schema_to_columns(array $schema): array
    {
        $columns = array(
            "ID" => "id"
        );
        foreach ($schema as $key => $value) {
            if (isset($value["name"])) {
                $columns[$key] = $value["name"];
            } else {
                $columns[$key] = $key;
            }
        }
        $columns["created_at"] = "Created At";
        $columns["modified_at"] = "Last Update";
        
        return $columns;
    }

    function display()
    {
        echo '<form id="events-filter" method="get">';

        echo '<input type="hidden" name="page" value="' . $_REQUEST["page"] . '" />';

        parent::display();

        echo '</form>';
    }
}