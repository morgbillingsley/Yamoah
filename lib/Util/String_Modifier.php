<?php

namespace Yamoah\Util;

/**
 * Class: String_Modifier
 */
class String_Modifier
{
    /**
     * @param string string to be formatted
     * @return string formatted title string
     */
    public static function title(string $string)
    {
        // Replace "_" character with space character
        $with_spaces = str_replace("_", " ", $string);
        // Replace "-" character with space character
        $with_spaces = str_replace("-", " ", $with_spaces);
        // Split the string by each word
        $words = explode(" ", $with_spaces);
        foreach ($words as $i => $word) {
            // Array of all the words that should remain lowercase
            $lowercase = array( "a", "an", "the", "for", "and", "nor", "but", "or", "yet", "so", "at", "around", "by", "after", "along", "for", "from", "of", "on", "to", "with", "without" );
            // If the word is the first in the sentence or if it is not found in the lowercase words array, then capitalize the word
            if ($i == 0 || ( !in_array($word, $lowercase)  )) {
                $words[$i] = ucfirst($word);
            }
        }
        // Glue the sentence back together and return it
        return implode(" ", $words);
    }

    /**
     * @param string string to be formatted
     * @param string the character to replace all delimeters with
     * @return string the newly formatted string
     */
    public static function replace_delimeter(string $string, string $replacement = " "): string
    {
        // An array of all the delimeters that should be replaced
        $delimeters = array( "_", "-", " ", ",", ".", "\t", "\n", "'" );
        $formatted = $string;
        // Loop through each delimeter, checking replacing every instance of it in the string
        foreach ($delimeters as $delimeter) {
            $formatted = str_replace( $delimeter, $replacement, $formatted );
        }
        // Return the newly formatted string
        return $formatted;
    }

    /**
     * @param string string to be formatted
     * @return string formatted snake case string
     */
    public static function snake(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $snake = self::replace_delimeter($lowercase, "_");

        return $snake;
    }

    /**
     * @param string string to be formatted
     * @return string formatted kebab case string
     */
    public static function kebab(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $kebab = self::replace_delimeter($lowercase, "-");

        return $kebab;
    }

    /**
     * @param string string to be formatted
     * @return string formatted camel case string
     */
    public static function camel(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $with_spaces = self::replace_delimeter( $lowercase );
        // Split the string by each word
        $words = explode(" ", $with_spaces);
        foreach ($words as $i => $word) {
            // If the word is the first in the sentence or if it is not found in the lowercase words array, then capitalize the word
            if ($i !== 0 ) {
                $words[$i] = ucfirst($word);
            }
        }
        // Glue the sentence back together and return it
        return implode("", $words);
    }

    /**
     * @param string string to be formatted
     * @return string formatted pascal case string
     */
    public static function pascal(string $string)
    {
        // Make the string lowercase
        $lowercase = strtolower( $string );
        // Replace all delimeters
        $with_spaces = self::replace_delimeter( $lowercase );
        // Split the string by each word
        $words = explode(" ", $with_spaces);
        foreach ($words as $i => $word) {
            // Capitalize the word
            $words[$i] = ucfirst($word);
        }
        // Glue the sentence back together and return it
        return implode("", $words);
    }

    /**
     * @param string string to remove
     * @param string string to remove from
     * @return string string with text removed
     */
    public static function remove(string $remove, string $host): string
    {
        return str_replace($remove, "", $host);
    }

    /**
     * @param string absolute path of file
     * @return string url of file to access from client
     */
    public static function path_to_url(string $path = ""): string
    {
        // Replace the absolute path of WordPress with the website url in the submitted path
        $url = str_replace(
            wp_normalize_path( untrailingslashit( ABSPATH ) ),
            site_url(),
            wp_normalize_path( $path )
        );
        // return the cleaned url
        return $url;
    }

    /**
     * @param string url to add query parameter to
     * @param string parameter key
     * @param mixed parameter value
     * @return string url with added query parameter
     */
    public static function add_param_to_url(string $url, string $key, string $value): string
    {
        // get the parts of the url
        $parts = parse_url( $url );
        // split up the query string into an array
        parse_str( $parts["query"], $query );
        // set the provided key to the provided value in the query array
        $query[ $key ] = $value;
        // glue the query array back into a string
        $parts["query"] = http_build_query( $query );
        // Build the url
        return self::build_url( $parts );
    }

    /**
     * @param array array of url parts similar to that of the return value of the parse_url function
     * @return string built url
     */
    public static function build_url(array $parts)
    {
        // Add the scheme
        $url = $parts["scheme"] . "://";
        // Check if the url has username and password
        $url .= isset( $parts["user"] ) && !empty( $parts["user"] ) && isset( $parts["pass"] ) && !empty( $parts["pass"] ) ? $parts["user"] . ":" . $parts["pass"] . "@" : "";
        // Add the host name
        $url .= $parts["host"];
        // Add the port
        $url .= isset( $parts["port"] ) && !empty( $parts["port"] ) ? ":" . $parts["port"] : "";
        // Add the path
        $url .= $parts["path"];
        // Add the query
        $url .= isset( $parts["query"] ) && !empty( $parts["query"] ) ? "?" . $parts["query"] : "";
        // Add the anchor
        $url .= isset( $parts["anchor"] ) && !empty( $parts["anchor"] ) ? "#" . $parts["anchor"] : "";
        // Return the built url
        return $url;
    }
}