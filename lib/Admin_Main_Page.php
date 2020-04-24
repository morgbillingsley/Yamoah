<?php

namespace Yamoah;

use \Yamoah\Util\Admin_Page;
use \Yamoah\Util\Admin_Data_Table;

/**
 * Class: Admin_Main_Page
 */
class Admin_Main_Page extends Admin_Page implements Util\Admin_Page_Interface 
{
    public function handle_post()
    {
        // Do Nothing
    }

    public function display()
    {
        // Convert schema to data columns
        $columns = Admin_Data_Table::schema_to_columns($this->menu->schema);
        // Create the admin data table
        $data_table = new Admin_Data_Table($columns, $this->table->table, $this->menu->edit_slug);
        // echo the page
        ?>

        <div class="wrap">
            <h1 class="wp-heading-inline"> <?= $this->menu->formatted_name ?> </h1>
            <a
                href="<?php menu_page_url($this->menu->create_slug); ?>"
                class="page-title-action"
            >
                Add New
            </a>
            <?php

            // Display the admin table
            $data_table->display();

            ?>
        </div>
        
        <?php
    }
}

?>