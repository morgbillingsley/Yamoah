<?php

namespace Yamoah;

use \Yamoah\Util\String_Modifier;

/**
 * Class: Admin_Menu
 * Description: Creates the pages to be displayed in the WordPress Admin UI
 */
class Admin_Menu
{
    /** @var Table_Resource resource object representing the table */
    public $table;

    /** @var array schema for the fields of the data table */
    public $schema;

    /** @var string name of the resource */
    public $name;

    /** @var string formatted name of the resource */
    public $formatted_name;

    /** @var string slug used to create the admin pages */
    public $slug;

    /** @var string capability level that users must have to access the admin menu from the WordPress admin dashboard */
    public $capability = "edit_others_posts";

    /** @var int position of the menu link in the left sidebar of the WordPress admin dashboard */
    public $position = NULL;

    /** @var string icon to be displayed in the left sidebar for the menu link */
    public $icon = "dashicons-screenoptions";

    /** @var string slug for the create page */
    public $create_slug;

    /** @var string slug for the edit page */
    public $edit_slug;

    /** @var string slug for the view page */
    public $view_slug;

    /** @var Admin_Create_Page object for the admin ui page where users can add data */
    public $create_page;

    /** @var Admin_Edit_Page object for the admin ui page where users can edit existing data */
    public $edit_page;

    /** @var Admin_View_Page object for the admin ui page where users can view existing data */
    public $view_page;

    /**
     * @param string name of the resource
     * @param array schema for the resource
     */
    public function __construct(Table_Resource $table, array $schema, string $capability = NULL, int $position = NULL, string $icon = NULL)
    {
        // Set the table as the submitted Table_Resource object
        $this->table = $table;
        // Set the schema for the table resource
        $this->schema = $schema;
        // Set the name
        $this->name = $this->table->name;
        // Set the formatted name
        $this->formatted_name = String_Modifier::title($this->name);
        // Set the slug
        $this->slug = String_Modifier::kebab("yamoah/" . $this->name);
        // Set the capability
        if ( $capability !== NULL ) $this->capability = $capability;
        // Set the position
        if ( $position !== NULL ) $this->position = $position;
        // Set the Icon
        if ( $icon !== NULL ) $this->icon = $icon;
        // Get the singular string of the resource
        $singular = rtrim( $this->name, "s" );
        // Create the create page slug
        $this->create_slug = String_Modifier::snake("create_" . $singular);
        // Create the edit page slug
        $this->edit_slug = String_Modifier::snake("edit_" . $singular);
        // Create the view page slug
        $this->view_slug = String_Modifier::snake("view_" . $singular);
    }

    /**
     * @return self returns this instance of the Admin_Menu object
     */
    public function create()
    {
        // Create the Admin_Main_Page object
        $this->main_page = new Admin_Main_Page($this);
        // Create the main menu page
        add_menu_page(
            $this->formatted_name,
            $this->formatted_name,
            $this->capability,
            $this->slug,
            array( $this->main_page, "build" ),
            $this->icon,
            $this->position
        );
        // Create the Admin_Create_Page object
        $this->create_page = new Admin_Create_Page($this);
        // Create the create page
        add_submenu_page(
            $this->slug,
            "Create " . $this->formatted_name,
            "Create " . $this->formatted_name,
            $this->capability,
            $this->create_slug,
            array( $this->create_page, "build" )
        );
        // Create the Admin_Edit_Page object
        $this->edit_page = new Admin_Edit_Page($this);
        // Create the edit page
        add_submenu_page(
            NULL,
            "Edit " . $this->formatted_name,
            "Edit " . $this->formatted_name,
            $this->capability,
            $this->edit_slug,
            array( $this->edit_page, "build" )
        );
        // Create the Admin_View_Page object
        $this->view_page = new Admin_View_Page($this);
        // Create the view page
        add_submenu_page(
            NULL,
            "View " . $this->formatted_name,
            "View " . $this->formatted_name,
            $this->capability,
            $this->view_slug,
            array( $this->view_page, "build" )
        );
    }
}