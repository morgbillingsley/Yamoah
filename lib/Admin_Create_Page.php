<?php

namespace Yamoah;

use \Yamoah\Util\String_Modifier;
use \Yamoah\Util\Asset;
use \Yamoah\Util\Admin_Page;
use \Yamoah\Util\Admin_Page_Interface;
use \Yamoah\Exception\Data_Query_Exception;

/**
 * Class: Admin_Create_Page
 */
class Admin_Create_Page extends Admin_Page implements Admin_Page_Interface
{
    public function handle_post()
    {
        try {
            // Add the data to the database
            $added = $this->table->add($_POST);
            // Set the alert to success message
            $this->set_alert( "The {$this->menu->name} was successfully added", false );
        } catch (Data_Query_Exception $error) {
            // Set the error to true
            $this->set_alert( $error->getMessage, true );
        }
    }

    /**
     * renders the form for users to create data using the admin dashboard UI
     */
    public function display()
    {
        // Bring in the wordpress media scripts
        wp_enqueue_media();
        // Add the wordpress media javascript file to the page
        Asset::include( __DIR__ . "/assets/wp_media.js" );
        // Page wrapper
        echo '<div class="wrap">';
        // Page heading
        echo '<h1>Create ' . rtrim( $this->menu->formatted_name, "s" ) . '</h1>';
        // Render alert
        echo $this->get_alert();
        // Open form
        echo '<form action="" method="POST"><table class="form-table" role="presentation"><tbody>';

        // Render each input
        foreach ($this->menu->schema as $name => $args) {
            
            Admin_Page::display_input($name, $args);

        }

        // Submit button
        echo '<tr><th><button type="submit" class="button">Create</button></th></tr>';
        // Close form
        echo '</tbody></table></form>';
        // Close page wrapper
        echo '<div>';
    }
}

?>