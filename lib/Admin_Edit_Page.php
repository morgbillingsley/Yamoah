<?php

namespace Yamoah;

use \Yamoah\Util\Admin_Page;
use \Yamoah\Exception\Data_Request_Exception;

/**
 * Class: Admin_Edit_Page
 */
class Admin_Edit_Page extends Admin_Page implements Util\Admin_Page_Interface
{
    public function handle_post()
    {
        if ( isset( $_GET["id"] ) ) {
            // Store the id in a variable
            $id = intval( $_GET["id"] );
            // Get the data with the corresponding id
            $data = $this->table->find_one( $id );
            // Update the data
            $data = $data->update($_POST);
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
        // Bring in the wordpress media scripts
        wp_enqueue_media();
        // Create dummy script tags
        wp_register_script( "yamoah-media-uploader", "" );
        wp_enqueue_script( "yamoah-media-uploader" );
        // Add script
        $script = file_get_contents( __DIR__ . "/assets/wp_media.js" );
        wp_add_inline_script( "yamoah-media-uploader", $script );
        // Page wrapper
        echo '<div class="wrap">';
        // Render alert
        echo $this->get_alert();
        // Page title
        echo '<h1>Edit ' . rtrim( $this->menu->formatted_name, "s" ) . '</h1>';
        // Beginning of the form
        echo '<form action="" method="POST"><table class="form-table" role="presentation"><tbody>';

        // Render each input
        foreach ($this->menu->schema as $name => $args) {

            Admin_Page::display_input($name, $args, $data->$name);

        }

        // Submit button
        echo '<tr><th><button type="submit" class="button">Update</button></th></tr>';
        // Close form
        echo '</tbody></table></form>';
        // Close page wrapper
        echo '</div>';
    }
}

?>