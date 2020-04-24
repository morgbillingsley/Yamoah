<?php

namespace Yamoah;

use \Yamoah\Util\Admin_Page;
use \Yamoah\Exception\Data_Request_Exception;

/**
 * Class: Admin_View_Page
 */
class Admin_View_Page extends Admin_Page implements Util\Admin_Page_Interface
{
    public function handle_post()
    {
        if ( isset( $_GET["id"] ) ) {
            // Store the id in a variable
            $id = intval( $_GET["id"] );
            // Get the data with the corresponding id
            $data = $this->table->find_one( $id );
        } else {
            // Send an error saying that an ID was not provided
            throw new Data_Request_Exception("An id was not supplied to this endpoint to access the data.");
        }
    }

    public function display()
    {
        if ( isset( $_GET["id"] ) ) {
            // Store the id in a variable
            $id = intval( $_GET["id"] );
            // Get the data with the corresponding id
            $data = $this->table->find_one( $id );
        } else {
            // Send an error saying that an ID was not provided
            throw new Data_Request_Exception("An id was not supplied to this endpoint to access the data.");
        }
        echo '<div class="wrap">';
        // Page title
        echo '<h1>Edit ' . rtrim( $this->menu->formatted_name, "s" ) . '</h1>';

        echo '</div>';
    }
}

?>