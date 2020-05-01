<?php

namespace Yamoah\Util;

class Asset
{
    public static function include(string $asset_path)
    {
        $contents = file_get_contents( $asset_path );
        // Get information about the path
        $path = pathinfo( $asset_path );
        // Asset name
        $name = "yamoah/" . $path["filename"];
        if ($path["extension"] == "js") {
            // Create a dummy script tag
            wp_register_script( $name, "" );
            // Enqueue the script
            wp_enqueue_script( $name );
            // Add the inline script
            wp_add_inline_script( $name, $contents );
        } elseif ($path["extension"] == "css") {
            // Create a dummy style tag
            wp_register_style( $name, "" );
            // Enqueue the style
            wp_enqueue_style( $name );
            // Add the inline style
            wp_add_inline_style( $name, $contents );
        }
    }
}