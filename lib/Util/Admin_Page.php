<?php

namespace Yamoah\Util;

use \Yamoah\Admin_Menu;
use \Yamoah\Exception\Data_Request_Exception;

/**
 * Class: Admin_Page
 */
class Admin_Page
{
    /** @var string html element containing an alert message */
    public $alert = '';

    public function __construct(Admin_Menu $menu)
    {
        // Set the menu variable
        $this->menu = $menu;
        // Set the table variable
        $this->table = $this->menu->table;
    }

    public function build()
    {
        try {
            // Check the HTTP method
            $method = $_SERVER["REQUEST_METHOD"];
            if ($method === "POST") {
                $this->handle_post();
            }
            // Render the page
            $this->display();
        } catch (Data_Request_Exception $error) {
            echo '<div class="wrap"><h1>' . $this->menu->formatted_name . '</h1>' . $this->set_alert($error->__toString(), true)->get_alert() . '</div>';
        }
    }

    /**
     * @param string message to display within alert
     * @param bool whether or not the alert is an error
     * @return self
     */
    public function set_alert(string $message, bool $error): self
    {
        if ($error) {
            $this->alert = '<div class="notice notice-error is-dismissible"><p>' . $message . '</p></div>';
        } else {
            $this->alert = '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
        }
        return $this;
    }

    /**
     * @return string the alert html element
     */
    public function get_alert()
    {
        if (empty( $this->alert )) {
            return "";
        }
        return $this->alert;
    }

    /**
     * @param string name of the field
     * @param array|string arguments for the field
     * @param mixed current value of the input, null if none
     */
    public static function display_input(string $name, $args, $value = NULL)
    {
        // Set the label for the input
        $label = isset($args["name"]) ? $args["name"] : $name;
        // Set the id for the input
        $id = String_Modifier::pascal( "create " . $name );
        // Set the type for the input
        $type = isset( $args["type"] ) ? $args["type"]
                : gettype( $args ) == "string" ? $args
                : "string";
        echo("<tr><th>");
        ?>
        <label for="<?= $id ?>"><?= $label ?></label>
        <?php
        echo("</th><td>");
        switch ( $type ) {
            case "string":
                // Render beginning of input
                echo "<input type=\"text\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
            case "int":
                // Render beginning of input
                echo "<input type=\"number\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
            case "integer":
                // Render beginning of input
                echo "<input type=\"number\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
            case "text":
                // Render beginning of input
                echo "<textarea class=\"regular-text\" name=\"$name\" id=\"$id\">";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? $value : "";
                // Render the closing tag
                echo "</textarea>";
                break;
            case "date":
                // Render beginning of input
                echo "<input type=\"date\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
            case "datetime":
                // Render beginning of input
                echo "<input type=\"datetime-local\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
            case "bool":
                // Render beginning of input
                echo "<input type=\"checkbox\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo boolval( $value ) ? " checked >" : ">";
                break;
            case "boolean":
                // Render beginning of input
                echo "<input type=\"checkbox\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo boolval( $value ) ? " checked >" : ">";
                break;
            case 'media':
                // Render beginning of input
                echo "<input type=\"hidden\" name=\"$name\" id=\"$id\"";
                // Check whether or not multiple media can be selected
                $multiple = isset( $args["multiple"] ) && gettype( $args["multiple"] ) == "boolean" ? $args["multiple"] : false;
                // If multiple, render the multiple attribute
                echo $multiple ? " multiple" : "";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                $button_id = $id . "Upload";
                echo "<button id=\"$button_id\" data-input=\"$id\" class=\"button wp_image_dialog\">Upload</button>";
                break;
            case 'currency':
                // Render beginning of input
                echo "<input type=\"text\" class=\"small-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
            default:
                // Render beginning of input
                echo "<input type=\"text\" class=\"regular-text\" name=\"$name\" id=\"$id\"";
                // If value is set, render the value; otherwise, render the closing tag
                echo $value !== NULL ? " value=\"$value\">" : ">";
                break;
        }
        // If there is a description for the field, render it here
        if ( isset( $args["description"] ) ) {
            echo "<br><span class=\"description\">{$args['description']}</span>";
        }
        echo("</td></tr>");
    }
}

?>