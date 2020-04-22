<?php

class Data_Table extends WP_List_Table
{
    private $db_columns;
    private $columns;
    private $table_suffix;
    private $edit_page;
    private $edit_action;
    public function __construct(array $columns, string $table_suffix, string $edit_page = null, string $edit_action = "edit")
    {
        parent::__construct();
        $this->db_columns = array_keys($columns);
        $this->columns = array("cb" => "<input type=\"checkbox\"/>") + $columns;
        $this->table_suffix = $table_suffix;
        $this->edit_page = !empty($edit_page) ? $edit_page : $table_suffix;
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
            'delete' => 'Delete'
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
        $table = $wpdb->prefix . $this->table_suffix;
        $whereClause = "";
        foreach ($ids as $id) {
            $whereClause .= $wpdb->prepare("ID = %s OR ", $id);
        }
        $whereClause = rtrim($whereClause, " OR ");
        $sql = "
            DELETE
            FROM $table
            WHERE $whereClause;
        ";
        $wpdb->query($sql);
    }

    public function tableData(int $limit = 10, int $offset = 0): array
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_suffix;
        $sortData = $this->getSortData();
        $sql = $wpdb->prepare("
                SELECT *
                FROM $table
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
        $table = $wpdb->prefix . $this->table_suffix;
        return $wpdb->get_var("SELECT COUNT(*) FROM $table");
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
}

?>